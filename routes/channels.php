<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Admin;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

Broadcast::channel('chat.{id}', function ($user, $id) {
    // Check if the authenticated user is either an admin or an intern
    if (auth()->guard('admin')->check()) {
        return true; // Admins can access all channels
    }

    // For interns, check if they are the intended recipient
    return (int) $user->id === (int) $id;
}); 