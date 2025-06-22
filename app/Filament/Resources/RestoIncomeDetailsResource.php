<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RestoIncomeDetailsResource\Pages;
use App\Filament\Resources\RestoIncomeDetailsResource\RelationManagers;
use App\Models\Resto_income_details;
use App\Models\RestoIncomeDetails;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\components\TextInput;
use Filament\Forms\components\Hidden;

use Filament\Tables\Columns\TextColumn;

class RestoIncomeDetailsResource extends Resource
{
    protected static ?string $model = Resto_income_details::class;

    protected static ?string $navigationGroup = 'Details Income';

    protected static ?string $navigationIcon = 'heroicon-o-cake';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


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
                    ->label('Nama Pembeli')->sortable()->searchable(),
                 TextColumn::make('makanan')
                    ->label('Makanan')->sortable()->searchable(),
                 TextColumn::make('minuman')
                    ->label('Minuman')->sortable()->searchable(),
                 TextColumn::make('qty_makanan')
                    ->label('Jumlah Makanan')->sortable()->searchable(),
                 TextColumn::make('qty_minuman')
                    ->label('Jumlah Minuman')->sortable()->searchable(),
                 TextColumn::make('harga_satuan_makanan')
                    ->label('Harga Satuan Makanan')->sortable()->searchable(),
                 TextColumn::make('harga_satuan_minuman')
                    ->label('Harga Satuan Minuman')->sortable()->searchable(),
                 TextColumn::make('total')
                    ->label('Total')->sortable()->searchable(),
                 TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')->sortable()->searchable(),

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
