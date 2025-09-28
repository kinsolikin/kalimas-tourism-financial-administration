<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SetingToiletResource\Pages;
use App\Filament\Resources\SetingToiletResource\RelationManagers;
use App\Models\SetingToilet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SetingToiletResource extends Resource
{
    protected static ?string $model = SetingToilet::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $modelLabel = "Harga Toilet";

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0.00)
                    ->prefix('Rp')
            ]);
    }

    public static function canCreate(): bool
    {
        // Hanya tampilkan tombol create jika belum ada data
        return \App\Models\SetingToilet::count() < 1;
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('price')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListSetingToilets::route('/'),
            'create' => Pages\CreateSetingToilet::route('/create'),
            'edit' => Pages\EditSetingToilet::route('/{record}/edit'),
        ];
    }
}
