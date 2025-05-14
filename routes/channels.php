<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Admin;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('chat.{id}', function ($user, $id) {
    // Allow users to listen to their own channel
    if (auth()->guard('admin')->check()) {
        return auth()->guard('admin')->id() == $id;
    }
    return auth()->id() == $id;
}); 