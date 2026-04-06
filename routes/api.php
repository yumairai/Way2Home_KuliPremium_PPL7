<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PreferensiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    
    // F003: Logout
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    // F004: Get User Profile
    Route::get('/user/profile', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ]);
    });

    Route::post('/preferensi/simpan', [PreferensiController::class, 'store']);
});