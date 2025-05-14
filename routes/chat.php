<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

// Routes accessible by both admins and interns
Route::middleware(['auth:admin,user'])->group(function () {
    Route::get('/chat', [MessageController::class, 'index'])->name('chat');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages', [MessageController::class, 'getMessages'])->name('messages.get');
    Route::post('/messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
});