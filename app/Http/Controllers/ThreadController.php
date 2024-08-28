<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ThreadController extends Controller
{
    public function store(Request $request)
    {
        // 錯誤處理：免費的使用者同時最多三個 thread
        $user = auth()->user();

        if (!$user->is_pro) {
            $threadCount = Thread::where('user_id', $user->id)->count();

            if ($threadCount >= 3) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Non-pro users can only have up to 3 threads. Upgrade to pro account to create more threads.'
                ], 429);
            }
        }

        try {
            $attributes = $request->validate([
                'type' => 'required|integer|min:1|max:2',
                'title' => 'required|string'
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

        $thread = Thread::create([
            'type' => $attributes['type'],
            'title' => $attributes['title'],
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => $thread->type,
                'title' => $thread->title,
            ]
        ]);
    }

    public function update(Thread $thread)
    {
        request()->validate([
            'title' => 'required|string'
        ]);

        $thread->update([
            'title' => request('title'),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => $thread->type,
                'title' => $thread->title,
            ]
        ]);
    }

    public function destroy(Thread $thread)
    {
        $thread->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Thread deleted successfully.',
        ]);
    }
}
