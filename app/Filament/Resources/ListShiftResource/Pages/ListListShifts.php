<?php

namespace App\Filament\Resources\ListShiftResource\Pages;

use App\Filament\Resources\ListShiftResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListListShifts extends ListRecords
{
    protected static string $resource = ListShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
