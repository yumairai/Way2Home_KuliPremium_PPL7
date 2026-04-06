<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('home');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', function () {
    return view('register');
})->name('register');

// inputnya
Route::get('/rekomendasi/input', function () {
    return view('input_preferensi_ai');
});

// hasil inputnya
Route::get('/rekomendasi/hasil', function () {
    return view('rekomendasi_rumah');
});

// otw ke form pembangunan rumah abis klik tombol pilih desain 
Route::get('/pembangunan', function () {
    return view('form_pembangunan_rumah');
});
