<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RumahDatasetSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = database_path('data/dummy_rumah_bandung_2000_v2.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found at: {$csvPath}");
            return;
        }

        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle); // skip header row

        $batch = [];
        $batchSize = 200;
        $now = now();

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 9) continue;

            $batch[] = [
                'id'                 => (int) $row[0],
                'nama_rumah'         => $row[1],
                'lokasi'             => $row[2],
                'luas_tanah'         => (int) $row[3],
                'jumlah_kamar'       => (int) $row[4],
                'jumlah_lantai'      => (int) $row[5],
                'tahun_bangun'       => (int) $row[6],
                'harga'              => (int) $row[7],
                'material_digunakan' => $row[8] ?? null,
                'created_at'         => $now,
                'updated_at'         => $now,
            ];

            if (count($batch) >= $batchSize) {
                DB::table('rumah_dataset')->upsert($batch, ['id']);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('rumah_dataset')->upsert($batch, ['id']);
        }

        fclose($handle);

        $this->command->info('RumahDataset: ' . DB::table('rumah_dataset')->count() . ' records seeded.');
    }
}
