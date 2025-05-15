<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;


/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

// Include admin and intern routes
require __DIR__.'/admin.php';
require __DIR__.'/intern.php';
require __DIR__.'/chat.php';  // Include chat routes

Route::middleware(['auth'])->group(function () {
    // ... existing routes ...
    
    // Role management routes
    Route::resource('roles', RoleController::class);
});

