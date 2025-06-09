<?php

namespace App\Filament\Resources\RestoIncomeDetailsResource\Pages;

use App\Filament\Resources\RestoIncomeDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRestoIncomeDetails extends ListRecords
{
    protected static string $resource = RestoIncomeDetailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
