<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\AI\Assistant;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $attributes = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string',
                'self_introduction' => 'required|string',
                'is_pro' => 'required|boolean',
            ]);
        } catch (ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $field => $messages) {
                $errors[$field] = $messages[0];
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $errors,
            ], 422);
        }

        // 本地註冊 provider ＝ 1
        $attributes['provider'] = 1;

        // 驗證資料庫中是否已存在此 email，若已存在，顯示錯誤訊息
        if (User::firstWhere('email', $request->email)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The email has already been taken.'
            ], 400);
        }

        // 檢查使用者名稱是否違反善良風俗，若違反，顯示錯誤訊息
        $assistant = new Assistant();
        if (!$assistant->isNameAppropriate($attributes['name'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => [
                    'name' => 'The name violates public morals. Please change.',
                ],
            ], 422);
        }

        // 依照自我介紹生成大頭貼（pending）
        $attributes['avatar_file_path'] = 'pending';

        // 將註冊資訊存入
        User::create($attributes);

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully. Please check your email for verification.'
        ]);
    }

    public function login(Request $request)
    {
        try {
            // 驗證收到的 request
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $field => $messages) {
                $errors[$field] = $messages[0];
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $errors,
            ], 422);
        }

        // 嘗試使用收到的帳號密碼登入
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 400);
        }

        // 取得已認證的使用者
        $user = User::firstWhere('email', $request->email);

        // 簽發 token
        $token = $user->createToken(
            'API token for ' . $user->email,
            ['*'],
            now()->addMonth() // expiration 為一個月
        )->plainTextToken; // 取 token 中的 plainTextToken


        // 回傳使用者資料和 token，狀態碼省略，因為預設為200，json() 第一個參數是 $data ，第二個參數是 http status code (default = 200)
        return response()->json([
            'status' => 'success',
            'message' => 'Authenticated',
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        // 回應 204 No Content
        return response()->noContent();
    }
}
