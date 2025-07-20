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
            'name' => 'lokettiketparkir',
            'email' => 'lokettiketparkir@loket.com',
            'password' => bcrypt('loket'),
            'role' => 'lokettiketparkirparkir',
        ],[
            'name' => 'loketparkir',
            'email' => 'loketparkir@loket.com',
            'password' => bcrypt('loket'),
            'role' => 'loketparkir',
        ],[
            'name' => 'loketresto',
            'email' => 'loketresto@loket.com',
            'password' => bcrypt('loket'),
            'role' => 'loketresto',
        ],[
            'name' => 'loketwahana',
            'email' => 'loketwahana@loket.com',
            'password' => bcrypt('loket'),
            'role' => 'loketwahana',
        ],[
            'name' => 'lokettoilet',
            'email' => 'lokettoilet@loket.com',
            'password' => bcrypt('loket'),
            'role' => 'lokettoilet',
        ],[
            'name' => 'bantuan',
            'email' => 'bantuan@bantuan.com',
            'password' => bcrypt('bantuan'),
            'role' => 'bantuan',
        ]]);
       
    }
}
