<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesainRumahSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = database_path('data/dummy_rumah_bandung_2000_v2.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found at: {$csvPath}");
            return;
        }

        $handle = fopen($csvPath, 'r');
        fgetcsv($handle); // skip header

        $batch = [];
        $batchSize = 200;
        $now = now();
        $gayaList = ['Minimalist', 'Modern', 'Mewah'];
        
        $locations = [
            'Kota Bandung',
            'Kabupaten Bandung',
            'Kabupaten Bandung Barat',
            'Kota Cimahi',
            'Kabupaten Sumedang',
            'Kabupaten Garut',
            'Kota Tasikmalaya',
            'Kabupaten Tasikmalaya',
            'Kabupaten Cianjur',
            'Kota Sukabumi',
            'Kabupaten Sukabumi',
            'Kota Bogor',
            'Kabupaten Bogor',
            'Kota Depok',
            'Kota Bekasi',
            'Kabupaten Bekasi',
            'Kabupaten Karawang',
            'Kabupaten Purwakarta',
            'Kabupaten Subang',
            'Kabupaten Indramayu',
            'Kota Cirebon',
            'Kabupaten Cirebon',
            'Kabupaten Kuningan',
            'Kabupaten Majalengka',
            'Kabupaten Ciamis',
            'Kota Banjar',
            'Kabupaten Pangandaran',
        ];
        $locationCount = count($locations);

        $rowIndex = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 9) {
                continue;
            }

            $id = (int) $row[0];
            $nama = trim($row[1]);
            $luasTanah = (int) $row[3];
            $jumlahKamar = (int) $row[4];
            $jumlahLantai = (int) $row[5];
            $tahunBangun = (int) $row[6];
            $harga = (int) $row[7];
            $materialDigunakan = trim((string) ($row[8] ?? ''));

            // Cycle through locations
            $lokasi = $locations[$rowIndex % $locationCount];
            
            $materialUtama = collect(explode(';', $materialDigunakan))
                ->map(fn($m) => trim(explode(':', $m)[0] ?? ''))
                ->filter()
                ->first() ?? 'Material Umum';

            $estimasiDurasi = max(3, min((int) ceil($luasTanah / 30), 24));
            $gaya = $gayaList[$rowIndex % 3];

            $batch[] = [
                'id' => $id,
                'tipe_rumah' => $nama,
                'deskripsi' => "Desain {$nama} dengan fokus kenyamanan keluarga di wilayah {$lokasi}.",
                'lokasi' => $lokasi,
                'gaya_arsitektur' => $gaya,
                'luas_tanah' => $luasTanah,
                'luas_bangunan' => (int) round($luasTanah * 0.78),
                'jumlah_kamar_tidur' => $jumlahKamar,
                'jumlah_kamar_mandi' => max(1, min(4, (int) ceil($jumlahKamar / 2))),
                'jumlah_lantai' => max(1, $jumlahLantai),
                'tahun_bangun' => $tahunBangun,
                'estimasi_biaya' => $harga,
                'estimasi_durasi' => $estimasiDurasi,
                'material_utama' => $materialUtama,
                'material_digunakan' => $materialDigunakan,
                'path_gambar_desain' => 'images/rekomendasi/rekom' . (($rowIndex % 3) + 1) . '.jpg',
                'fasilitas' => 'Ruang keluarga; Dapur; Kamar utama; Carport',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $rowIndex++;

            if (count($batch) >= $batchSize) {
                DB::table('desain_rumah')->upsert($batch, ['id'], [
                    'tipe_rumah', 'deskripsi', 'lokasi', 'gaya_arsitektur',
                    'luas_tanah', 'luas_bangunan', 'jumlah_kamar_tidur',
                    'jumlah_kamar_mandi', 'jumlah_lantai', 'tahun_bangun',
                    'estimasi_biaya', 'estimasi_durasi', 'material_utama',
                    'material_digunakan', 'path_gambar_desain', 'fasilitas',
                    'updated_at'
                ]);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('desain_rumah')->upsert($batch, ['id'], [
                'tipe_rumah', 'deskripsi', 'lokasi', 'gaya_arsitektur',
                'luas_tanah', 'luas_bangunan', 'jumlah_kamar_tidur',
                'jumlah_kamar_mandi', 'jumlah_lantai', 'tahun_bangun',
                'estimasi_biaya', 'estimasi_durasi', 'material_utama',
                'material_digunakan', 'path_gambar_desain', 'fasilitas',
                'updated_at'
            ]);
        }

        fclose($handle);

        $this->command->info('DesainRumah: ' . DB::table('desain_rumah')->count() . ' records seeded.');
    }
}
