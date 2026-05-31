<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Mandor;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * TesterSeeder
 *
 * Mengisi akun-akun khusus untuk keperluan E2E Testing.
 * Semua akun yang dibuat di sini memiliki flag `is_tester = true` sehingga
 * middleware BypassTesterRequest akan aktif dan secara otomatis:
 *  - Mengisi field upload dokumen/foto dengan aset dummy Supabase.
 *  - Mem-bypass proses pembayaran Midtrans (simulasi langsung ke 'berhasil').
 *
 * Rincian akun:
 *  - 24 Customer tester  : tester.customer01@way2home.test — tester.customer24@way2home.test
 *  -  3 Mandor  tester   : tester.mandor01@way2home.test  — tester.mandor03@way2home.test
 *  -  3 Admin   tester   : tester.admin01@way2home.test   — tester.admin03@way2home.test
 *
 * Password semua akun  : TesterPass123!
 * (Gunakan `updateOrCreate` agar seeder aman dijalankan berulang kali.)
 */
class TesterSeeder extends Seeder
{
    /** Password tunggal yang dipakai oleh seluruh akun tester. */
    private const PASSWORD = 'TesterPass123!';

    // ──────────────────────────────────────────────────────────────────────────
    // Entry Point
    // ──────────────────────────────────────────────────────────────────────────

    public function run(): void
    {
        $this->seedCustomers();
        $this->seedMandors();
        $this->seedAdmins();

        $this->command->info('✅ TesterSeeder selesai: 24 customer, 3 mandor, 3 admin (is_tester = true).');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 1. Customer Tester (24 akun)
    // ──────────────────────────────────────────────────────────────────────────

    private function seedCustomers(): void
    {
        $customers = [
            ['no' => '01', 'name' => 'Andi Wijaya',       'phone' => '08100000001', 'address' => 'Jl. Melati No. 1, Jakarta Selatan'],
            ['no' => '02', 'name' => 'Budi Santoso',      'phone' => '08100000002', 'address' => 'Jl. Mawar No. 2, Depok'],
            ['no' => '03', 'name' => 'Citra Dewi',        'phone' => '08100000003', 'address' => 'Perum. Griya Indah Blok A-3'],
            ['no' => '04', 'name' => 'Dian Pratiwi',      'phone' => '08100000004', 'address' => 'Jl. Flamboyan No. 4, Bogor'],
            ['no' => '05', 'name' => 'Eko Kurniawan',     'phone' => '08100000005', 'address' => 'Jl. Anggrek No. 5, Bekasi'],
            ['no' => '06', 'name' => 'Fitri Handayani',   'phone' => '08100000006', 'address' => 'Jl. Kenanga No. 6, Tangerang'],
            ['no' => '07', 'name' => 'Gilang Ramadhan',   'phone' => '08100000007', 'address' => 'Jl. Dahlia No. 7, Jakarta Timur'],
            ['no' => '08', 'name' => 'Hana Pertiwi',      'phone' => '08100000008', 'address' => 'Jl. Cempaka No. 8, Jakarta Barat'],
            ['no' => '09', 'name' => 'Irwan Setiawan',    'phone' => '08100000009', 'address' => 'Jl. Seruni No. 9, Bandung'],
            ['no' => '10', 'name' => 'Joko Susilo',       'phone' => '08100000010', 'address' => 'Jl. Teratai No. 10, Surabaya'],
            ['no' => '11', 'name' => 'Kartika Sari',      'phone' => '08100000011', 'address' => 'Jl. Bougenville No. 11, Bekasi'],
            ['no' => '12', 'name' => 'Luthfi Hakim',      'phone' => '08100000012', 'address' => 'Jl. Lavender No. 12, Depok'],
            ['no' => '13', 'name' => 'Maya Anggraini',    'phone' => '08100000013', 'address' => 'Perum. Sejahtera Blok B-13'],
            ['no' => '14', 'name' => 'Nurul Hidayah',     'phone' => '08100000014', 'address' => 'Jl. Tulip No. 14, Jakarta Utara'],
            ['no' => '15', 'name' => 'Oscar Firmansyah',  'phone' => '08100000015', 'address' => 'Jl. Kamboja No. 15, Bogor'],
            ['no' => '16', 'name' => 'Putri Rahayu',      'phone' => '08100000016', 'address' => 'Jl. Sakura No. 16, Tangerang'],
            ['no' => '17', 'name' => 'Qori Oktavia',      'phone' => '08100000017', 'address' => 'Jl. Aster No. 17, Jakarta Pusat'],
            ['no' => '18', 'name' => 'Rizky Maulana',     'phone' => '08100000018', 'address' => 'Jl. Melati No. 18, Bekasi'],
            ['no' => '19', 'name' => 'Sari Wulandari',    'phone' => '08100000019', 'address' => 'Perum. Asri Blok C-19'],
            ['no' => '20', 'name' => 'Tri Wahyudi',       'phone' => '08100000020', 'address' => 'Jl. Nusa Indah No. 20, Depok'],
            ['no' => '21', 'name' => 'Ulfah Nuraini',     'phone' => '08100000021', 'address' => 'Jl. Chrysant No. 21, Bandung'],
            ['no' => '22', 'name' => 'Vino Ardiansyah',   'phone' => '08100000022', 'address' => 'Jl. Bougainville No. 22, Surabaya'],
            ['no' => '23', 'name' => 'Winda Kusuma',      'phone' => '08100000023', 'address' => 'Jl. Peony No. 23, Jakarta Selatan'],
            ['no' => '24', 'name' => 'Xavier Pratama',    'phone' => '08100000024', 'address' => 'Jl. Zinnia No. 24, Tangerang Selatan'],
        ];

        foreach ($customers as $c) {
            $email = "tester.customer{$c['no']}@way2home.test";

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name'              => $c['name'],
                    'password'          => self::PASSWORD,
                    'role'              => 'customer',
                    'phone_number'      => $c['phone'],
                    'address'           => $c['address'],
                    'email_verified_at' => now(),   // Tester tidak perlu verifikasi email
                    'is_tester'         => true,    // ← Flag utama untuk BypassTesterRequest
                    'is_first_login'    => false,   // Langsung skip onboarding
                ]
            );

            // Buat atau perbarui record di tabel customers
            Customer::updateOrCreate(
                ['user_id' => $user->id],
                ['no_hp'   => $c['phone']]
            );
        }

