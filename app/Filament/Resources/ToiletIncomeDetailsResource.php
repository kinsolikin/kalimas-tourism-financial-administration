<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ToiletIncomeDetailsResource\Pages;
use App\Filament\Resources\ToiletIncomeDetailsResource\RelationManagers;
use App\Models\ToiletIncomeDetails;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Toilet_income_details;
use Filament\Tables\Columns\TextColumn;

class ToiletIncomeDetailsResource extends Resource
{
    protected static ?string $navigationGroup = 'Details Income';

    protected static ?string $model = Toilet_income_details::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

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
                    TextColumn::make('jumlah_pengguna')
                    ->label('Jumlah Pengguna')->searchable()
                    ->sortable(),
                TextColumn::make('harga_per_orang')
                    ->label('Harga per Orang')->searchable()
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
            'index' => Pages\ListToiletIncomeDetails::route('/'),
            'create' => Pages\CreateToiletIncomeDetails::route('/create'),
            'edit' => Pages\EditToiletIncomeDetails::route('/{record}/edit'),
        ];
    }    
}
