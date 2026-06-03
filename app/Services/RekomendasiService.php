<?php

namespace App\Services;

use App\Models\DesainRumah;
use Illuminate\Support\Collection;

/**
 * RekomendasiService
 *
 * Pure ML Content-Based Filtering engine untuk rekomendasi rumah.
 *
 * Algoritma:
 *   1. Min-Max Normalization pada semua fitur numerik
 *   2. Weighted Feature Similarity Score:
 *        score = Σ (weight_i × similarity_i)
 *   3. Bobot fitur dinamis berdasarkan PRIORITAS user
 *   4. Return Top-N hasil dengan skor tertinggi
 *
 * Fitur yang digunakan:
 *   - lokasi        : match eksak (binary 0/1)
 *   - gaya_arsitektur : match eksak (binary 0/1)
 *   - luas_tanah    : normalized proximity  (1 - |norm_a - norm_b|)
 *   - jumlah_kamar_tidur : normalized proximity
 *   - estimasi_biaya     : normalized proximity
 */
class RekomendasiService
{
    // ─────────────────────────────────────────
    // Dataset statistics (dari CSV - static reference)
    // ─────────────────────────────────────────
    private const STATS = [
        'luas_tanah'   => ['min' => 30,          'max' => 350],
        'jumlah_kamar' => ['min' => 1,            'max' => 10],
        'harga'        => ['min' => 22_500,       'max' => 15_718_461_000],
    ];

    // ─────────────────────────────────────────
    // Bobot berdasarkan prioritas user
    // ─────────────────────────────────────────
    private const WEIGHTS = [
        'biaya'   => ['lokasi' => 0.10, 'gaya' => 0.10, 'luas' => 0.10, 'kamar' => 0.15, 'harga' => 0.55],
        'estetik' => ['lokasi' => 0.20, 'gaya' => 0.30, 'luas' => 0.20, 'kamar' => 0.15, 'harga' => 0.15],
        'cepat'   => ['lokasi' => 0.20, 'gaya' => 0.10, 'luas' => 0.35, 'kamar' => 0.15, 'harga' => 0.20],
    ];

    /**
     * Generate top-N rekomendasi rumah berdasarkan preferensi user.
     *
     * @param array $preferensi {
     *   lokasi: string,
     *   luas_area: int,
     *   jumlah_kamar: int,
     *   budget: int,
     *   prioritas: string (biaya|estetik|cepat)
     * }
     * @param int $topN  Jumlah rekomendasi yang dikembalikan
     * @return Collection
     */
    public function rekomendasikan(array $preferensi, int $topN = 6): Collection
    {
        $weights = self::WEIGHTS[$preferensi['prioritas']] ?? self::WEIGHTS['biaya'];

        // Normalisasi nilai preferensi user
        $normUser = [
            'luas'   => $this->normalize($preferensi['luas_area'],    self::STATS['luas_tanah']),
            'kamar'  => $this->normalize($preferensi['jumlah_kamar'], self::STATS['jumlah_kamar']),
            'harga'  => $this->normalize($preferensi['budget'],       self::STATS['harga']),
        ];

        // Ambil semua rumah dari DB (chunk-based untuk memori efisien)
        $scored = collect();

        DesainRumah::query()->chunk(500, function ($rows) use ($preferensi, $normUser, $weights, &$scored) {
            foreach ($rows as $desain) {
                $score = $this->hitungSkor($desain, $preferensi, $normUser, $weights);

                $scored->push([
                    'id'            => $desain->id,
                    'nama_rumah'    => $desain->tipe_rumah,
                    'lokasi'        => $desain->lokasi ?? '-',
                    'gaya_arsitektur' => $desain->gaya_arsitektur ?? '-',
                    'deskripsi'     => $desain->deskripsi,
                    'luas_tanah'    => $desain->luas_tanah,
                    'luas_bangunan' => $desain->luas_bangunan,
                    'jumlah_kamar'  => $desain->jumlah_kamar_tidur,
                    'jumlah_kamar_mandi' => $desain->jumlah_kamar_mandi,
                    'jumlah_lantai' => $desain->jumlah_lantai ?? 1,
                    'tahun_bangun'  => $desain->tahun_bangun ?? now()->year,
                    'harga'         => $desain->estimasi_biaya,
                    'estimasi_durasi' => $desain->estimasi_durasi,
                    'material_digunakan' => $desain->material_digunakan ?: $desain->material_utama,
                    'fasilitas'     => $desain->fasilitas,
                    'path_gambar_desain' => $desain->path_gambar_desain,
                    'skor'          => round($score * 100, 2), // dalam persen
                ]);
            }
        });

        return $scored
            ->sortByDesc('skor')
            ->values()
            ->take($topN);
    }

