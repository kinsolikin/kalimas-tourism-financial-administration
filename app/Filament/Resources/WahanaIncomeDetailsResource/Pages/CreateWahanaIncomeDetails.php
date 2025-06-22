<?php

namespace App\Filament\Resources\WahanaIncomeDetailsResource\Pages;

use App\Filament\Resources\WahanaIncomeDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use APP\Models\Income;

class CreateWahanaIncomeDetails extends CreateRecord
{
    protected static string $resource = WahanaIncomeDetailsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Cek income hari ini berdasarkan kategori dan user
        $income = Income::where('income_categori_id', 4)
            ->where('user_id', $data['user_id'] ?? 4) // Gunakan user_id dari data atau default ke 1
            ->whereDate('created_at', now())
            ->first();

        if ($income) {
            // Jika sudah ada, tambahkan amount baru
            $income->amount += $data['total'] ?? 0;
            $income->save();
        } else {
            // Jika belum ada, buat baru
            $income = Income::create([
                'income_categori_id' => 4,
                'user_id' => $data['user_id'] ?? 4, // Gunakan user_id dari data atau default ke 1
                'amount' => $data['total'] ?? 0,
            ]);
        }

        // Pastikan field income_id pada detail terisi
        $data['income_id'] = $income->id;

        return $data;
    }

}
