<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('beds', function (User $user) {
    return $user->hasRole(['Admin', 'Super Admin']);
});

Broadcast::channel('dashboard', function (User $user) {
    return $user->hasRole(['Admin', 'Super Admin']);
});
