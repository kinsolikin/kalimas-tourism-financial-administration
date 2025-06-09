<?php

namespace App\Filament\Resources\WahanaIncomeDetailsResource\Pages;

use App\Filament\Resources\WahanaIncomeDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWahanaIncomeDetails extends ListRecords
{
    protected static string $resource = WahanaIncomeDetailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
