<?php

namespace App\Filament\Resources\RestoIncomeDetailsResource\Pages;

use App\Filament\Resources\RestoIncomeDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRestoIncomeDetails extends EditRecord
{
    protected static string $resource = RestoIncomeDetailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
