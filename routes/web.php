<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\MaterialController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\PreferensiController;
use App\Http\Controllers\Customer\ProyekController;

Route::get('/', [AuthController::class, 'index'])->name('home');

Route::get('/material', [MaterialController::class, 'index'])->name('material.index');
Route::get('/api/materials', [MaterialController::class, 'getMaterials']);

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware(['auth'])->group(function () {
    Route::post('/api/preferensi/simpan', [PreferensiController::class, 'store']);
    Route::post('/api/proyek/ajukan', [ProyekController::class, 'store']); // pindah ke sini yak dari api.php

    Route::get('/dashboard', function () {
        return view('customer-layouts.dashboard');
    })->name('customer-layouts.dashboard');

    Route::get('/material/cart', function () {
        return view('customer-layouts.cart');
    });

    Route::get('/recommendation', function () {
        return view('customer-layouts.input_preferensi_ai');
    });

    Route::get('/recommendation/result', [PreferensiController::class, 'result']);

    Route::get('/renovation', function () {
        return view('customer-layouts.renovation');
    })->name('customer.renovation');

    Route::get('/renovation-form', function () {
        return view('customer-layouts.renovation_form');
    })->name('customer.renovation_form');

    Route::get('/house-build-form', function () {
        return view('customer-layouts.form_pembangunan_rumah');
    });

    Route::prefix('project')->group(function () {
        Route::redirect('/', '/project/1');
        Route::get('/{id}/tracking', function () {
            return view('customer-layouts.customer_tracking');
        })->where('id', '[1-5]');
        Route::get('/{id}', [ProyekController::class, 'show'])->where('id', '[1-5]');
    });

    Route::get('/profile', function () {
        return view('customer-layouts.profile');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/customer-logout', [AuthController::class, 'logout'])->name('customer.logout');
});

// admin
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::redirect('/', '/admin/dashboard');

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/verifikasi', function () {
        return view('admin.verifikasi_dokumen');
    })->name('admin.verifikasi');

    Route::get('/kelola-material', function () {
        return view('admin.kelola_material');
    })->name('admin.kelola_material');

    Route::get('/manajemen-mandor', function () {
        return view('admin.manajemen_mandor');
    })->name('admin.manajemen_mandor');

    Route::get('/monitor-proyek', function () {
        return view('admin.monitor_proyek');
    })->name('admin.monitor_proyek');

    // buat urusan pdf di verifikasi dokumen, nanti tinggal ganti aja path nya sesuai kebutuhan
    Route::get('/preview/{filename}', function ($filename) {
        $path = public_path('images/aset/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }
        $mimeType = mime_content_type($path);

        $isPdf = $mimeType === 'application/pdf';

        return response(file_get_contents($path), 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $isPdf ? 'attachment' : 'inline',
            'Cache-Control' => 'no-cache',
        ]);
    })->name('admin.preview');
});


// nanti buatkan auth middleware khusus mandor, biar bisa akses route mandor ini, sekarang sementara dibiarkan aja untuk testing
Route::get('/mandor/tracking', function () {
    return view('mandor.mandor_tracking');
})->name('mandor.tracking');

Route::get('/mandor/dashboard', function () {
    return view('mandor.mandor_dashboard');
})->name('mandor.dashboard');
