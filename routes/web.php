<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// inputnya
Route::get('/rekomendasi/input', function () {
    return view('input_preferensi_ai');
});

// hasil inputnya
Route::get('/rekomendasi/hasil', function () {
    return view('rekomendasi_rumah');
});
