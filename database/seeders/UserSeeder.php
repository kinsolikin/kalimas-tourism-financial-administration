<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([[
            'name' => 'lokettiket',
            'email' => 'lokettiket@loket.com',
            'password' => bcrypt('lokettiket'),
            'role' => 'lokettiketparkir',
        ],[
            'name' => 'loketparkir',
            'email' => 'loketparkir@loket.com',
            'password' => bcrypt('loketparkir'),
            'role' => 'loketparkir',
        ],[
            'name' => 'loketresto',
            'email' => 'loketresto@loket.com',
            'password' => bcrypt('loketresto'),
            'role' => 'loketresto',
        ],[
            'name' => 'loketwahana',
            'email' => 'loketwahana@loket.com',
            'password' => bcrypt('loketwahana'),
            'role' => 'loketwahana',
        ],[
            'name' => 'lokettoilet',
            'email' => 'lokettoilet@loket.com',
            'password' => bcrypt('lokettoilet'),
            'role' => 'lokettoilet',
        ],[
            'name' => 'bantuan',
            'email' => 'bantuan@bantuan.com',
            'password' => bcrypt('bantuan'),
            'role' => 'bantuan',
        ]]);
       
    }
}
