<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketIncomeDetailsResource\Pages;
use App\Filament\Resources\TicketIncomeDetailsResource\RelationManagers;
use App\Models\TicketIncomeDetails;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Ticket_income_details;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;


class TicketIncomeDetailsResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Details Income';

    protected static ?string $model = Ticket_income_details::class;



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('jumlah_orang')
                    ->label('Jumlah Orang')->searchable()
                    ->sortable(),
                TextColumn::make('harga_satuan')
                    ->label('Harga Satuan')->searchable()
                    ->sortable(),
                TextColumn::make('total')
                    ->label('Total')->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTicketIncomeDetails::route('/'),
            'create' => Pages\CreateTicketIncomeDetails::route('/create'),
            'edit' => Pages\EditTicketIncomeDetails::route('/{record}/edit'),
        ];
    }    
}
