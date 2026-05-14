<?php

namespace App\Services;

use App\Models\DesainRumah;
use Illuminate\Support\Collection;

/**
 * RekomendasiService - Hybrid ML Recommendation Engine
 *
 * Algoritma:
 *   1. Content-Based Filtering (70%):
 *      - Min-Max Normalization pada fitur numerik
 *      - Weighted Feature Similarity Score
 *   2. Collaborative Filtering Simulation (15%):
 *      - Similarity dengan designs yang match user profile
 *   3. Material Availability Scoring (15%):
 *      - Prefer designs dengan material yang tersedia
 *
 * Bobot dinamis berdasarkan prioritas user (biaya/estetik/cepat)
 */
class RekomendasiService
{
    private const STATS = [
        'luas_tanah'   => ['min' => 30,          'max' => 350],
        'jumlah_kamar' => ['min' => 1,            'max' => 10],
        'harga'        => ['min' => 100_000_000,  'max' => 2_000_000_000],
    ];

    private const CONTENT_WEIGHTS = [
        'biaya'   => ['lokasi' => 0.15, 'gaya' => 0.10, 'luas' => 0.10, 'kamar' => 0.15, 'harga' => 0.50],
        'estetik' => ['lokasi' => 0.20, 'gaya' => 0.35, 'luas' => 0.15, 'kamar' => 0.10, 'harga' => 0.20],
        'cepat'   => ['lokasi' => 0.15, 'gaya' => 0.10, 'luas' => 0.40, 'kamar' => 0.10, 'harga' => 0.25],
    ];

    /**
     * Main recommendation engine combining 3 scoring approaches
     */
    public function rekomendasikan(array $preferensi, int $topN = 6): Collection
    {
        // Get base content similarity scores
        $contentScored = $this->scoreByContent($preferensi);

        // Enhance with collaborative filtering
        $hybridScored = $this->enhanceWithCollaborative($contentScored, $preferensi);

        // Boost with material availability
        $finalScored = $this->boostWithMaterialAvailability($hybridScored, $preferensi);

        return $finalScored
            ->sortByDesc('skor')
            ->values()
            ->take($topN);
    }

    // ─────────────────────────────────────────────────────────────
    // 1. CONTENT-BASED FILTERING (70% weight)
    // ─────────────────────────────────────────────────────────────

    private function scoreByContent(array $preferensi): Collection
    {
        $weights = self::CONTENT_WEIGHTS[$preferensi['prioritas']] ?? self::CONTENT_WEIGHTS['biaya'];

        $normUser = [
            'luas'   => $this->normalize($preferensi['luas_area'], self::STATS['luas_tanah']),
            'kamar'  => $this->normalize($preferensi['jumlah_kamar'], self::STATS['jumlah_kamar']),
            'harga'  => $this->normalize($preferensi['budget'], self::STATS['harga']),
        ];

        $scored = collect();

        DesainRumah::query()->chunk(500, function ($rows) use ($preferensi, $normUser, $weights, &$scored) {
            foreach ($rows as $desain) {
                $score = $this->hitungSkorContent($desain, $preferensi, $normUser, $weights);

                $scored->push([
                    'id'                 => $desain->id,
                    'nama_rumah'         => $desain->tipe_rumah,
                    'lokasi'             => $desain->lokasi ?? '-',
                    'gaya_arsitektur'    => $desain->gaya_arsitektur ?? '-',
                    'deskripsi'          => $desain->deskripsi,
                    'luas_tanah'         => $desain->luas_tanah,
                    'luas_bangunan'      => $desain->luas_bangunan,
                    'jumlah_kamar'       => $desain->jumlah_kamar_tidur,
                    'jumlah_kamar_mandi' => $desain->jumlah_kamar_mandi,
                    'jumlah_lantai'      => $desain->jumlah_lantai ?? 1,
                    'tahun_bangun'       => $desain->tahun_bangun ?? now()->year,
                    'harga'              => $desain->estimasi_biaya,
                    'estimasi_durasi'    => $desain->estimasi_durasi,
                    'material_digunakan' => $desain->material_digunakan ?: $desain->material_utama,
                    'fasilitas'          => $desain->fasilitas,
                    'path_gambar_desain' => $desain->path_gambar_desain,
                    'skor_content'       => $score,
                    'skor_collaborative' => 0.5,
                    'skor_material'      => 0.5,
                    'skor'               => 0,
                ]);
            }
        });

        return $scored;
    }

