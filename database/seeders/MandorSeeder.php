<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Mandor;

class MandorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mandorUsers = User::where('role', 'mandor')->get();

        $data = [
            [
                'area_kerja'       => 'Jakarta Selatan',
                'lama_pengalaman'  => 8,
                'sertifikasi'      => 'SKT Madya',
            ],
            [
                'area_kerja'       => 'Jakarta Barat',
                'lama_pengalaman'  => 5,
                'sertifikasi'      => null,
            ],
            [
                'area_kerja'       => 'Depok',
                'lama_pengalaman'  => 12,
                'sertifikasi'      => 'SKT Utama',
            ],
        ];

        foreach ($mandorUsers as $index => $user) {
            Mandor::create([
                'user_id'          => $user->id,
                'sertifikasi'      => $data[$index]['sertifikasi'],
                'path_foto_profil' => 'profil/mandor/foto_profil_mandor.png',
                'area_kerja'       => $data[$index]['area_kerja'],
                'path_foto_ktp'    => null,
                'lama_pengalaman'  => $data[$index]['lama_pengalaman'],
                'status'           => 'aktif',
                'rating'           => 0,
            ]);
        }
    }
}
