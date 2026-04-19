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
                'estimasi_durasi' => 12, // 120 hari
                'material_utama' => 'Bata Ringan & Baja Ringan',
                'path_gambar_desain' => 'images/rekomendasi/rekom1.jpg',
                'fasilitas' => 'Carport, Taman Depan, Ruang Jemur',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}