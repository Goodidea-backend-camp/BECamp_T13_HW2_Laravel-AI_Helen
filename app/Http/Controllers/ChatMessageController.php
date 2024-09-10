<?php

namespace App\Http\Controllers;

use App\AI\Assistant;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    public function store(Request $request, $threadId)
    {
        // 錯誤處理：免費的使用者同時最多10個 chat message
        $user = auth()->user();

        if (! $user->is_pro) {
            $chatMessageCount = ChatMessage::where('thread_id', $threadId)->where('role', 1)->count();

            if ($chatMessageCount >= 10) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Non-pro users can only have up to 10 message. Upgrade to pro account to create more messages.',
                ], 429);
            }
        }

        // 取得當前使用者發送的訊息（從 request 中取得），驗證並儲存至資料庫
        $request->validate([
            'content' => 'required|string',
        ]);

        $currentChatMessageByUser = ChatMessage::create([
            'role' => 1,
            'content' => $request['content'],
            'thread_id' => $threadId,
        ]);

        // 取得所有歷史訊息紀錄+當前使用者發送的訊息
        $recordInDatabase = ChatMessage::where('thread_id', $threadId)->get();
        $record = [];

        foreach ($recordInDatabase as $item) {
            $role = $item['role'] == 1 ? 'user' : ($item['role'] == 2 ? 'assistant' : null);
            $record[] = [
                'role' => $role,
                'content' => $item['content'],
            ];
        }

        // 發送請求至 OpenAI Client，取得 AI 回覆訊息
        $assistant = new Assistant();
        $response = $assistant->sendChatMessage($record);

        // 將當前訊息及當前 AI 回覆訊息儲存至資料庫
        $currentChatMessageByAI = ChatMessage::create([
            'role' => 2,
            'content' => $response,
            'thread_id' => $threadId,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                [
                    'role' => $currentChatMessageByUser->role,
                    'content' => $currentChatMessageByUser->content,
                ],
                [
                    'role' => $currentChatMessageByAI->role,
                    'content' => $currentChatMessageByAI->content,

                ],
            ],
        ]);
    }
}
