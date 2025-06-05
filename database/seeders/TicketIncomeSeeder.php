<?php

namespace Database\Seeders;

use App\Models\Ticket_income_details;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Income;
use App\Models\User;
use Ramsey\Uuid\Type\Decimal;

class TicketIncomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       

        $income = Income::create([
            'income_categori_id' => 1, // ID kategori Tiket
            'user_id' => 1,            // Pastikan user dengan ID 1 ada
            'amount' => 0,
        ]);

        for ($i = 0; $i < 2; $i++) {
            $jumlah = rand(1, 5);
            $harga = rand(1, 10);
            $total = $jumlah * $harga;
        
            Ticket_income_details::create([
                'income_id' => $income->id,
                'jumlah_orang' => $jumlah,
                'harga_satuan' => $harga,
                'total' => $total,
            ]);
        
            $income->increment('amount', $total);
        }
    }
}
