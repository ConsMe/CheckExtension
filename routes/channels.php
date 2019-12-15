<?php

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

Broadcast::channel('followTheCheckers', function ($user) {
    $role = $user->role;
    if ($role === 'superadmin' || ($role === 'admin' && $user->has_access_to_checkers)) {
        return ['id' => null];
    }
    if ($role === 'checker') {
        return ['id' => $user->id];
    }
    return false;
});
Broadcast::channel('checker.{id}', function ($user, $id) {
    return (string) $user->id === $id;
});
