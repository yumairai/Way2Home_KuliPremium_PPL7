<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TesterSeeder::class,         // ← Akun E2E testing (is_tester = true)
            MaterialSeeder::class,
            DesainRumahSeeder::class,
            MandorSeeder::class,
            ProyekSeeder::class,
            DetailProyekBangunSeeder::class,
            DokumenProyekSeeder::class,
            ProyekMilestoneSeeder::class,
            PembayaranProyekSeeder::class,
            TesterDataSeeder::class,
        ]);
    }
}