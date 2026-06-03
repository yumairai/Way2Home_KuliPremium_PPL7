<?php

namespace App\Services;

use App\Models\DesainRumah;

/**
 * ML Recommendation Service menggunakan algoritma K-Nearest Neighbors (KNN)
 * 
 * Algoritma:
 * 1. Normalisasi fitur menggunakan Min-Max Scaling
 * 2. Hitung jarak Euclidean berbobot antara preferensi user dan setiap desain
 * 3. Urutkan berdasarkan jarak terkecil (paling mirip)
 * 4. Kembalikan K desain terdekat sebagai rekomendasi
 */
class MLRecommendationService
{
    /**
     * Bobot fitur per prioritas user (Feature Weights)
     * Total bobot = 1.0 untuk setiap prioritas
     */
    private array $featureWeights = [
        'biaya' => [
            'estimasi_biaya'        => 0.40,
            'luas_bangunan'         => 0.20,
            'jumlah_kamar_tidur'    => 0.15,
            'jumlah_kamar_mandi'    => 0.10,
            'estimasi_durasi'       => 0.10,
            'jumlah_lantai'         => 0.05,
        ],
        'estetik' => [
            'estimasi_biaya'        => 0.15,
            'luas_bangunan'         => 0.25,
            'jumlah_kamar_tidur'    => 0.20,
            'jumlah_kamar_mandi'    => 0.15,
            'estimasi_durasi'       => 0.10,
            'jumlah_lantai'         => 0.15,
        ],
        'cepat' => [
            'estimasi_biaya'        => 0.20,
            'luas_bangunan'         => 0.15,
            'jumlah_kamar_tidur'    => 0.15,
            'jumlah_kamar_mandi'    => 0.10,
            'estimasi_durasi'       => 0.35,
            'jumlah_lantai'         => 0.05,
        ],
    ];

    /**
     * Range min-max untuk normalisasi (berdasarkan data domain)
     */
    private array $featureRanges = [
        'estimasi_biaya'     => ['min' => 100_000_000,  'max' => 2_000_000_000],
        'luas_bangunan'      => ['min' => 25,            'max' => 350],
        'jumlah_kamar_tidur' => ['min' => 1,             'max' => 6],
        'jumlah_kamar_mandi' => ['min' => 1,             'max' => 5],
        'estimasi_durasi'    => ['min' => 1,             'max' => 24],
        'jumlah_lantai'      => ['min' => 1,             'max' => 3],
    ];

    /**
     * Jalankan algoritma KNN untuk merekomendasikan desain rumah
     *
     * @param array $prefs  Preferensi user dari form input
     * @param int   $k      Jumlah rekomendasi yang dikembalikan
     * @return array        Array desain rumah hasil rekomendasi dengan skor ML
     */
    public function recommend(array $prefs, int $k = 3): array
    {
        // Step 1: Ambil kandidat dari database (filter kategorikal dulu)
        $candidates = $this->getCandidates($prefs);

        if ($candidates->isEmpty()) {
            // Fallback: perluas pencarian tanpa filter lokasi & gaya
            $candidates = $this->getCandidates($prefs, relaxed: true);
        }

        if ($candidates->isEmpty()) {
            return [];
        }

        // Step 2: Buat vektor fitur untuk preferensi user
        $userVector = $this->buildUserVector($prefs);

        // Step 3: Hitung Weighted Euclidean Distance untuk setiap kandidat
        $weights = $this->featureWeights[$prefs['priority']];
        $distances = [];

        foreach ($candidates as $desain) {
            $desainVector = $this->buildDesainVector($desain);
            $distance = $this->weightedEuclideanDistance($userVector, $desainVector, $weights);

            // Hitung similarity score (0-100): semakin kecil jarak, semakin tinggi skor
            $similarityScore = round((1 / (1 + $distance)) * 100, 1);

            $distances[] = [
                'desain'           => $desain,
                'distance'         => $distance,
                'ml_score'         => $similarityScore,
                'feature_vector'   => $desainVector,
                'user_vector'      => $userVector,
            ];
        }

        // Step 4: Urutkan berdasarkan jarak terkecil (KNN sort)
        usort($distances, fn($a, $b) => $a['distance'] <=> $b['distance']);

        // Step 5: Ambil K terdekat dan format output
        return array_map(
            fn($item) => $this->formatResult($item),
            array_slice($distances, 0, $k)
        );
    }

    /**
     * Ambil kandidat desain dari DB dengan filter kategorikal
     */
    private function getCandidates(array $prefs, bool $relaxed = false)
    {
        $flexibility = ($prefs['flexibility'] / 100) * $prefs['budget'];
        $minBudget   = $prefs['budget'] - $flexibility;
        $maxBudget   = $prefs['budget'] + $flexibility;

        $query = DesainRumah::query()
            ->whereBetween('estimasi_biaya', [$minBudget, $maxBudget])
            ->where('jumlah_kamar_tidur', '>=', $prefs['bedrooms'])
            ->where('jumlah_kamar_mandi', '>=', $prefs['bathrooms']);

        if (!$relaxed) {
            $query->where('lokasi', 'like', '%' . $prefs['location'] . '%')
                  ->where('gaya_arsitektur', 'like', '%' . $prefs['style'] . '%');
        }

        return $query->get();
    }

