<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- 1. SEEDER UNTUK ADMIN ---
        $adminUser = User::create([
            'name'         => 'Super Admin',
            'email'        => 'admin@gmail.com',
            'password'     => 'password', // Langsung teks biasa
            'role'         => 'admin',
            'phone_number' => '081111111111',
            'address'      => 'Kantor Pusat Management',
        ]);

        Admin::create([
            'user_id'     => $adminUser->id,
            'level_admin' => 'super_admin',
        ]);


        // --- 2. SEEDER UNTUK CUSTOMER (3 Akun) ---
        $customers = [
            [
                'name'    => 'Budi Customer',
                'email'   => 'customer@gmail.com',
                'phone'   => '082222222221',
                'address' => 'Jl. Perumahan Indah No. 10',
                'ktp'     => 'ktp_budi.jpg'
            ],
            [
                'name'    => 'Siti Aminah',
                'email'   => 'siti@gmail.com',
                'phone'   => '082222222222',
                'address' => 'Jl. Mawar Melati No. 5',
                'ktp'     => 'ktp_siti.jpg'
            ],
            [
                'name'    => 'Agus Pratama',
                'email'   => 'agus@gmail.com',
                'phone'   => '082222222223',
                'address' => 'Griya Asri Blok C-12',
                'ktp'     => 'ktp_agus.jpg'
            ],
        ];

        foreach ($customers as $c) {
            $user = User::create([
                'name'         => $c['name'],
                'email'        => $c['email'],
                'password'     => '12345678', // Langsung teks biasa
                'role'         => 'customer',
                'phone_number' => $c['phone'],
                'address'      => $c['address'],
            ]);

            Customer::create([
                'user_id'            => $user->id,
                'no_hp'              => $c['phone'],
                'path_file_foto_ktp' => $c['ktp'],
            ]);
        }


        // --- 3. SEEDER UNTUK MANDOR (3 Akun) ---
        $mandors = [
            [
                'name'    => 'Asep Mandor',
                'email'   => 'mandor@gmail.com',
                'phone'   => '083333333331',
                'address' => 'Bedeng Proyek A',
            ],
            [
                'name'    => 'Kurniawan Mandor',
                'email'   => 'kurnia@gmail.com',
                'phone'   => '083333333332',
                'address' => 'Mess Kontraktor B',
            ],
            [
                'name'    => 'Prayoga Mandor',
                'email'   => 'prayoga@gmail.com',
                'phone'   => '083333333333',
                'address' => 'Jl. Pembangunan No. 99',
            ],
        ];

        foreach ($mandors as $m) {
            User::create([
                'name'         => $m['name'],
                'email'        => $m['email'],
                'password'     => 'passwordmandor', // Langsung teks biasa
                'role'         => 'mandor',
                'phone_number' => $m['phone'],
                'address'      => $m['address'],
            ]);
        }
    }
}