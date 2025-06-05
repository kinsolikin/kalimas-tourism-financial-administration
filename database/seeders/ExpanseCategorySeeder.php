<?php

namespace Database\Seeders;

use App\Models\Expanse_category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpanseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Operasinal',
            'Mendadak'
        ];

        foreach ($categories as $category) {
            Expanse_category::create(['name' => $category]);
        }
    }
}
