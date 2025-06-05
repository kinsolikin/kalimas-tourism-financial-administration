<?php

namespace App\Filament\Resources\TotalIncomeResource\Pages;

use App\Filament\Resources\TotalIncomeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTotalIncome extends CreateRecord
{
    
    protected static string $resource = TotalIncomeResource::class;

 
    protected function getHeaderActions(): array
    {
        return [
            // ...
        ];
    }
}
