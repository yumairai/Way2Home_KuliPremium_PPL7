<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('desain_rumah')->insert([
            [
                'id' => 1,
                'tipe_rumah' => 'Modern Minimalis Type 45',
                'deskripsi' => 'Rumah efisien dengan konsep open plan, cocok untuk keluarga baru yang menginginkan estetika modern.',
                'luas_tanah' => 90,
                'luas_bangunan' => 45,
                'jumlah_kamar_tidur' => 2,
                'jumlah_kamar_mandi' => 1,
                'estimasi_biaya' => 250000000,
                'estimasi_durasi' => 120, // 120 hari
                'material_utama' => 'Bata Ringan & Baja Ringan',
                'path_gambar_desain' => 'desain/modern-minimalis.jpg',
                'fasilitas' => 'Carport, Taman Depan, Ruang Jemur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'tipe_rumah' => 'Scandinavian Luxury Type 70',
                'deskripsi' => 'Desain mewah dengan langit-langit tinggi dan pencahayaan alami yang maksimal.',
                'luas_tanah' => 120,
                'luas_bangunan' => 70,
                'jumlah_kamar_tidur' => 3,
                'jumlah_kamar_mandi' => 2,
                'estimasi_biaya' => 450000000,
                'estimasi_durasi' => 180, // 180 hari
                'material_utama' => 'Beton Bertulang & Granit',
                'path_gambar_desain' => 'desain/scandinavian.jpg',
                'fasilitas' => 'Garasi 2 Mobil, Balkon, Smart Home System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'tipe_rumah' => 'Industrial Urban Type 60',
                'deskripsi' => 'Sentuhan bata ekspos dan aksen besi metalik yang memberikan kesan maskulin dan kekinian.',
                'luas_tanah' => 100,
                'luas_bangunan' => 60,
                'jumlah_kamar_tidur' => 2,
                'jumlah_kamar_mandi' => 2,
                'estimasi_biaya' => 350000000,
                'estimasi_durasi' => 150, 
                'material_utama' => 'Semen Ekspos & Besi H-Beam',
                'path_gambar_desain' => 'desain/industrial.jpg',
                'fasilitas' => 'Roof Garden, Kitchen Set Industrial',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}