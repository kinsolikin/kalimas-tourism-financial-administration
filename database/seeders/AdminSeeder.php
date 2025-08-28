<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'name' => 'Bendahara',
            'email' => 'Bendahara@gmail.com',
            'password' => bcrypt('admin'),
            'role' => 'admin', // pastikan ada field role
        ]);

        Admin::create([
            'name' => 'Ketua',
            'email' => 'Ketua@gmail.com',
            'password' => bcrypt('superadmin'),
            'role' => 'super_admin',
        ]);
    }
}