    /**
     * Bangun vektor fitur numerik dari preferensi user (sudah ternormalisasi)
     */
    private function buildUserVector(array $prefs): array
    {
        $raw = [
            'estimasi_biaya'     => $prefs['budget'],
            'luas_bangunan'      => $prefs['area'],
            'jumlah_kamar_tidur' => $prefs['bedrooms'],
            'jumlah_kamar_mandi' => $prefs['bathrooms'],
            'estimasi_durasi'    => 12, // target durasi default user
            'jumlah_lantai'      => 1,  // default 1 lantai
        ];

        return $this->normalize($raw);
    }

    /**
     * Bangun vektor fitur numerik dari objek DesainRumah (sudah ternormalisasi)
     */
    private function buildDesainVector(DesainRumah $desain): array
    {
        $raw = [
            'estimasi_biaya'     => $desain->estimasi_biaya,
            'luas_bangunan'      => $desain->luas_bangunan,
            'jumlah_kamar_tidur' => $desain->jumlah_kamar_tidur,
            'jumlah_kamar_mandi' => $desain->jumlah_kamar_mandi,
            'estimasi_durasi'    => $desain->estimasi_durasi,
            'jumlah_lantai'      => $desain->jumlah_lantai ?? 1,
        ];

        return $this->normalize($raw);
    }

    /**
     * Min-Max Normalization: mengubah nilai ke rentang [0, 1]
     * Formula: x_norm = (x - min) / (max - min)
     */
    private function normalize(array $rawVector): array
    {
        $normalized = [];

        foreach ($rawVector as $feature => $value) {
            $min = $this->featureRanges[$feature]['min'];
            $max = $this->featureRanges[$feature]['max'];
            $range = $max - $min;

            $normalized[$feature] = $range > 0
                ? max(0.0, min(1.0, ($value - $min) / $range))
                : 0.0;
        }

        return $normalized;
    }

    /**
     * Weighted Euclidean Distance
     * Formula: d = sqrt( sum( w_i * (a_i - b_i)^2 ) )
     *
     * Memberikan bobot yang berbeda pada setiap fitur
     * sesuai prioritas user (biaya / estetik / cepat)
     */
    private function weightedEuclideanDistance(array $a, array $b, array $weights): float
    {
        $sumSquared = 0.0;

        foreach ($weights as $feature => $weight) {
            if (isset($a[$feature], $b[$feature])) {
                $diff = $a[$feature] - $b[$feature];
                $sumSquared += $weight * ($diff * $diff);
            }
        }

        return sqrt($sumSquared);
    }

    /**
     * Format hasil rekomendasi untuk ditampilkan di view
     */
    private function formatResult(array $item): array
    {
        $desain = $item['desain'];
        $userVec = $item['user_vector'];
        $desainVec = $item['feature_vector'];

        return [
            'id'               => $desain->id,
            'tipe_rumah'       => $desain->tipe_rumah,
            'deskripsi'        => $desain->deskripsi,
            'lokasi'           => $desain->lokasi,
            'gaya_arsitektur'  => $desain->gaya_arsitektur,
            'luas_bangunan'    => $desain->luas_bangunan,
            'luas_tanah'       => $desain->luas_tanah,
            'jumlah_kamar_tidur'  => $desain->jumlah_kamar_tidur,
            'jumlah_kamar_mandi'  => $desain->jumlah_kamar_mandi,
            'jumlah_lantai'    => $desain->jumlah_lantai ?? 1,
            'estimasi_biaya'   => $desain->estimasi_biaya,
            'estimasi_durasi'  => $desain->estimasi_durasi,
            'material_utama'   => $desain->material_utama,
            'fasilitas'        => $desain->fasilitas,
            'path_gambar_desain' => $desain->path_gambar_desain,

            // Data ML
            'ml_score'         => $item['ml_score'],
            'ml_distance'      => round($item['distance'], 4),
            'ml_feature_match' => $this->buildFeatureMatchDetail($userVec, $desainVec),
        ];
    }

    /**
     * Buat detail perbandingan fitur user vs desain (untuk transparansi ML)
     */
    private function buildFeatureMatchDetail(array $userVec, array $desainVec): array
    {
        $labels = [
            'estimasi_biaya'     => 'Budget',
            'luas_bangunan'      => 'Luas Bangunan',
            'jumlah_kamar_tidur' => 'Kamar Tidur',
            'jumlah_kamar_mandi' => 'Kamar Mandi',
            'estimasi_durasi'    => 'Durasi Bangun',
            'jumlah_lantai'      => 'Jumlah Lantai',
        ];

        $details = [];
        foreach ($labels as $feature => $label) {
            $match = 1 - abs(($userVec[$feature] ?? 0) - ($desainVec[$feature] ?? 0));
            $details[] = [
                'label'   => $label,
                'match'   => round($match * 100),
            ];
        }
        return $details;
    }
}
