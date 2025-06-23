<?php

namespace App\Filament\Resources\SetingToiletResource\Pages;

use App\Filament\Resources\SetingToiletResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSetingToilets extends ListRecords
{
    protected static string $resource = SetingToiletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
