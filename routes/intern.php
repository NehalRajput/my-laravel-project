<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InternTaskController;
use App\Http\Controllers\CommentController;

// Guest Intern Routes
Route::middleware(['guest:user'])->name('intern.')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'showLogin')->name('login');
        Route::post('/login', 'login');
        Route::get('/register', 'showRegister')->name('register');
        Route::post('/register', 'register');
    });
});

// Authenticated Intern Routes
Route::middleware(['auth:user'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    
    Route::prefix('intern')->name('intern.')->group(function () {
        // Tasks
        Route::controller(InternTaskController::class)->prefix('tasks')->name('tasks.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{task}', 'show')->name('show');
            Route::patch('/{task}/status', 'updateStatus')->name('update-status');
            Route::post('/{task}/comment', 'addComment')->name('comment');
        });

        // Comments
        Route::post('/comments/tasks/{task}', [CommentController::class, 'store'])->name('comments.store');
    });

    // Authentication
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
