<?php

use Illuminate\Support\Facades\Route;
use App\Events\TestEvent;
use App\Http\Controllers\ProductController;

Route::get('/test-event', function () {
    event(new TestEvent());
    return 'Event dispatched!';
});

// Route::resource('products', ProductController::class);

Route::get('/products', [ProductController::class, 'showProducts']);


Route::get('/', function () {
    return view('welcome');
});
