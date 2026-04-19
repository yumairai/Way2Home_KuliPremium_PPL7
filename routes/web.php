<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\ProyekController;
use App\Http\Controllers\Customer\MaterialController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\PreferensiController;
use App\Http\Controllers\Customer\PaymentMaterialController;
use App\Http\Controllers\Customer\PaymentProyekController;
use App\Http\Controllers\Admin\VerifikasiProyekController;


// Public Routes
Route::get('/', [AuthController::class, 'index'])->name('home');

// Callback Midtrans 
Route::post('/payment/callback', [PaymentMaterialController::class, 'callback']);
Route::post('/proyek/callback', [ProyekController::class, 'callback']);


// Guest Routes (Belum Login)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});


// Customer Routes (Sudah Login)
Route::middleware(['auth'])->group(function () {

    // Dashboard & Logouts
    Route::get('/dashboard', function () {
        return view('customer-layouts.dashboard');
    })->name('customer-layouts.dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Fitur Rekomendasi & Form Pembangunan
    Route::get('/recommendation', function () {
        return view('customer-layouts.input_preferensi_ai');
    })->name('recommendation.input');

    Route::get('/recommendation/result', function () {
        return view('customer-layouts.rekomendasi_rumah');
    })->name('recommendation.result');

    Route::get('/house-build-form', function () {
        return view('customer-layouts.form_pembangunan_rumah');
    })->name('proyek.form');

    // Manajemen Proyek
    Route::prefix('proyek')->group(function () {
        Route::get('/', [ProyekController::class, 'index'])->name('proyek.index');
        Route::get('/{id}', [ProyekController::class, 'show'])->name('proyek.show');

        // Action Ajax
        Route::post('/ajukan', [ProyekController::class, 'store'])->name('proyek.store');
        Route::post('/bayar-dp', [ProyekController::class, 'bayarDP'])->name('proyek.bayarDP');
        Route::post('/preferensi/simpan', [PreferensiController::class, 'store'])->name('proyek.preferensi.simpan');
    });

    // Material & Shopping Cart
    Route::prefix('material')->group(function () {
        Route::get('/', [MaterialController::class, 'index'])->name('material.index');
        Route::get('/api-list', [MaterialController::class, 'getMaterials'])->name('material.api');

        Route::get('/cart', function () {
            return view('customer-layouts.cart');
        })->name('cart.view');
    });

    // Operasi Keranjang (AJAX)
    Route::prefix('cart')->group(function () {
        Route::get('/data', [CartController::class, 'index'])->name('cart.data');
        Route::post('/add', [CartController::class, 'addToCart'])->name('cart.add');
        Route::put('/update/{id}', [CartController::class, 'updateQuantity'])->name('cart.update');
        Route::delete('/delete/{id}', [CartController::class, 'removeFromCart'])->name('cart.delete');
        Route::delete('/remove-material/{id}', [CartController::class, 'removeByMaterial'])->name('cart.removeMaterial');
    });

    // Checkout Material
    Route::post('/payment/checkout', [PaymentMaterialController::class, 'checkout'])->name('checkout.material');

    // Pembayaran DP Proyek
    Route::post('/proyek/bayar-dp', [PaymentProyekController::class, 'bayarDP'])->name('proyek.bayarDP');
});


// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/verifikasi', [VerifikasiProyekController::class, 'index'])->name('admin.verifikasi');
    Route::put('/verifikasi/{id}', [VerifikasiProyekController::class, 'update'])->name('admin.verifikasi.update');

    Route::get('/kelola-material', function () {
        return view('admin.kelola_material');
    })->name('admin.material');
    Route::get('/manajemen-mandor', function () {
        return view('admin.manajemen_mandor');
    })->name('admin.mandor');
    Route::get('/monitor-proyek', function () {
        return view('admin.monitor_proyek');
    })->name('admin.monitor');

    // Preview Dokumen Proyek
    Route::get('/preview/{filename}', function ($filename) {
        $path = public_path('images/aset/' . $filename);
        if (!file_exists($path)) abort(404);

        $mimeType = mime_content_type($path);
        return response()->file($path, ['Content-Type' => $mimeType]);
    })->name('admin.preview');
});
