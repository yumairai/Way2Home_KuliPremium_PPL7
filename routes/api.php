<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\MaterialController;
use App\Http\Controllers\Customer\PaymentController;

Route::middleware('web')->group(function () {
    // Material API
    Route::get('/materials', [MaterialController::class, 'getMaterials']);

    // Cart API
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::put('/cart/update/{id}', [CartController::class, 'updateQuantity']);
    Route::delete('/cart/delete/{id}', [CartController::class, 'removeFromCart']);
    Route::delete('/cart/remove-material/{id}', [CartController::class, 'removeByMaterial']);

    // Payment & Checkout API
    Route::post('/payment/checkout', [PaymentController::class, 'checkout']);
    Route::post('/payment/callback', [PaymentController::class, 'callback']);
});