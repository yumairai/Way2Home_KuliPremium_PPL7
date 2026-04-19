<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DokumenProyekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua detail proyek bangun
        $details = DB::table('detail_proyek_bangun')->get();

        foreach ($details as $detail) {
            // Ambil status proyek untuk nentuin status verifikasi dokumen
            $proyek = DB::table('proyek')->where('id', $detail->proyek_id)->first();

            $statusDoc = 'disetujui';
            if ($proyek->status_proyek === 'Menunggu Verifikasi') {
                $statusDoc = 'pending';
            } elseif ($proyek->status_proyek === 'Revisi Dokumen') {
                $statusDoc = 'ditolak';
            }

            // Map jenis dokumen ke nama file dummy yang sama semua
            $dokumenMap = [
                'Sertifikat Tanah' => 'sertifikat_tanah.jpg',
                'KTP Pemilik'      => 'ktp.jpg',
                'IMB/PBG'          => 'imb.jpg',
                'Surat Kuasa'      => 'surat_kuasa.jpg',
            ];

            foreach ($dokumenMap as $jenis => $fileName) {
                DB::table('dokumen_proyek')->insert([
                    'detail_bangun_id' => $detail->id,
                    'jenis_dokumen'    => $jenis,
                    'file_path'        => "uploads/proyek/dokumen/{$fileName}", // Nama file sama semua
                    'status_verifikasi'=> $statusDoc,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        }
    }
}