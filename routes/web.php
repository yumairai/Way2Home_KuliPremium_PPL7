<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\ProyekController;
use App\Http\Controllers\Customer\MaterialController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\PreferensiController;
use App\Http\Controllers\Customer\PaymentMaterialController;
use App\Http\Controllers\Customer\PaymentProyekController;
use App\Http\Controllers\Customer\RenovasiController as CustomerRenovasiController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\TrackingProyekController as CustomerTrackingProyekController;
use App\Http\Controllers\Admin\VerifikasiProyekController;
use App\Http\Controllers\Admin\ManageMandorController;
use App\Http\Controllers\Mandor\RenovasiController as MandorRenovasiController;
use App\Http\Controllers\Mandor\TrackingProyekController as MandorTrackingProyekController;
use App\Http\Controllers\Admin\ManageMaterialController;
use App\Http\Controllers\Admin\ManageOrderController;
use App\Http\Controllers\Customer\OrderController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'index'])->name('home');

Route::post('/payment/callback', [PaymentMaterialController::class, 'callback']);
Route::post('/payment/checkout/success', [PaymentMaterialController::class, 'handleSuccess']); // ← tambah ini
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

// 🔔 Halaman notice (setelah register/login tapi belum verif)
Route::get('/email/verify-notice', function () {
    return view('email.verify-notice');
})->middleware('auth')->name('verification.notice');


// 🔗 Klik link dari email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    // ✅ setelah verif → ke dashboard customer
    return redirect()->route('customer-layouts.dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');


// 🔁 Kirim ulang email verifikasi
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Email verifikasi dikirim ulang!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');



Route::get('/test-email', function () {
    Mail::raw('Test email verification', function ($message) {
        $message->to('your-email@example.com')
                ->subject('Test Verification');
    });

    return 'Email sent';
});

Route::post('/midtrans/callback', [PaymentProyekController::class, 'callback'])
    ->name('midtrans.callback');

