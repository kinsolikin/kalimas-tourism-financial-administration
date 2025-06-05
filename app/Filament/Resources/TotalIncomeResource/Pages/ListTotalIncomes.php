<?php

namespace App\Filament\Resources\TotalIncomeResource\Pages;

use App\Filament\Resources\TotalIncomeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTotalIncomes extends ListRecords

{ 
    
    protected static string $resource = TotalIncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            
        ];
    }

}
