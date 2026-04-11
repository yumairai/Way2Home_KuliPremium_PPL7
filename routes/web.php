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

// admin
Route::get('/admin', function () {
    return view('admin.admin_page');
});