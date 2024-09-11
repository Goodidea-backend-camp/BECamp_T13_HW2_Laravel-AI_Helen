<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\ThreadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum', 'can:create,App\Models\ChatMessage,threadId'])->post('/threads/{threadId}/messages/text', [ChatMessageController::class, 'store']);

Route::middleware('auth:sanctum')->post('/threads', [ThreadController::class, 'store']);
Route::middleware(['auth:sanctum', 'can:update,thread'])->put('/threads/{thread}', [ThreadController::class, 'update']);
Route::middleware(['auth:sanctum', 'can:delete,thread'])->delete('/threads/{thread}', [ThreadController::class, 'destroy']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
