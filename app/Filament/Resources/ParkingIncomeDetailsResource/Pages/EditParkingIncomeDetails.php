<?php

namespace App\Filament\Resources\ParkingIncomeDetailsResource\Pages;

use App\Filament\Resources\ParkingIncomeDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParkingIncomeDetails extends EditRecord
{
    protected static string $resource = ParkingIncomeDetailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
