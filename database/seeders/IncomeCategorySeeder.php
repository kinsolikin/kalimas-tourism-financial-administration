<?php

namespace Database\Seeders;

use App\Models\Income_categori;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IncomeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Tiket',
            'Parkir',
            'resto',
            'wahana',
            'Toilet',
            'Bantuan',
        ];

        foreach ($categories as $category) {
            Income_categori::create(['name' => $category]);
        }
    }
}
