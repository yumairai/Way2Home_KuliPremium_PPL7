<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
            'password'     => 'password',
            'role'         => 'admin',
            'phone_number' => '081111111111',
            'address'      => 'Kantor Pusat Management',
        ]);

        Admin::create([
            'user_id'     => $adminUser->id,
            'level_admin' => 'super_admin',
        ]);


        // --- 2. SEEDER UNTUK CUSTOMER ---
        $customerUser = User::create([
            'name'         => 'Budi Customer',
            'email'        => 'customer@gmail.com',
            'password'     => '12345678',
            'role'         => 'customer',
            'phone_number' => '082222222222',
            'address'      => 'Jl. Perumahan Indah No. 10',
        ]);

        Customer::create([
            'user_id'            => $customerUser->id,
            'no_hp'              => '082222222222',
            'path_file_foto_ktp' => 'ktp_budi.jpg', // Dummy path
        ]);


        // --- 3. SEEDER UNTUK MANDOR (Opsional) ---
        // Karena di enum kamu ada 'mandor', kita buatkan akunnya juga di tabel users
        User::create([
            'name'         => 'Asep Mandor',
            'email'        => 'mandor@gmail.com',
            'password'     => 'passwordmandor',
            'role'         => 'mandor',
            'phone_number' => '083333333333',
            'address'      => 'Bedeng Proyek A',
        ]);
    }
}