<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BantuanIncomeDetailsResource\Pages;
use App\Filament\Resources\BantuanIncomeDetailsResource\RelationManagers;
use App\Models\BantuanIncomeDetails;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Bantuan_income_details;
use Filament\Tables\Columns\TextColumn;

class BantuanIncomeDetailsResource extends Resource
{
    protected static ?string $navigationGroup = 'Detail Pendapatan';


    protected static ?string $model = Bantuan_income_details::class;

    protected static ?string $modelLabel = 'Bantuan';


    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }
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
                TextColumn::make('sumber_bantuan')
                    ->label('Sumber Bantuan')->searchable()
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->label('Keterangan')->searchable()
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
            'index' => Pages\ListBantuanIncomeDetails::route('/'),
            'create' => Pages\CreateBantuanIncomeDetails::route('/create'),
            'edit' => Pages\EditBantuanIncomeDetails::route('/{record}/edit'),
        ];
    }
}
