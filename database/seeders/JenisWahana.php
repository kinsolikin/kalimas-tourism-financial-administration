<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Wahana;
use Carbon\Carbon;
class JenisWahana extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Wahana::insert([
            [
                'jeniswahana' => 'Kora-Kora',
                'price' => 20000,
                 'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            
            ],
            [
                'jeniswahana' => 'Bianglala',
                'price' => 15000,
                 'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'jeniswahana' => 'Komedi Putar',
                'price' => 10000,
                 'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'jeniswahana' => 'Perahu Kincir',
                'price' => 12000,
                 'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'jeniswahana' => 'Flying Fox',
                'price' => 25000,
                 'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'jeniswahana' => 'Bumper Car',
                'price' => 18000,
                 'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
           
        ]);
    }
}