        $this->command->line("   → 24 customer tester dibuat.");
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 2. Mandor Tester (3 akun)
    // ──────────────────────────────────────────────────────────────────────────

    private function seedMandors(): void
    {
        // ── 3 Mandor Tester Biasa ─────────────────────────────────────────────
        $mandors = [
            [
                'no'              => '01',
                'name'            => 'Bambang Tester',
                'phone'           => '08200000001',
                'address'         => 'Bedeng Tester A, Jakarta',
                'area_kerja'      => 'Jakarta Selatan',
                'lama_pengalaman' => 7,
                'sertifikasi'     => 'SKT Madya',
            ],
            [
                'no'              => '02',
                'name'            => 'Sudarmo Tester',
                'phone'           => '08200000002',
                'address'         => 'Mess Tester B, Depok',
                'area_kerja'      => 'Depok',
                'lama_pengalaman' => 10,
                'sertifikasi'     => 'SKT Utama',
            ],
            [
                'no'              => '03',
                'name'            => 'Wahyu Tester',
                'phone'           => '08200000003',
                'address'         => 'Kontrakan Tester C, Bekasi',
                'area_kerja'      => 'Bekasi',
                'lama_pengalaman' => 5,
                'sertifikasi'     => null,
            ],
        ];

        foreach ($mandors as $m) {
            $email = "tester.mandor{$m['no']}@way2home.test";

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name'              => $m['name'],
                    'password'          => self::PASSWORD,
                    'role'              => 'mandor',
                    'phone_number'      => $m['phone'],
                    'address'           => $m['address'],
                    'email_verified_at' => now(),
                    'is_tester'         => true,
                    'is_first_login'    => false,
                ]
            );

            Mandor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'sertifikasi'      => $m['sertifikasi'],
                    'path_foto_profil' => 'https://ovyjfudrdwrlyioygotq.supabase.co/storage/v1/object/public/public-assets/testing/avatars/foto_profil_mandor.png',
                    'area_kerja'       => $m['area_kerja'],
                    'path_foto_ktp'    => null,
                    'lama_pengalaman'  => $m['lama_pengalaman'],
                    'status'           => 'aktif',
                    'rating'           => 0,
                    'is_ghost'         => false,
                ]
            );
        }

        // ── Mandor Hantu (dedicated auto-assign, tidak muncul di admin) ───────
        $ghostUser = User::updateOrCreate(
            ['email' => 'tester.mandor.ghost@way2home.test'],
            [
                'name'              => 'Mandor Ghost',
                'password'          => self::PASSWORD,
                'role'              => 'mandor',
                'phone_number'      => '08200000099',
                'address'           => 'Ghost',
                'email_verified_at' => now(),
                'is_tester'         => true,
                'is_first_login'    => false,
            ]
        );

        Mandor::updateOrCreate(
            ['user_id' => $ghostUser->id],
            [
                'sertifikasi'      => null,
                'path_foto_profil' => null,
                'area_kerja'       => 'Ghost',
                'path_foto_ktp'    => null,
                'lama_pengalaman'  => 0,
                'status'           => 'aktif',
                'rating'           => 0,
                'is_ghost'         => true,
            ]
        );

        $this->command->line("   → 3 mandor tester + 1 mandor hantu dibuat.");
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 3. Admin Tester (3 akun)
    // ──────────────────────────────────────────────────────────────────────────

    private function seedAdmins(): void
    {
        $admins = [
            [
                'no'          => '01',
                'name'        => 'Admin Tester Satu',
                'phone'       => '08300000001',
                'address'     => 'Kantor Tester, Lantai 1',
                'level_admin' => 'super_admin',
            ],
            [
                'no'          => '02',
                'name'        => 'Admin Tester Dua',
                'phone'       => '08300000002',
                'address'     => 'Kantor Tester, Lantai 2',
                'level_admin' => 'admin',
            ],
            [
                'no'          => '03',
                'name'        => 'Admin Tester Tiga',
                'phone'       => '08300000003',
                'address'     => 'Kantor Tester, Lantai 3',
                'level_admin' => 'admin',
            ],
        ];

        foreach ($admins as $a) {
            $email = "tester.admin{$a['no']}@way2home.test";

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name'              => $a['name'],
                    'password'          => self::PASSWORD,
                    'role'              => 'admin',
                    'phone_number'      => $a['phone'],
                    'address'           => $a['address'],
                    'email_verified_at' => now(),
                    'is_tester'         => true,
                    'is_first_login'    => false,
                ]
            );

            // Buat atau perbarui record di tabel admins
            Admin::updateOrCreate(
                ['user_id'    => $user->id],
                ['level_admin' => $a['level_admin']]
            );
        }

        $this->command->line("   → 3 admin tester dibuat.");
    }
}
