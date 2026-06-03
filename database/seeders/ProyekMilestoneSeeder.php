<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProyekMilestone;
use App\Models\Proyek;

class ProyekMilestoneSeeder extends Seeder
{
    public static array $defaultTasks = [
        // Fondasi
        ['nama_task' => 'Galian & Urugan',              'milestone' => 'Fondasi',   'urutan' => 1],
        ['nama_task' => 'Pemasangan Batu Kali',          'milestone' => 'Fondasi',   'urutan' => 2],
        ['nama_task' => 'Sloof Beton',                   'milestone' => 'Fondasi',   'urutan' => 3],
        // Struktur
        ['nama_task' => 'Pemasangan Hebel/Dinding',      'milestone' => 'Struktur',  'urutan' => 4],
        ['nama_task' => 'Kolom & Balok',                 'milestone' => 'Struktur',  'urutan' => 5],
        ['nama_task' => 'Plester & Acian',               'milestone' => 'Struktur',  'urutan' => 6],
        // Atap
        ['nama_task' => 'Rangka Atap',                   'milestone' => 'Atap',      'urutan' => 7],
        ['nama_task' => 'Pemasangan Genteng',             'milestone' => 'Atap',      'urutan' => 8],
        ['nama_task' => 'Plafon & Lipslang',             'milestone' => 'Atap',      'urutan' => 9],
        // MEP
        ['nama_task' => 'Instalasi Listrik',             'milestone' => 'MEP',       'urutan' => 10],
        ['nama_task' => 'Instalasi Air & Sanitasi',      'milestone' => 'MEP',       'urutan' => 11],
        ['nama_task' => 'Pemasangan Titik Lampu',        'milestone' => 'MEP',       'urutan' => 12],
        // Finishing
        ['nama_task' => 'Pemasangan Lantai & Keramik',   'milestone' => 'Finishing', 'urutan' => 13],
        ['nama_task' => 'Pengecatan & Kusen',             'milestone' => 'Finishing', 'urutan' => 14],
        ['nama_task' => 'Sanitari & Aksesoris',           'milestone' => 'Finishing', 'urutan' => 15],
    ];

    public static function generateForProyek(int $proyekId): void
    {
        foreach (self::$defaultTasks as $task) {
            ProyekMilestone::create([
                'proyek_id'  => $proyekId,
                'nama_task'  => $task['nama_task'],
                'milestone'  => $task['milestone'],
                'urutan'     => $task['urutan'],
                'is_selesai' => false,
            ]);
        }
    }

    public function run(): void
    {
        Proyek::where('status_proyek', 'In Progress')
            ->where('jenis_proyek', 'Bangun Rumah')
            ->whereDoesntHave('tasks')
            ->each(fn($proyek) => self::generateForProyek($proyek->id));
    }
}