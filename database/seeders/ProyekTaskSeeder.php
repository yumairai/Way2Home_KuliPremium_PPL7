<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProyekTask;
use App\Models\Proyek;

class ProyekTaskSeeder extends Seeder
{
    public static array $defaultTasks = [
        ['nama_task' => 'Pembersihan & Pengukuran Lahan',   'urutan' => 1],
        ['nama_task' => 'Galian Tanah Pondasi',              'urutan' => 2],
        ['nama_task' => 'Pemasangan Bekisting Pondasi',      'urutan' => 3],
        ['nama_task' => 'Pengecoran Pondasi',                'urutan' => 4],
        ['nama_task' => 'Pemasangan Sloof',                  'urutan' => 5],
        ['nama_task' => 'Pemasangan Kolom & Balok Lantai 1', 'urutan' => 6],
        ['nama_task' => 'Pengecoran Kolom & Balok',          'urutan' => 7],
        ['nama_task' => 'Pemasangan Bekisting Dak',          'urutan' => 8],
        ['nama_task' => 'Pengecoran Dak Lantai 1',           'urutan' => 9],
        ['nama_task' => 'Pemasangan Bata Dinding',           'urutan' => 10],
        ['nama_task' => 'Plesteran & Acian Dinding',         'urutan' => 11],
        ['nama_task' => 'Pemasangan Rangka Atap',            'urutan' => 12],
        ['nama_task' => 'Pemasangan Penutup Atap',           'urutan' => 13],
        ['nama_task' => 'Pemasangan Kusen Pintu & Jendela',  'urutan' => 14],
        ['nama_task' => 'Instalasi Listrik',                 'urutan' => 15],
        ['nama_task' => 'Instalasi Plumbing',                'urutan' => 16],
        ['nama_task' => 'Pemasangan Keramik Lantai',         'urutan' => 17],
        ['nama_task' => 'Pengecatan Dinding',                'urutan' => 18],
        ['nama_task' => 'Pemasangan Sanitasi',               'urutan' => 19],
        ['nama_task' => 'Finishing & Pembersihan Akhir',     'urutan' => 20],
    ];

    public static function generateForProyek(int $proyekId): void
    {
        foreach (self::$defaultTasks as $task) {
            ProyekTask::create([
                'proyek_id'  => $proyekId,
                'nama_task'  => $task['nama_task'],
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