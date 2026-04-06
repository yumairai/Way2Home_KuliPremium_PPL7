<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PreferensiRumah;
use Illuminate\Support\Facades\Auth;

class PreferensiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'lokasi' => 'required|string',
            'gaya_arsitektur' => 'required|string',
            'luas_area' => 'required|integer',
            'jumlah_kamar' => 'required|integer',
            'budget' => 'required|integer',
            'prioritas' => 'required|string',
        ]);

        $user = Auth::user();

        if (!$user->customer) {
            return response()->json([
                'message' => 'Data Profil Customer tidak ditemukan. Pastikan Anda sudah terdaftar sebagai Customer.',
            ], 404);
        }

        $preferensi = PreferensiRumah::create([
            'customer_id'     => $user->customer->id,
            'lokasi'          => $request->lokasi,
            'gaya_arsitektur' => $request->gaya_arsitektur,
            'luas_area'       => $request->luas_area,
            'jumlah_kamar'    => $request->jumlah_kamar,
            'budget'          => $request->budget,
            'prioritas'       => $request->prioritas,
        ]);

        return response()->json([
            'message' => 'Preferensi rumah berhasil disimpan',
            'data'    => $preferensi
        ], 201);
    }
}