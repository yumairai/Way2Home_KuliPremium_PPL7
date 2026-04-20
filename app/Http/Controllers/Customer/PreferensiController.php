<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PreferensiRumah;
use App\Services\RekomendasiService;
use Illuminate\Support\Facades\Auth;

class PreferensiController extends Controller
{
    protected RekomendasiService $rekomendasiService;

    public function __construct(RekomendasiService $rekomendasiService)
    {
        $this->rekomendasiService = $rekomendasiService;
    }

    /**
     * Simpan preferensi user, jalankan ML engine, simpan hasil di session.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lokasi'          => 'required|string',
            'gaya_arsitektur' => 'required|string',
            'luas_area'       => 'required|integer|min:30|max:350',
            'jumlah_kamar'    => 'required|integer|min:1|max:10',
            'budget'          => 'required|integer|min:100000000',
            'prioritas'       => 'required|string|in:biaya,estetik,cepat',
        ]);

        $user = Auth::user();

        if (!$user->customer) {
            return response()->json([
                'message' => 'Data Profil Customer tidak ditemukan. Pastikan Anda sudah terdaftar sebagai Customer.',
            ], 404);
        }

        // Simpan preferensi ke database
        $preferensi = PreferensiRumah::create([
            'customer_id'     => $user->customer->id,
            'lokasi'          => $request->lokasi,
            'gaya_arsitektur' => $request->gaya_arsitektur,
            'luas_area'       => $request->luas_area,
            'jumlah_kamar'    => $request->jumlah_kamar,
            'budget'          => $request->budget,
            'prioritas'       => $request->prioritas,
        ]);

        // ─── Jalankan ML Engine ───────────────────────────────────────────
        $inputML = [
            'lokasi'       => $request->lokasi,
            'luas_area'    => (int) $request->luas_area,
            'jumlah_kamar' => (int) $request->jumlah_kamar,
            'budget'       => (int) $request->budget,
            'prioritas'    => $request->prioritas,
        ];

        $rekomendasi = $this->rekomendasiService->rekomendasikan($inputML, topN: 3);

        // Simpan hasil rekomendasi ke session
        session([
            'rekomendasi_hasil'       => $rekomendasi->toArray(),
            'rekomendasi_preferensi'  => [
                'lokasi'          => $request->lokasi,
                'gaya_arsitektur' => $request->gaya_arsitektur,
                'luas_area'       => $request->luas_area,
                'jumlah_kamar'    => $request->jumlah_kamar,
                'budget'          => $request->budget,
                'prioritas'       => $request->prioritas,
            ],
        ]);
        // ─────────────────────────────────────────────────────────────────

        return response()->json([
            'message'     => 'Rekomendasi berhasil dibuat!',
            'preferensi'  => $preferensi,
            'rekomendasi' => $rekomendasi,
        ], 201);
    }

    /**
     * Tampilkan halaman hasil rekomendasi.
     */
    public function result()
    {
        $hasil      = session('rekomendasi_hasil', []);
        $preferensi = session('rekomendasi_preferensi', []);

        if (empty($hasil)) {
            return redirect('/recommendation')->with('error', 'Silakan isi preferensi terlebih dahulu.');
        }

        return view('customer-layouts.rekomendasi_rumah', compact('hasil', 'preferensi'));
    }
}
