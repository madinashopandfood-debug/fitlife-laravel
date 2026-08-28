<?php

use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

// Rate limited to protect against form-spam/bot abuse.
Route::middleware('throttle:20,1')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
});

Route::get('/pixel-config', [OrderController::class, 'pixelConfig']);
