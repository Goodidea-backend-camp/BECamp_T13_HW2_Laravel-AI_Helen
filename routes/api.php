<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ThreadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// 使用 php artisan route:list | grep threads 命令可以看到對應路由清單
Route::middleware('auth:sanctum')->apiResource('threads', ThreadController::class);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
