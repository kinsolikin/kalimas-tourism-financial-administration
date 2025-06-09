<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WahanaIncomeDetailsResource\Pages;
use App\Filament\Resources\WahanaIncomeDetailsResource\RelationManagers;
use App\Models\WahanaIncomeDetails;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

use App\Models\Wahana_income_details;

class WahanaIncomeDetailsResource extends Resource
{
    protected static ?string $navigationGroup = 'Details Income';


    protected static ?string $model = Wahana_income_details::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

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
                  TextColumn::make('nama_wahana')
                    ->label('Nama Wahana')->searchable()
                    ->sortable(),
                TextColumn::make('harga')
                    ->label('Harga')->searchable()
                    ->sortable(),
                TextColumn::make('jumlah')
                    ->label('Jumlah')->searchable()
                    ->sortable(),
                TextColumn::make('total')
                    ->label('total')->searchable()
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
            'index' => Pages\ListWahanaIncomeDetails::route('/'),
            'create' => Pages\CreateWahanaIncomeDetails::route('/create'),
            'edit' => Pages\EditWahanaIncomeDetails::route('/{record}/edit'),
        ];
    }    
}
