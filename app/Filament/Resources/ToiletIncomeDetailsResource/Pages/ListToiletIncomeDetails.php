<?php

namespace App\Filament\Resources\ToiletIncomeDetailsResource\Pages;

use App\Filament\Resources\ToiletIncomeDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListToiletIncomeDetails extends ListRecords
{
    protected static string $resource = ToiletIncomeDetailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
