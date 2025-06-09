<?php

namespace App\Filament\Resources\TicketIncomeDetailsResource\Pages;

use App\Filament\Resources\TicketIncomeDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketIncomeDetails extends EditRecord
{
    protected static string $resource = TicketIncomeDetailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
