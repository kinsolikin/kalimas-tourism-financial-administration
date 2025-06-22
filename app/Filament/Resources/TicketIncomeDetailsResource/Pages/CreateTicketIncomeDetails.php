<?php

namespace App\Filament\Resources\TicketIncomeDetailsResource\Pages;

use App\Filament\Resources\TicketIncomeDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Income;
class CreateTicketIncomeDetails extends CreateRecord
{
    protected static string $resource = TicketIncomeDetailsResource::class;

     protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Cek income hari ini berdasarkan kategori dan user
        $income = Income::where('income_categori_id', 1)
            ->where('user_id', 1)
            ->whereDate('created_at', now())
            ->first();

        if ($income) {
            // Jika sudah ada, tambahkan amount baru
            $income->amount += $data['total'] ?? 0;
            $income->save();
        } else {
            // Jika belum ada, buat baru
            $income = Income::create([
                'income_categori_id' => 1,
                'user_id' => 1,
                'amount' => $data['total'] ?? 0,
            ]);
        }

        // Pastikan field income_id pada detail terisi
        $data['income_id'] = $income->id;

        return $data;
    }
}