    private function hitungSkorContent(
        DesainRumah $rumah,
        array $preferensi,
        array $normUser,
        array $weights
    ): float {
        $skorLokasi = strcasecmp((string) $rumah->lokasi, (string) $preferensi['lokasi']) === 0 ? 1.0 : 0.0;
        $skorGaya   = strcasecmp((string) $rumah->gaya_arsitektur, (string) $preferensi['gaya_arsitektur']) === 0 ? 1.0 : 0.0;

        $normLuas   = $this->normalize($rumah->luas_tanah, self::STATS['luas_tanah']);
        $skorLuas   = $this->proximitySimilarity($normLuas, $normUser['luas']);

        $normKamar  = $this->normalize($rumah->jumlah_kamar_tidur, self::STATS['jumlah_kamar']);
        $skorKamar  = $this->proximitySimilarity($normKamar, $normUser['kamar']);

        $normHarga  = $this->normalize($rumah->estimasi_biaya, self::STATS['harga']);
        $skorHarga  = $this->hargaSimilarity($normHarga, $normUser['harga']);

        return
            $weights['lokasi'] * $skorLokasi +
            $weights['gaya']   * $skorGaya   +
            $weights['luas']   * $skorLuas   +
            $weights['kamar']  * $skorKamar  +
            $weights['harga']  * $skorHarga;
    }

    // ─────────────────────────────────────────────────────────────
    // 2. COLLABORATIVE FILTERING SIMULATION (15% weight)
    // ─────────────────────────────────────────────────────────────

    private function enhanceWithCollaborative(Collection $contentScored, array $preferensi): Collection
    {
        return $contentScored->map(function ($item) use ($preferensi) {
            // Collaborative score: similarity to designs in same location + gaya
            $collaboScore = 0.5;

            if ($item['lokasi'] === $preferensi['lokasi']) {
                $collaboScore += 0.25;
            }
            if ($item['gaya_arsitektur'] === $preferensi['gaya_arsitektur']) {
                $collaboScore += 0.25;
            }

            // Boost if nearby price range
            $budgetRange = $preferensi['budget'] * 0.2;
            if (abs($item['harga'] - $preferensi['budget']) <= $budgetRange) {
                $collaboScore += 0.1;
            }

            $item['skor_collaborative'] = min(1.0, $collaboScore);
            return $item;
        });
    }

    // ─────────────────────────────────────────────────────────────
    // 3. MATERIAL AVAILABILITY SCORING (15% weight)
    // ─────────────────────────────────────────────────────────────

    private function boostWithMaterialAvailability(Collection $scored, array $preferensi): Collection
    {
        return $scored->map(function ($item) {
            // Query material availability for this design
            $designMaterials = DesainRumah::find($item['id'])
                ?->materials()
                ?->where('stok', '>', 0)
                ?->count() ?? 0;

            $totalMaterials = DesainRumah::find($item['id'])
                ?->materials()
                ?->count() ?? 1;

            $availabilityRatio = $totalMaterials > 0 ? $designMaterials / $totalMaterials : 0.7;
            $item['skor_material'] = $availabilityRatio;

            // Hybrid score: 70% content + 15% collaborative + 15% material
            $item['skor'] = round(
                ($item['skor_content'] * 0.70) +
                ($item['skor_collaborative'] * 0.15) +
                ($item['skor_material'] * 0.15) * 100,
                2
            );

            return $item;
        });
    }

    // ─────────────────────────────────────────────────────────────
    // HELPER METHODS
    // ─────────────────────────────────────────────────────────────

    private function normalize(float $value, array $stats): float
    {
        $range = $stats['max'] - $stats['min'];
        if ($range == 0) return 0.0;
        return ($value - $stats['min']) / $range;
    }

    private function proximitySimilarity(float $normA, float $normB): float
    {
        return max(0.0, 1.0 - abs($normA - $normB));
    }

    private function hargaSimilarity(float $normHarga, float $normBudget): float
    {
        $diff = $normHarga - $normBudget;

        if ($diff <= 0) {
            return max(0.0, 1.0 - abs($diff) * 0.5);
        } else {
            return max(0.0, 1.0 - $diff * 2.0);
        }
    }

    public static function formatHarga(int $harga): string
    {
        if ($harga >= 1_000_000_000) {
            return 'Rp ' . number_format($harga / 1_000_000_000, 1, ',', '.') . ' M';
        }
        $bulat = ceil($harga / 1_000_000) * 1_000_000;
        return 'Rp ' . number_format($bulat, 0, ',', '.') . ' jt';
    }

    public static function estimasiDurasi(int $luasTanah): string
    {
        $bulan = (int) ceil($luasTanah / 30);
        $bulan = max(3, min($bulan, 24));
        return $bulan . ' Bulan';
    }
}
