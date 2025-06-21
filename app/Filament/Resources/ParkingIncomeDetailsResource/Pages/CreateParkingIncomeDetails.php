<?php

namespace App\Filament\Resources\ParkingIncomeDetailsResource\Pages;

use App\Filament\Resources\ParkingIncomeDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Income;

class CreateParkingIncomeDetails extends CreateRecord
{
    protected static string $resource = ParkingIncomeDetailsResource::class;

        protected function mutateFormDataBeforeCreate(array $data): array
    {

        // Cek income hari ini berdasarkan kategori dan user
        $income = Income::where('income_categori_id', 2)
            ->where('user_id', 2)
            ->whereDate('created_at', now())
            ->first();

        if ($income) {
            // Jika sudah ada, tambahkan amount baru
            $income->amount += $data['total'] ?? 0;
            $income->save();
        } else {
            // Jika belum ada, buat baru
            $income = Income::create([
                'income_categori_id' => 2,
                'user_id' => 2,
                'amount' => $data['total'] ?? 0,
            ]);
        }

        // Pastikan field income_id pada detail terisi
        $data['income_id'] = $income->id;

        return $data;
    }

}
