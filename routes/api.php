<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\ThreadController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/threads', [ThreadController::class, 'store']);
    Route::put('/threads/{thread}', [ThreadController::class, 'update'])->middleware('can:update,thread');
    Route::delete('/threads/{thread}', [ThreadController::class, 'destroy'])->middleware('can:delete,thread');

    Route::post('/threads/{threadId}/messages/text', [ChatMessageController::class, 'store'])
        ->middleware('can:create,App\Models\ChatMessage,threadId');
});
