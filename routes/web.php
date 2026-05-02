<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\ProyekController;
use App\Http\Controllers\Customer\MaterialController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\PreferensiController;
use App\Http\Controllers\Customer\PaymentMaterialController;
use App\Http\Controllers\Customer\PaymentProyekController;
use App\Http\Controllers\Customer\RenovasiController as CustomerRenovasiController;
use App\Http\Controllers\Admin\VerifikasiProyekController;
use App\Http\Controllers\Admin\ManageMandorController;
use App\Http\Controllers\Mandor\RenovasiController as MandorRenovasiController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'index'])->name('home');

// Callback Midtrans (Harus di luar auth karena dipanggil server-to-server)
Route::post('/payment/callback', [PaymentMaterialController::class, 'callback']);
Route::post('/proyek/callback', [ProyekController::class, 'callback']);

/*
|--------------------------------------------------------------------------
| Guest Routes (Belum Login)
|--------------------------------------------------------------------------
*/

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::prefix('material')->group(function () {
    Route::get('/', [MaterialController::class, 'index'])->name('material.index');
    Route::get('/materials', [MaterialController::class, 'getMaterials']);
});

/*
|--------------------------------------------------------------------------
| Customer Routes (Sudah Login)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::middleware(['customer'])->group(function () {
        // Dashboard & Customer Home
        Route::get('/dashboard', function () {
            return view('customer-layouts.dashboard');
        })->name('customer-layouts.dashboard');

        Route::post('/preferensi/simpan', [PreferensiController::class, 'store'])->name('proyek.preferensi.simpan');

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

        // Fitur renovasi
        Route::get('/renovation', [CustomerRenovasiController::class, 'index'])->name('customer.renovation');
        Route::get('/renovation-form', [CustomerRenovasiController::class, 'create'])->name('customer.renovation_form');
        Route::post('/renovation', [CustomerRenovasiController::class, 'store'])->name('customer.renovation.store');
        Route::post('/renovation/{requestRenovasi}/accept-offer', [CustomerRenovasiController::class, 'acceptOffer'])
            ->name('customer.renovation.accept');
        Route::post('/renovation/{requestRenovasi}/negotiate', [CustomerRenovasiController::class, 'negotiate'])
            ->name('customer.renovation.negotiate');
        Route::post('/renovation/{requestRenovasi}/reject-offer', [CustomerRenovasiController::class, 'rejectOffer'])
            ->name('customer.renovation.reject');

        // Fitur order history
        Route::get('/order', function () {
            return view('customer-layouts.order');
        })->name('customer.order');
        Route::get('/recommendation/result', [PreferensiController::class, 'result']);

        Route::get('/house-build-form', [ProyekController::class, 'create'])->name('proyek.form_bangun');

        Route::prefix('project')->group(function () {
            Route::redirect('/', '/project/1');
            Route::get('/{id}/tracking', function ($id) {
                return view('customer-layouts.customer_tracking');
            });
            Route::get('/{id}', [ProyekController::class, 'show'])->where('id', '[1-5]');
        });

        // Fitur profile
        Route::get('/profile', function () {
            return view('customer-layouts.profile');
        })->name('customer.profile');

        // Manajemen Proyek
        Route::prefix('proyek')->group(function () {
            Route::get('/', [ProyekController::class, 'index'])->name('proyek.index');
            Route::get('/{id}/tracking', function ($id) {          // ← pindah ke sini, sebelum /{id}
                return view('customer-layouts.customer_tracking');
            })->name('proyek.tracking');
            Route::get('/{id}', [ProyekController::class, 'show'])->name('proyek.show');

            // Action Ajax
            Route::post('/ajukan', [ProyekController::class, 'store'])->name('proyek.store');
            Route::post('/bayar-dp', [PaymentProyekController::class, 'bayarDP'])->name('proyek.bayarDP');
        });

        // Material & Shopping Cart
        Route::prefix('material')->group(function () {
            Route::view('/cart', 'customer-layouts.cart')->name('cart.view');
        });


        // Operasi Keranjang (AJAX)
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index']);
            Route::post('/add', [CartController::class, 'addToCart']);
            Route::put('/update/{id}', [CartController::class, 'updateQuantity']);
            Route::delete('/delete/{id}', [CartController::class, 'removeFromCart']);
            Route::delete('/remove-material/{id}', [CartController::class, 'removeByMaterial']);
        });

        // Checkout Material
        Route::post('/payment/checkout', [PaymentMaterialController::class, 'checkout']);
        Route::post('/payment/callback', [PaymentMaterialController::class, 'callback']);
        Route::post('/proyek/payment-success', [PaymentProyekController::class, 'handleSuccess']);
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/verifikasi', [VerifikasiProyekController::class, 'index'])->name('admin.verifikasi');
    Route::put('/verifikasi/{id}', [VerifikasiProyekController::class, 'update'])->name('admin.verifikasi.update');

    Route::get('/kelola-material', function () {
        return view('admin.kelola_material');
    })->name('admin.material');

    Route::get('/order-management', function () {
        return view('admin.manajemen_order');
    })->name('admin.order');

    Route::get('/manajemen-mandor', [ManageMandorController::class, 'index'])->name('admin.mandor');
    Route::post('/manajemen-mandor/assign', [ManageMandorController::class, 'assign'])->name('admin.mandor.assign');
    Route::post('/manajemen-mandor/unassign', [ManageMandorController::class, 'unassign'])->name('admin.mandor.unassign');

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


/*
|--------------------------------------------------------------------------
| Mandor Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'mandor'])->prefix('mandor')->group(function () {
    Route::get('/tracking', [MandorRenovasiController::class, 'tracking'])->name('mandor.tracking');
    Route::post('/renovation/{requestRenovasi}/done', [MandorRenovasiController::class, 'markDone'])
        ->name('mandor.renovation.done');
    Route::get('/dashboard', [MandorRenovasiController::class, 'dashboard'])->name('mandor.dashboard');
    Route::post('/renovation/{requestRenovasi}/negotiate', [MandorRenovasiController::class, 'negotiate'])
        ->name('mandor.renovation.negotiate');
    Route::post('/renovation/{requestRenovasi}/offer', [MandorRenovasiController::class, 'submitOffer'])
        ->name('mandor.renovation.offer');
});
