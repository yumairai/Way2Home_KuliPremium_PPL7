<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DetailProyekBangunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proyekList = DB::table('proyek')->get();

        $desainId = 1;

        foreach ($proyekList as $proyek) {
            $catatan = null;

            if ($proyek->status_proyek === 'Revisi Dokumen') {
                $catatan = 'Foto sertifikat tanah tidak terbaca jelas. Mohon unggah ulang dengan resolusi yang lebih tinggi.';
            }

            DB::table('detail_proyek_bangun')->insert([
                'proyek_id'        => $proyek->id,
                'desain_rumah_id'  => $desainId,
                'catatan_admin'    => $catatan,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }
    }
}
