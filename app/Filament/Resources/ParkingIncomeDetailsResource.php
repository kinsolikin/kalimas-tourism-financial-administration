<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParkingIncomeDetailsResource\Pages;
use App\Filament\Resources\ParkingIncomeDetailsResource\RelationManagers;
use App\Models\ParkingIncomeDetails;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Parking_income_details;
use Filament\Tables\Columns\TextColumn;

class ParkingIncomeDetailsResource extends Resource
{
    protected static ?string $navigationGroup = 'Details Income';

    protected static ?string $model = Parking_income_details::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';


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
                   TextColumn::make('jenis_kendaraan')
                    ->label('Jenis Kendaraan')->searchable()
                    ->sortable(),
                TextColumn::make('jumlah_kendaraan')
                    ->label('Jumlah Kendaraan')->searchable()
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
            'index' => Pages\ListParkingIncomeDetails::route('/'),
            'create' => Pages\CreateParkingIncomeDetails::route('/create'),
            'edit' => Pages\EditParkingIncomeDetails::route('/{record}/edit'),
        ];
    }    
}
