<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ThreadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|integer|min:1|max:2',
            'title' => 'required|string',
        ]);

        // 錯誤處理：免費的使用者同時最多三個 thread
        $user = auth()->user();

        DB::beginTransaction();

        try {

            if (! $user->is_pro) {
                $threadCount = Thread::where('user_id', $user->id)->whereNull('deleted_at')->lockForUpdate()->count();

                if ($threadCount >= 3) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Non-pro users can only have up to 3 threads. Upgrade to pro account to create more threads.',
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            $thread = new Thread();
            $thread->type = $request['type'];
            $thread->title = $request['title'];
            $thread->user_id = auth()->id();
            $thread->save();

            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => $thread->type,
                'title' => $thread->title,
            ],
        ]);
    }

    public function update(Thread $thread)
    {
        request()->validate([
            'title' => 'required|string',
        ]);

        $thread->update([
            'title' => request('title'),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => $thread->type,
                'title' => $thread->title,
            ],
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
