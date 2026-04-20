<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PreferensiRumah;
use App\Models\RekomendasiRumah;
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
            'gaya_arsitektur' => $request->gaya_arsitektur,
            'luas_area'    => (int) $request->luas_area,
            'jumlah_kamar' => (int) $request->jumlah_kamar,
            'budget'       => (int) $request->budget,
            'prioritas'    => $request->prioritas,
        ];

        $rekomendasi = $this->rekomendasiService->rekomendasikan($inputML, topN: 3);

        if ($rekomendasi->isNotEmpty()) {
            $now = now();
            RekomendasiRumah::query()->insert(
                $rekomendasi->map(fn ($rumah) => [
                    'preferensi_rumah_id' => $preferensi->id,
                    'desain_rumah_id' => $rumah['id'],
                    'skor_rekomendasi' => $rumah['skor'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all()
            );
        }

        // Simpan hasil rekomendasi ke session
        session([
            'rekomendasi_preferensi_id' => $preferensi->id,
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
        $hasil = [];
        $preferensi = [];
        $preferensiId = session('rekomendasi_preferensi_id');

        if (!empty($preferensiId)) {
            $preferensiModel = PreferensiRumah::query()->find($preferensiId);

            if ($preferensiModel) {
                $preferensi = [
                    'lokasi' => $preferensiModel->lokasi,
                    'gaya_arsitektur' => $preferensiModel->gaya_arsitektur,
                    'luas_area' => $preferensiModel->luas_area,
                    'jumlah_kamar' => $preferensiModel->jumlah_kamar,
                    'budget' => $preferensiModel->budget,
                    'prioritas' => $preferensiModel->prioritas,
                ];

                $hasil = $preferensiModel->rekomendasiRumah()
                    ->with('desainRumah')
                    ->orderByDesc('skor_rekomendasi')
                    ->take(3)
                    ->get()
                    ->map(function ($item) {
                        $desain = $item->desainRumah;

                        if (!$desain) {
                            return null;
                        }

                        return [
                            'id' => $desain->id,
                            'nama_rumah' => $desain->tipe_rumah,
                            'lokasi' => $desain->lokasi ?? '-',
                            'gaya_arsitektur' => $desain->gaya_arsitektur ?? '-',
                            'deskripsi' => $desain->deskripsi,
                            'luas_tanah' => $desain->luas_tanah,
                            'luas_bangunan' => $desain->luas_bangunan,
                            'jumlah_kamar' => $desain->jumlah_kamar_tidur,
                            'jumlah_kamar_mandi' => $desain->jumlah_kamar_mandi,
                            'jumlah_lantai' => $desain->jumlah_lantai ?? 1,
                            'tahun_bangun' => $desain->tahun_bangun ?? now()->year,
                            'harga' => $desain->estimasi_biaya,
                            'estimasi_durasi' => $desain->estimasi_durasi,
                            'material_digunakan' => $desain->material_digunakan ?: $desain->material_utama,
                            'fasilitas' => $desain->fasilitas,
                            'path_gambar_desain' => $desain->path_gambar_desain,
                            'skor' => round((float) $item->skor_rekomendasi, 2),
                        ];
                    })
                    ->filter()
                    ->values()
                    ->toArray();
            }
        }

        if (empty($hasil)) {
            $hasil = session('rekomendasi_hasil', []);
        }

        if (empty($preferensi)) {
            $preferensi = session('rekomendasi_preferensi', []);
        }

        if (empty($hasil)) {
            return redirect('/recommendation')->with('error', 'Silakan isi preferensi terlebih dahulu.');
        }

        return view('customer-layouts.rekomendasi_rumah', compact('hasil', 'preferensi'));
    }
}
