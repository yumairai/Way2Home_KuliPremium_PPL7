<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- GRUP AUTH (PUBLIC) ---
// Route ini bisa diakses tanpa login (untuk daftar dan masuk)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']); // F002
    Route::post('/login', [AuthController::class, 'login']);       // F001
});

// --- GRUP PROTECTED (HARUS LOGIN) ---
// Route di bawah ini hanya bisa diakses jika membawa "Token" (Sanctum)
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

    // Nanti F005, F007, F008 dst ditaruh di sini...
});