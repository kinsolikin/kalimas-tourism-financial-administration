<?php

namespace App\Filament\Resources\ParkingIncomeDetailsResource\Pages;

use App\Filament\Resources\ParkingIncomeDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParkingIncomeDetails extends ListRecords
{
    protected static string $resource = ParkingIncomeDetailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