/*
|--------------------------------------------------------------------------
| Customer Routes (Sudah Login)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile (tidak perlu middleware customer)
    Route::get('/profile', function () {
        return view('customer-layouts.profile', ['user' => Auth::user()]);
    })->name('customer.profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('customer.profile.update');
    Route::post('/profile/update-address-data', [ProfileController::class, 'updateAddressData'])->name('customer.profile.updateAddressData');

    Route::middleware(['auth', 'customer', 'verified'])->group(function () {

        // Dashboard
        Route::get('/dashboard', function () {
            return view('customer-layouts.dashboard');
        })->name('customer-layouts.dashboard');

        Route::post('/preferensi/simpan', [PreferensiController::class, 'store'])->name('proyek.preferensi.simpan');

        // Rekomendasi
        Route::get('/recommendation', function () {
            return view('customer-layouts.input_preferensi_ai');
        })->name('recommendation.input');
        Route::get('/recommendation/result', [PreferensiController::class, 'result'])->name('recommendation.result');

        // Form Pembangunan
        Route::get('/house-build-form', [ProyekController::class, 'create'])->name('proyek.form_bangun');

        // Renovasi
        Route::get('/renovation', [CustomerRenovasiController::class, 'index'])->name('customer.renovation');
        Route::get('/renovation-form', [CustomerRenovasiController::class, 'create'])->name('customer.renovation_form');
        Route::post('/renovation', [CustomerRenovasiController::class, 'store'])->name('customer.renovation.store');
        Route::post('/renovation/{requestRenovasi}/accept-offer', [CustomerRenovasiController::class, 'acceptOffer'])->name('customer.renovation.accept');
        Route::post('/renovation/{requestRenovasi}/negotiate', [CustomerRenovasiController::class, 'negotiate'])->name('customer.renovation.negotiate');
        Route::post('/renovation/{requestRenovasi}/reject-offer', [CustomerRenovasiController::class, 'rejectOffer'])->name('customer.renovation.reject');

        // Order history
        Route::get('/order', [OrderController::class, 'index'])->name('customer.order');


        // Manajemen Proyek
        Route::prefix('proyek')->group(function () {
            Route::get('/', [ProyekController::class, 'index'])->name('proyek.index');
            Route::get('/dokumentasi/{dok}', [CustomerTrackingProyekController::class, 'getDokumentasiUrl'])->name('proyek.dokumentasi.url'); // ← sudah benar posisinya
            Route::get('/{id}/tracking', [CustomerTrackingProyekController::class, 'tracking'])->name('proyek.tracking');
            Route::get('/{id}', [ProyekController::class, 'show'])->name('proyek.show');
            Route::post('/{id}/batal', [ProyekController::class, 'batal'])->name('proyek.batal');
            Route::post('/ajukan', [ProyekController::class, 'store'])->name('proyek.store');
            Route::post('/bayar', [PaymentProyekController::class, 'bayar']);
            Route::post('/payment-success', [PaymentProyekController::class, 'handleSuccess']);
        });

        // Material & Cart
        Route::prefix('material')->group(function () {
            Route::view('/cart', 'customer-layouts.cart')->name('cart.view');
        });

        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index']);
            Route::post('/add', [CartController::class, 'addToCart']);
            Route::put('/update/{id}', [CartController::class, 'updateQuantity']);
            Route::delete('/delete/{id}', [CartController::class, 'removeFromCart']);
            Route::delete('/remove-material/{id}', [CartController::class, 'removeByMaterial']);
        });

        // Checkout
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

    Route::get('/kelola-material', [ManageMaterialController::class, 'index'])
        ->name('admin.material');

    Route::get('/order-management', [ManageOrderController::class, 'index'])->name('admin.order');
    Route::patch('/order-management/{order}/status', [ManageOrderController::class, 'updateStatus'])->name('admin.order.updateStatus');

    Route::get('/kelola-material', [ManageMaterialController::class, 'index'])->name('admin.material');
    Route::post('/kelola-material', [ManageMaterialController::class, 'store'])->name('admin.material.store');
    Route::put('/kelola-material/{material}', [ManageMaterialController::class, 'update'])->name('admin.material.update');
    Route::delete('/kelola-material/{material}', [ManageMaterialController::class, 'destroy'])->name('admin.material.destroy');

    Route::get('/manajemen-mandor', [ManageMandorController::class, 'index'])->name('admin.mandor');
    Route::post('/manajemen-mandor/assign', [ManageMandorController::class, 'assign'])->name('admin.mandor.assign');
    Route::post('/manajemen-mandor/unassign', [ManageMandorController::class, 'unassign'])->name('admin.mandor.unassign');

    Route::get('/monitor-proyek', function () {
        return view('admin.monitor_proyek');
    })->name('admin.monitor');

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
    Route::get('/dashboard', [MandorRenovasiController::class, 'dashboard'])->name('mandor.dashboard');
    Route::get('/tracking', [MandorRenovasiController::class, 'tracking'])->name('mandor.tracking');
    Route::post('/renovation/{requestRenovasi}/done', [MandorRenovasiController::class, 'markDone'])->name('mandor.renovation.done');
    Route::post('/renovation/{requestRenovasi}/negotiate', [MandorRenovasiController::class, 'negotiate'])->name('mandor.renovation.negotiate');
    Route::post('/renovation/{requestRenovasi}/offer', [MandorRenovasiController::class, 'submitOffer'])->name('mandor.renovation.offer');

    Route::get('/proyek/tracking', [MandorTrackingProyekController::class, 'tracking'])->name('mandor.proyek.tracking');
    Route::post('/task/{task}/complete', [MandorTrackingProyekController::class, 'completeTask'])->name('mandor.task.complete');
    Route::post('/proyek/{proyek}/aktivitas', [MandorTrackingProyekController::class, 'tambahAktivitas'])->name('mandor.proyek.aktivitas');
    Route::post('/proyek/{proyek}/dokumentasi', [MandorTrackingProyekController::class, 'uploadDokumentasi'])->name('mandor.proyek.dokumentasi');
    Route::get('/dokumentasi/{dok}', [MandorTrackingProyekController::class, 'getDokumentasiUrl'])->name('mandor.dokumentasi.url');
});