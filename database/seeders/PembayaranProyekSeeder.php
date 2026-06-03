<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Proyek;

class PembayaranProyekSeeder extends Seeder
{
    public function run(): void
    {
        Proyek::with('detailBangun.desainRumah')
            ->whereDoesntHave('pembayaranProyek', fn($q) => $q->where('periode', 0))
            ->get()
            ->each(fn($p) => $p->generateDP());
    }
}