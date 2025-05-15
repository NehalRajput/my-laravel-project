<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

class ChatServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register chat routes
        Route::middleware('web')
            ->group(base_path('routes/chat.php'));

        // Register broadcast channels with CSRF protection
        Broadcast::routes(['middleware' => ['web', 'auth:admin,user']]);

        // Include channel authorization routes
        require base_path('routes/channels.php');
    }
}