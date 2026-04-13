<?php

use Illuminate\Support\Facades\Route;

// guest
Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('customer.dashboard');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', function () {
    return view('register');
})->name('register');

// inputnya
Route::get('/rekomendasi/input', function () {
    return view('customer.input_preferensi_ai');
});

// hasil inputnya
Route::get('/rekomendasi/hasil', function () {
    return view('customer.rekomendasi_rumah');
});

// otw ke form pembangunan rumah abis klik tombol pilih desain 
Route::get('/pembangunan', function () {
    return view('customer.form_pembangunan_rumah');
});

// otw ke material marketplace abis klik tombol beli material di form pembangunan rumah
Route::get('/material-only', function () {
    return view('customer.material_marketplace');
});

// otw ke progress track user abis klik tombol lihat progress pembangunan rumah di form pembangunan rumah 
Route::get('/progress-track-user', function () {
    return view('customer.material_marketplace'); // sementara ke sini dulu yah hehe
});

Route::get('/material', function () {
    return view('customer.material_marketplace');
});

Route::get('/material/cart', function () {
    return view('customer.cart');
});

Route::get('/user/projects', function () {
    return view('customer.proyek_user');
});

Route::prefix('user')->group(function () {
    Route::get('/orders', function () {
        return view('customer.order_user');
    })->name('user.orders');

    Route::prefix('projects')->group(function () {
        Route::redirect('/', '/user/projects/1');

        Route::get('/{id}', function ($id) {
            $viewName = "customer.proyek_user" . $id;
            if (view()->exists($viewName)) {
                return view($viewName, ['projectId' => $id]);
            }
            abort(404, "Halaman dummy untuk proyek $id belum dibuat.");
        })->name('user.projects.detail');
    });

    Route::get('/profile', function () {
        return view('customer.profile_edit');
    })->name('user.profile');
});

// admin
Route::prefix('admin')->group(function () {
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

Route::get('/tes', function () {
    return view('customer.proyekuser');
});
