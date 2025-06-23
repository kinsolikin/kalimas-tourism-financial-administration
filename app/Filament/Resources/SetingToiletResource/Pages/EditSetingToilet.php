<?php

namespace App\Filament\Resources\SetingToiletResource\Pages;

use App\Filament\Resources\SetingToiletResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSetingToilet extends EditRecord
{
    protected static string $resource = SetingToiletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
