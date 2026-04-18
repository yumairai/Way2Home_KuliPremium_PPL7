<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('materials')->insert([
            [
                'nama_material' => 'Besi Beton 19mm',
                'kategori' => 'Beton',
                'harga' => 320000,
                'satuan' => 'btg',
                'stok' => 2500,
                'deskripsi' => 'Perwira • 19mm • 27kg',
                'path_foto_material' => 'images/material/beton.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_material' => 'Bata Ringan Walbric',
                'kategori' => 'Hebel',
                'harga' => 740000,
                'satuan' => 'kubik',
                'stok' => 5000,
                'deskripsi' => 'Walbric • 60x20x10cm • 83pcs',
                'path_foto_material' => 'images/material/hebel.jpeg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_material' => 'Besi Beton 16mm',
                'kategori' => 'Beton',
                'harga' => 230000,
                'satuan' => 'btg',
                'stok' => 3000,
                'deskripsi' => 'AS Asia Steel • 16mm • 19kg',
                'path_foto_material' => 'images/material/beton.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_material' => 'Keramik Asia Tile',
                'kategori' => 'Keramik',
                'harga' => 60000,
                'satuan' => 'dus',
                'stok' => 9000,
                'deskripsi' => 'Asia Tile • 40x40cm • 17kg',
                'path_foto_material' => 'images/material/keramik.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_material' => 'Cat Dinding Dulux',
                'kategori' => 'Cat',
                'harga' => 915000,
                'satuan' => 'pail',
                'stok' => 10000,
                'deskripsi' => 'Dulux • Chrysan White • 25kg',
                'path_foto_material' => 'images/material/dulux.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_material' => 'Granite Tile',
                'kategori' => 'Granite',
                'harga' => 120000,
                'satuan' => 'dus',
                'stok' => 0,
                'deskripsi' => 'Niro • 60x60cm • 34kg',
                'path_foto_material' => 'images/material/granite.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_material' => 'Cat Dinding Nobrand',
                'kategori' => 'Cat',
                'harga' => 230000,
                'satuan' => 'pail',
                'stok' => 0,
                'deskripsi' => 'Nobrand • Acrylic Emulsion • 20kg',
                'path_foto_material' => 'images/material/nobrand.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_material' => 'Bata Ringan JUMA',
                'kategori' => 'Hebel',
                'harga' => 500000,
                'satuan' => 'kubik',
                'stok' => 8000,
                'deskripsi' => 'JUMA • 60x20x10cm • 83pcs',
                'path_foto_material' => 'images/material/hebel.jpeg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_material' => 'Triplek Meranti Campur',
                'kategori' => 'Triplek',
                'harga' => 175000,
                'satuan' => 'lembar',
                'stok' => 4000,
                'deskripsi' => 'Meranti Campur • 122cm x 244cm x 1.8cm • 20kg',
                'path_foto_material' => 'images/material/triplek.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_material' => 'Pipa PVC Wavin 3\'',
                'kategori' => 'PVC',
                'harga' => 97000,
                'satuan' => 'btg',
                'stok' => 0,
                'deskripsi' => 'Wavin • 4m • Tipe D',
                'path_foto_material' => 'images/material/pvc.jpeg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_material' => 'Besi Beton 13mm',
                'kategori' => 'Beton',
                'harga' => 150000,
                'satuan' => 'btg',
                'stok' => 2000,
                'deskripsi' => 'AS Asia Steel • 13mm • 13kg',
                'path_foto_material' => 'images/material/beton.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_material' => 'Pipa PVC Wavin 2 1/2\'',
                'kategori' => 'PVC',
                'harga' => 75000,
                'satuan' => 'btg',
                'stok' => 1000,
                'deskripsi' => 'Wavin • 4m • Tipe D',
                'path_foto_material' => 'images/material/pvc.jpeg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}