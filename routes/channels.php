<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('private-App.Models.User.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
Broadcast::channel('products', function ($user) {
    return true; // Atau Anda bisa menambahkan logika otentikasi di sini jika diperlukan
});
