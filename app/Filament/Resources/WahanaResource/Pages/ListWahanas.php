<?php

namespace App\Filament\Resources\WahanaResource\Pages;

use App\Filament\Resources\WahanaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWahanas extends ListRecords
{
    protected static string $resource = WahanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
