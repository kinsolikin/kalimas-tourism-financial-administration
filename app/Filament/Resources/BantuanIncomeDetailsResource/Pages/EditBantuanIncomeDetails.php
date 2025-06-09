<?php

namespace App\Filament\Resources\BantuanIncomeDetailsResource\Pages;

use App\Filament\Resources\BantuanIncomeDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBantuanIncomeDetails extends EditRecord
{
    protected static string $resource = BantuanIncomeDetailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
