<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. ADMIN ---
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'         => 'Super Admin',
                'password'     => 'passwordadmin',
                'role'         => 'admin',
                'phone_number' => '081111111111',
                'address'      => 'Kantor Pusat Management',
                'email_verified_at' => now(), // Admin tidak perlu verifikasi email
            ]
        );

        Admin::updateOrCreate(
            ['user_id' => $adminUser->id],
            ['level_admin' => 'super_admin']
        );


        // --- 2. CUSTOMER (MULTIPLE) ---
        $customers = [
            [
                'name'    => 'Budi Customer',
                'email'   => 'customer@gmail.com',
                'phone'   => '082222222221',
                'address' => 'Jl. Perumahan Indah No. 10'
            ],
            [
                'name'    => 'Siti Aminah',
                'email'   => 'siti@gmail.com',
                'phone'   => '082222222222',
                'address' => 'Jl. Mawar Melati No. 5'
            ],
            [
                'name'    => 'Agus Pratama',
                'email'   => 'agus@gmail.com',
                'phone'   => '082222222223',
                'address' => 'Griya Asri Blok C-12'
            ],
        ];

        foreach ($customers as $c) {
            $user = User::updateOrCreate(
                ['email' => $c['email']],
                [
                    'name'              => $c['name'],
                    'password'          => 'passwordcustomer',
                    'role'              => 'customer',
                    'phone_number'      => $c['phone'],
                    'address'           => $c['address'],
                    'email_verified_at' => now(), // Customer seed otomatis verified
                ]
            );

            Customer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'no_hp'              => $c['phone'],
                ]
            );
        }


        // --- 3. MANDOR (MULTIPLE) ---
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
            User::updateOrCreate(
                ['email' => $m['email']],
                [
                    'name'         => $m['name'],
                    'password'     => 'passwordmandor',
                    'role'         => 'mandor',
                    'phone_number' => $m['phone'],
                    'address'      => $m['address'],
                    'email_verified_at' => now(), // Mandor tidak perlu verifikasi email
                ]
            );
        }
    }
}