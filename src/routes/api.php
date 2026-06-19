<?php

use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SubtaskController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'data' => $request->user(),
        ]);
    });

    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('categories', CategoryController::class)->except(['show']);

    Route::prefix('tasks/{task}')->group(function () {
        Route::get('subtasks', [SubtaskController::class, 'index']);
        Route::post('subtasks', [SubtaskController::class, 'store']);
        Route::get('comments', [CommentController::class, 'index']);
        Route::post('comments', [CommentController::class, 'store']);
        Route::get('attachments', [AttachmentController::class, 'index']);
        Route::post('attachments', [AttachmentController::class, 'store']);
    });

    Route::put('subtasks/{subtask}', [SubtaskController::class, 'update']);
    Route::patch('subtasks/{subtask}/toggle', [SubtaskController::class, 'toggle']);
    Route::delete('subtasks/{subtask}', [SubtaskController::class, 'destroy']);
    Route::delete('comments/{comment}', [CommentController::class, 'destroy']);
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy']);

    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);
});
