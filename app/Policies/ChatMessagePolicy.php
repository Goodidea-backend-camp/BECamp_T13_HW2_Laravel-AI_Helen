<?php

namespace App\Policies;

use App\Models\Thread;
use App\Models\User;

class ChatMessagePolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, $threadId): bool
    {
        $thread = Thread::where('id', $threadId)->first();

        return $thread && $thread->user_id === $user->id;
    }
}