    // ─────────────────────────────────────────
    // Hitung skor kemiripan untuk 1 rumah
    // ─────────────────────────────────────────
    private function hitungSkor(
        DesainRumah $rumah,
        array $preferensi,
        array $normUser,
        array $weights
    ): float {
        // 1. Lokasi: exact match
        $skorLokasi = strcasecmp((string) $rumah->lokasi, (string) $preferensi['lokasi']) === 0 ? 1.0 : 0.0;

        // 2. Gaya arsitektur: exact match
        $skorGaya = strcasecmp((string) $rumah->gaya_arsitektur, (string) $preferensi['gaya_arsitektur']) === 0 ? 1.0 : 0.0;

        // 3. Luas tanah: proximity similarity
        $normLuas  = $this->normalize($rumah->luas_tanah,   self::STATS['luas_tanah']);
        $skorLuas  = $this->proximitySimilarity($normLuas,  $normUser['luas']);

        // 4. Jumlah kamar: proximity similarity
        $normKamar = $this->normalize($rumah->jumlah_kamar_tidur, self::STATS['jumlah_kamar']);
        $skorKamar = $this->proximitySimilarity($normKamar, $normUser['kamar']);

        // 5. Harga: proximity similarity (budget sebagai batas atas ideal)
        $normHarga = $this->normalize($rumah->estimasi_biaya, self::STATS['harga']);
        $skorHarga = $this->hargaSimilarity($normHarga, $normUser['harga']);

        // Weighted sum
        return
            $weights['lokasi'] * $skorLokasi +
            $weights['gaya']   * $skorGaya   +
            $weights['luas']   * $skorLuas   +
            $weights['kamar']  * $skorKamar  +
            $weights['harga']  * $skorHarga;
    }

    // ─────────────────────────────────────────
    // Min-Max Normalization → [0, 1]
    // ─────────────────────────────────────────
    private function normalize(float $value, array $stats): float
    {
        $range = $stats['max'] - $stats['min'];
        if ($range == 0) return 0.0;
        return ($value - $stats['min']) / $range;
    }

    // ─────────────────────────────────────────
    // Proximity Similarity: semakin dekat semakin tinggi
    // similarity = 1 - |a - b|
    // ─────────────────────────────────────────
    private function proximitySimilarity(float $normA, float $normB): float
    {
        return max(0.0, 1.0 - abs($normA - $normB));
    }

    // ─────────────────────────────────────────
    // Harga Similarity:
    //   - Harga di bawah/sesuai budget → high score
    //   - Harga jauh di atas budget → penalti
    // ─────────────────────────────────────────
    private function hargaSimilarity(float $normHarga, float $normBudget): float
    {
        $diff = $normHarga - $normBudget;

        if ($diff <= 0) {
            // Harga ≤ budget: skor tinggi, semakin dekat semakin bagus
            return max(0.0, 1.0 - abs($diff) * 0.5);
        } else {
            // Harga > budget: penalti lebih besar
            return max(0.0, 1.0 - $diff * 2.0);
        }
    }

    // ─────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────

    /**
     * Format harga ke Rupiah singkat, misal: Rp 450 Jt, Rp 1,2 M
     */
    public static function formatHarga(int $harga): string
    {
        if ($harga >= 1_000_000_000) {
            return 'Rp ' . number_format($harga / 1_000_000_000, 1, ',', '.') . ' M';
        }
        return 'Rp ' . number_format($harga / 1_000_000, 0, ',', '.') . ' jt';
    }

    /**
     * Estimasi durasi konstruksi berdasarkan luas tanah (dalam bulan)
     */
    public static function estimasiDurasi(int $luasTanah): string
    {
        $bulan = (int) ceil($luasTanah / 30); // ~30 m² per bulan
        $bulan = max(3, min($bulan, 24));
        return $bulan . ' Bulan';
    }
}
