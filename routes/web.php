<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\SubscriberController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('subscribers', [SubscriberController::class, 'store'])->middleware('throttle:100000,1');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
