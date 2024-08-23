<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
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
