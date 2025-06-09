<?php

namespace App\Filament\Resources\BantuanIncomeDetailsResource\Pages;

use App\Filament\Resources\BantuanIncomeDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBantuanIncomeDetails extends ListRecords
{
    protected static string $resource = BantuanIncomeDetailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
