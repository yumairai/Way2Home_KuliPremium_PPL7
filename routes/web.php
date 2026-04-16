<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PreferensiController;
use App\Http\Controllers\Api\ProyekController;

Route::get('/', [AuthController::class, 'index'])->name('home');

Route::get('/material', function () {
    return view('customer-layouts.material_marketplace');
})->name('material.index');

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

    Route::get('/recommendation/result', function () {
        return view('customer-layouts.rekomendasi_rumah');
    });

    Route::get('/house-build-form', function () {
        return view('customer-layouts.form_pembangunan_rumah');
    });

    Route::prefix('project')->group(function () {
        Route::redirect('/', '/project/1');

        Route::get('/1', function () {
            return view('customer-layouts.proyek_user1');
        });

        Route::get('/2', function () {
            return view('customer-layouts.proyek_user2');
        });

        Route::get('/3', function () {
            return view('customer-layouts.proyek_user3');
        });

        Route::get('/4', function () {
            return view('customer-layouts.proyek_user4');
        });

        Route::get('/5', function () {
            return view('customer-layouts.proyek_user5');
        });
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
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
