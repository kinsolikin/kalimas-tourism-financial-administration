<?php

namespace App\Filament\Resources\ToiletIncomeDetailsResource\Pages;

use App\Filament\Resources\ToiletIncomeDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Income;

class CreateToiletIncomeDetails extends CreateRecord
{
    protected static string $resource = ToiletIncomeDetailsResource::class;

    public function mutateFormDataBeforeCreate(array $data): array  
    {
        $income = Income::where('income_categori_id',5)
        ->whereDate('created_at',now())
        ->first();

        if($income){
            $income->amount+=$data['total'] ?? 0;
            $income->save();
        }else{
            $income = Income::create([
                'income_categori_id' => 5,
                'user_id' => 5, // Menggunakan ID user yang sedang login
                'amount' => $data['total'] ?? 0,
            ]);
        }

        $data['income_id'] = $income->id;
        // Lakukan modifikasi data sebelum disimpan
        return $data;
    }
}
