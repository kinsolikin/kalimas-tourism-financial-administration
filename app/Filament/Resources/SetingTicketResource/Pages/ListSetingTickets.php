<?php

namespace App\Filament\Resources\SetingTicketResource\Pages;

use App\Filament\Resources\SetingTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSetingTickets extends ListRecords
{
    protected static string $resource = SetingTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
