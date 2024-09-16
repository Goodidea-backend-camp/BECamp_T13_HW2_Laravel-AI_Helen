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

    Route::post('/threads/{thread}/messages', [ChatMessageController::class, 'store'])
        // 'App\Models\ChatMessage' 指定了目標 Model，適用於不需要 Model 實例的授權操作（例如 create）
        // 官方文件連結：https://laravel.com/docs/master/authorization#middleware-actions-that-dont-require-models
        // 'thread' 是路由中的參數，Laravel 透過隱式綁定將其自動解析為 Thread Model 的實例
        ->middleware('can:create,App\Models\ChatMessage,thread');
});
