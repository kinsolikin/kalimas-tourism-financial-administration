<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JenisKendaraanResource\Pages;
use App\Filament\Resources\JenisKendaraanResource\RelationManagers;
use App\Models\JenisKendaraan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JenisKendaraanResource extends Resource
{
    protected static ?string $model = JenisKendaraan::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = 'Setting';


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
                //
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
            'index' => Pages\ListJenisKendaraans::route('/'),
            'create' => Pages\CreateJenisKendaraan::route('/create'),
            'edit' => Pages\EditJenisKendaraan::route('/{record}/edit'),
        ];
    }    
}
