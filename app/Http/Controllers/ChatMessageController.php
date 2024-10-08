<?php

namespace App\Http\Controllers;

use App\AI\Assistant;
use App\Models\ChatMessage;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ChatMessageController extends Controller
{
    public function store(Request $request, Thread $thread)
    {
        // 取得當前使用者發送的訊息，放在資料庫操作之前，避免若 validate() 未通過，需要 return 卻無法 rollback 資料
        $request->validate([
            'content' => 'required|string|max:3000',
        ]);

        // 錯誤處理：免費的使用者同時最多10個 chat message
        $user = auth()->user();

        DB::beginTransaction();

        try {
            if (! $user->is_pro) {
                $chatMessageCount = ChatMessage::where('thread_id', $thread->id)->where('role', ChatMessage::ROLE_USER)->lockForUpdate()->count();

                if ($chatMessageCount >= 10) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Non-pro users can only have up to 10 message. Upgrade to pro account to create more messages.',
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            $currentChatMessageByUser = new ChatMessage();
            $currentChatMessageByUser->role = ChatMessage::ROLE_USER;
            $currentChatMessageByUser->content = $request['content'];
            $currentChatMessageByUser->thread_id = $thread->id;
            $currentChatMessageByUser->save();

            // 取得所有歷史訊息紀錄+當前使用者發送的訊息(request 訊息加總設置為 19 則，以預防 request token 超限)
            $record = [];
            $recordInDatabase = ChatMessage::where('thread_id', $thread->id)
                ->orderBy('id', 'desc')
                ->take(19)
                ->select('id', 'role', 'content')
                ->get()
                ->sortBy('id'); // 使用 PHP 進行排序，不依賴資料庫

            foreach ($recordInDatabase as $item) {
                $role = $item['role'] == ChatMessage::ROLE_USER ? 'user' : ($item['role'] == ChatMessage::ROLE_ASSISTANT ? 'assistant' : null);
                $record[] = [
                    'role' => $role,
                    'content' => $item['content'],
                ];
            }

            // 發送請求至 OpenAI Client，取得 AI 回覆訊息
            $assistant = new Assistant();
            $response = $assistant->sendChatMessage($record);

            // 將當前訊息及當前 AI 回覆訊息儲存至資料庫
            $currentChatMessageByAI = new ChatMessage();
            $currentChatMessageByAI->role = ChatMessage::ROLE_ASSISTANT;
            $currentChatMessageByAI->content = $response;
            $currentChatMessageByAI->thread_id = $thread->id;
            $currentChatMessageByAI->save();

            DB::commit();
        } catch (\Throwable $throwable) {

            DB::rollBack();

            throw $throwable;
        }

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
