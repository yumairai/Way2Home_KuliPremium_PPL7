<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PreferensiRumah;
use App\Models\RekomendasiRumah;
use App\Models\DesignImageGlobal;
use App\Services\MLRecommendationService;
use Illuminate\Support\Facades\Auth;

class PreferensiController extends Controller
{
    protected MLRecommendationService $mlService;

    public function __construct(MLRecommendationService $mlService)
    {
        $this->mlService = $mlService;
    }

    /**
     * Simpan preferensi user, jalankan ML engine (KNN), simpan hasil di session.
     */
    public function store(Request $request)
    {
        $request->validate([
            'location'    => 'required|string',
            'style'       => 'required|string',
            'area'        => 'required|integer|min:25|max:350',
            'bedrooms'    => 'required|integer|min:1',
            'bathrooms'   => 'required|integer|min:1',
            'garage'      => 'required|integer|min:0',
            'quality'     => 'required|integer|min:1|max:10',
            'budget'      => 'required|integer|min:100000000|max:2000000000',
            //'ac_required' => 'required|boolean',
            'priority'    => 'required|in:biaya,estetik,cepat',
            'flexibility' => 'required|numeric|min:0|max:50',
        ]);

        $user = Auth::user();

        if (!$user->customer) {
            return response()->json([
                'message' => 'Data Profil Customer tidak ditemukan. Pastikan Anda sudah terdaftar sebagai Customer.',
            ], 404);
        }

        // Simpan preferensi ke database (tetap pakai format lama)
        $preferensi = PreferensiRumah::create([
            'customer_id'     => $user->customer->id,
            'lokasi'          => $request->location,
            'gaya_arsitektur' => $request->style,
            'luas_area'       => $request->area,
            'jumlah_kamar'    => $request->bedrooms,
            'budget'          => $request->budget,
            'prioritas'       => $request->priority,
        ]);

        // ─── Jalankan ML Engine KNN ───────────────────────────────────────
        $inputML = [
            'location'    => $request->location,
            'style'       => $request->style,
            'area'        => (int) $request->area,
            'bedrooms'    => (int) $request->bedrooms,
            'bathrooms'   => (int) $request->bathrooms,
            'garage'      => (int) $request->garage,
            'quality'     => (int) $request->quality,
            'budget'      => (int) $request->budget,
            //'ac_required' => (bool) $request->ac_required,
            'priority'    => $request->priority,
            'flexibility' => (float) $request->flexibility,
        ];

        $rekomendasi = $this->mlService->recommend($inputML, k: 3);

        if (!empty($rekomendasi)) {
            $now = now();
            $records = [];
            foreach ($rekomendasi as $rumah) {
                $records[] = [
                    'preferensi_rumah_id' => $preferensi->id,
                    'desain_rumah_id' => $rumah['id'],
                    'skor_rekomendasi' => $rumah['ml_score'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            if (!empty($records)) {
                RekomendasiRumah::query()->insert($records);
            }
        }

        // Simpan hasil rekomendasi ke session
        session([
            'rekomendasi_preferensi_id' => $preferensi->id,
            'rekomendasi_hasil'       => $rekomendasi,
            'rekomendasi_preferensi'  => [
                'lokasi'          => $request->location,
                'gaya_arsitektur' => $request->style,
                'luas_area'       => $request->area,
                'jumlah_kamar'    => $request->bedrooms,
                'budget'          => $request->budget,
                'prioritas'       => $request->priority,
            ],
            'ml_algorithm_info'  => [
                'algorithm'      => 'K-Nearest Neighbors (KNN)',
                'distance'       => 'Weighted Euclidean Distance',
                'normalization'  => 'Min-Max Scaling',
                'k'              => 3,
                'priority'       => $request->priority,
                'total_features' => 6,
            ],
        ]);
        // ─────────────────────────────────────────────────────────────────

        return response()->json([
            'message'     => 'Rekomendasi KNN berhasil dibuat!',
            'preferensi'  => $preferensi,
            'rekomendasi' => $rekomendasi,
        ], 201);
    }

    /**
     * Tampilkan halaman hasil rekomendasi KNN.
     */
    public function result()
    {
        $hasil = [];
        $preferensi = [];
        $algorithmInfo = [];
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
                    ->map(function ($item, $index) {
                        $desain = $item->desainRumah;

                        if (!$desain) {
                            return null;
                        }

                        // Get image from global 12 images pool
                        $imagePosition = $index + 1;
                        $kategori = $desain->gaya_arsitektur ?? 'Modern';
                        $gambarPath = DesignImageGlobal::getImageByCategory($kategori, $imagePosition);

                        if (!$gambarPath) {
                            $gambarPath = DesignImageGlobal::getRandomImageByCategory($kategori);
                        }

                        return [
                            'id' => $desain->id,
                            'tipe_rumah' => $desain->tipe_rumah,
                            'deskripsi' => $desain->deskripsi,
                            'lokasi' => $desain->lokasi ?? '-',
                            'gaya_arsitektur' => $desain->gaya_arsitektur ?? '-',
                            'luas_bangunan' => $desain->luas_bangunan,
                            'luas_tanah' => $desain->luas_tanah,
                            'jumlah_kamar' => $desain->jumlah_kamar_tidur,
                            'jumlah_kamar_mandi' => $desain->jumlah_kamar_mandi,
                            'jumlah_lantai' => $desain->jumlah_lantai ?? 1,
                            'estimasi_biaya' => $desain->estimasi_biaya,
                            'estimasi_durasi' => $desain->estimasi_durasi,
                            'material_utama' => $desain->material_utama,
                            'material_digunakan' => $desain->material_digunakan ?: $desain->material_utama,
                            'fasilitas' => $desain->fasilitas,
                            'path_gambar_desain' => $gambarPath ? asset($gambarPath) : $desain->path_gambar_desain,
                            'ml_score' => (float) $item->skor_rekomendasi,
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

        $algorithmInfo = session('ml_algorithm_info', []);

        if (empty($hasil)) {
            return redirect('/recommendation')->with('error', 'Silakan isi preferensi terlebih dahulu.');
        }

        return view('customer-layouts.rekomendasi_rumah', compact('hasil', 'preferensi', 'algorithmInfo'));
    }

    /**
     * Handle design selection - user picks one design
     */
    public function select(Request $request)
    {
        $request->validate([
            'desain_id' => 'required|integer|exists:desain_rumah,id',
            'preferensi_id' => 'required|integer',
        ]);

        $preferensiId = $request->preferensi_id;
        $selectedDesainId = $request->desain_id;

        // Update rekomendasi status to mark which one is selected
        RekomendasiRumah::where('preferensi_rumah_id', $preferensiId)
            ->update(['is_selected' => false]);

        RekomendasiRumah::where('preferensi_rumah_id', $preferensiId)
            ->where('desain_rumah_id', $selectedDesainId)
            ->update(['is_selected' => true]);

        return response()->json([
            'message' => 'Desain rumah berhasil dipilih!',
            'desain_id' => $selectedDesainId,
        ], 200);
    }
}
