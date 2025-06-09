<?php

namespace App\Filament\Resources\ToiletIncomeDetailsResource\Pages;

use App\Filament\Resources\ToiletIncomeDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditToiletIncomeDetails extends EditRecord
{
    protected static string $resource = ToiletIncomeDetailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
