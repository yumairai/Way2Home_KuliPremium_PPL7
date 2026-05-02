<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;

class ProyekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customerIds = Customer::pluck('id')->toArray();

        $proyekData = [
            [
                'customer_id'   => $customerIds[0] ?? 1,
                'mandor_id'     => null,
                'jenis_proyek'  => 'Bangun Rumah',
                'alamat_proyek' => 'Jl. Melati No. 12, Bandung',
                'tanggal_mulai' => null, // Biasanya tanggal fiksasi nunggu mandor
                'status_proyek' => 'Pengalokasian Mandor',
                'jumlah_cicilan' => 4,
            ],
            [
                'customer_id'   => $customerIds[1] ?? 2,
                'mandor_id'     => null,
                'jenis_proyek'  => 'Bangun Rumah',
                'alamat_proyek' => 'Komp. Asri B-9, Jakarta',
                'tanggal_mulai' => null,
                'status_proyek' => 'Pengalokasian Mandor',
                'jumlah_cicilan' => 0,
            ],
            [
                'customer_id'   => $customerIds[2] ?? 3,
                'mandor_id'     => null,
                'jenis_proyek'  => 'Bangun Rumah',
                'alamat_proyek' => 'Jl. Raya Utama No. 45, Surabaya',
                'tanggal_mulai' => null,
                'status_proyek' => 'Pengalokasian Mandor',
                'jumlah_cicilan' => 12,
            ],

            // 1 Proyek: Revisi Dokumen
            [
                'customer_id'   => $customerIds[0] ?? 1,
                'mandor_id'     => null,
                'jenis_proyek'  => 'Bangun Rumah ',
                'alamat_proyek' => 'Jl. Melati No. 12, Bandung',
                'tanggal_mulai' => null,
                'status_proyek' => 'Revisi Dokumen',
                'jumlah_cicilan' => 0,
            ],

            // 1 Proyek: Menunggu Verifikasi
            [
                'customer_id'   => $customerIds[1] ?? 2,
                'mandor_id'     => null,
                'jenis_proyek'  => 'Bangun Rumah',
                'alamat_proyek' => 'Komp. Asri B-9, Jakarta',
                'tanggal_mulai' => null,
                'status_proyek' => 'Menunggu Verifikasi',
                'jumlah_cicilan' => 2,
            ],
        ];

        foreach ($proyekData as $data) {
            DB::table('proyek')->insert(array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
