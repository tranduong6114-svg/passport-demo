<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

Route::get('/public/info', function () {
    return response()->json([
        'message' => 'Đây là API công khai, không cần token',
        'version' => '1.0',
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login',    [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {

    Route::get('/auth/me',       [AuthController::class, 'me']);
    Route::post('/auth/logout',  [AuthController::class, 'logout']);

    Route::get('/tasks',         [TaskController::class, 'index']);
    Route::get('/tasks/{id}',    [TaskController::class, 'show']);

    Route::middleware('scope:write-tasks')->group(function () {
        Route::post('/tasks',       [TaskController::class, 'store']);
        Route::put('/tasks/{id}',   [TaskController::class, 'update']);
        Route::delete('/tasks/{id}',[TaskController::class, 'destroy']);
    });

    Route::middleware('scope:admin')->prefix('admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
    });
});