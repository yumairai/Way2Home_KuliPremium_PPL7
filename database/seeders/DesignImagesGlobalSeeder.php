<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignImagesGlobalSeeder extends Seeder
{
    /**
     * Seed 9 global images: 3 categories × 3 positions
     * 
     * Kategori: Minimalist, Modern, Mewah (Premium removed)
     * Setiap kategori: 3 gambar (urutan 1, 2, 3)
     * 
     * Mapping ke 3 rekomendasi:
     * - Rekomendasi 1 (Ranking 1) → gambar urutan 1
     * - Rekomendasi 2 (Ranking 2) → gambar urutan 2
     * - Rekomendasi 3 (Ranking 3) → gambar urutan 3
     */
    public function run(): void
    {
        $kategoris = ['Minimalist', 'Modern', 'Mewah'];
        $images = [];
        $now = now();

        foreach ($kategoris as $kategori) {
            for ($urutan = 1; $urutan <= 3; $urutan++) {
                $images[] = [
                    'kategori' => $kategori,
                    'urutan' => $urutan,
                    'path_gambar' => sprintf(
                        'images/design-placeholder/%s-design-%d.jpg',
                        strtolower(str_replace(' ', '-', $kategori)),
                        $urutan
                    ),
                    'deskripsi' => "{$kategori} Design #{$urutan}",
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('design_images_global')->insert($images);

        $this->command->info('DesignImagesGlobal: 9 images seeded (3 categories × 3 positions).');
    }
}
