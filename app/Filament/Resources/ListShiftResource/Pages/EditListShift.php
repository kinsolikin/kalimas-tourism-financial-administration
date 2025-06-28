<?php

namespace App\Filament\Resources\ListShiftResource\Pages;

use App\Filament\Resources\ListShiftResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditListShift extends EditRecord
{
    protected static string $resource = ListShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
