<?php

namespace App\Filament\Resources\JenisKendaraanResource\Pages;

use App\Filament\Resources\JenisKendaraanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJenisKendaraan extends EditRecord
{
    protected static string $resource = JenisKendaraanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
