<?php

namespace App\Filament\Resources\TicketIncomeDetailsResource\Pages;

use App\Filament\Resources\TicketIncomeDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTicketIncomeDetails extends ListRecords
{
    protected static string $resource = TicketIncomeDetailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
