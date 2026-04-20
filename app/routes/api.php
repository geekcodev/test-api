<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\OrderController;

Route::prefix('v1')->group(function () {
    // Product Routes
    Route::get('/products', [ProductController::class, 'index']);

    // Order Routes
    Route::middleware(['throttle:order-create-limiter'])->group(function () {
        Route::post('/orders', [OrderController::class, 'store']);
    });
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);
});
