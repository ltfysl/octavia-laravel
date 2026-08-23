<?php

use App\Broadcasting\AuthorizeRunChannel;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('runs.{id}', AuthorizeRunChannel::class);
