<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RestoIncomeDetailsResource\Pages;
use App\Filament\Resources\RestoIncomeDetailsResource\RelationManagers;
use App\Models\RestoIncomeDetails;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Resto_income_details;
use Filament\Tables\Columns\TextColumn;

class RestoIncomeDetailsResource extends Resource
{
    protected static ?string $navigationGroup = 'Details Income';

    protected static ?string $model = Resto_income_details::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';

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
                TextColumn::make('name_customer')
                    ->label('Nama Customer')->searchable()
                    ->sortable(),
                TextColumn::make('makanan')
                    ->label('Makanan')->searchable()
                    ->sortable(),
                TextColumn::make('qty_makanan')
                    ->label('Qty Makanan')->searchable()
                    ->sortable(),
                TextColumn::make('minuman')
                    ->label('Minuman')->searchable()
                    ->sortable(),
                TextColumn::make('qty_minuman')
                    ->label('Qty Minuman')->searchable()
                    ->sortable(),
                TextColumn::make('harga_satuan_makanan')
                    ->label('Harga Satuan Makanan')->searchable()
                    ->sortable(),
                TextColumn::make('harga_satuan_minuman')
                    ->label('Harga Satuan Minuman')->searchable()
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
            'index' => Pages\ListRestoIncomeDetails::route('/'),
            'create' => Pages\CreateRestoIncomeDetails::route('/create'),
            'edit' => Pages\EditRestoIncomeDetails::route('/{record}/edit'),
        ];
    }
}
