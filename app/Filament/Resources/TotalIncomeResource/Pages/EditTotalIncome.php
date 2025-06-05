<?php

namespace App\Filament\Resources\TotalIncomeResource\Pages;

use App\Filament\Resources\TotalIncomeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTotalIncome extends EditRecord
{
        
    protected static string $resource = TotalIncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
