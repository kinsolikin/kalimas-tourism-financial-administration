<?php

namespace App\Filament\Resources\SetingTicketResource\Pages;

use App\Filament\Resources\SetingTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSetingTicket extends EditRecord
{
    protected static string $resource = SetingTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
