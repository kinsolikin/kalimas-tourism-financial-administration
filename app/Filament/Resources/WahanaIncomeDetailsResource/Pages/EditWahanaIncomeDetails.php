<?php

namespace App\Filament\Resources\WahanaIncomeDetailsResource\Pages;

use App\Filament\Resources\WahanaIncomeDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWahanaIncomeDetails extends EditRecord
{
    protected static string $resource = WahanaIncomeDetailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
