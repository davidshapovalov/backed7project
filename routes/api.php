<?php

use App\Http\Controllers\NoteController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // ОБЯЗАТЕЛЬНЫЕ ФУНКЦИИ
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
    });
});


Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('notes', NoteController::class);

    Route::get('notes/stats/status', [NoteController::class, 'statsByStatus']);
    Route::patch('notes/actions/archive-old-drafts', [NoteController::class, 'archiveOldDrafts']);
    Route::get('users/{user}/notes', [NoteController::class, 'userNotesWithCategories']);
    Route::get('notes-actions/search', [NoteController::class, 'search']);

    Route::post('/notes/{id}/pin', [NoteController::class, 'pinNote']);
    Route::post('/notes/{id}/unpin', [NoteController::class, 'unpinNote']);
    Route::post('/notes/{id}/publish', [NoteController::class, 'publish']);
    Route::post('/notes/{id}/archive', [NoteController::class, 'archive']);


    Route::get('notes/{note}/tasks', [TaskController::class, 'index']);
    Route::post('notes/{note}/tasks', [TaskController::class, 'store']);
    Route::get('notes/{note}/tasks/{task}', [TaskController::class, 'show']);
    Route::put('notes/{note}/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('notes/{note}/tasks/{task}', [TaskController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

    Route::middleware('admin')->group(function () {
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    });
});
