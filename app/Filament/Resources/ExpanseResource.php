<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpanseResource\Pages;
use App\Filament\Resources\ExpanseResource\RelationManagers;
use App\Models\Expanse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpanseResource extends Resource
{
    protected static ?string $model = Expanse::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
protected static ?string $navigationGroup = 'Pengeluaran';
    protected static ?string $navigationLabel = 'Rincian Pengeluaran';



     public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('expanse_category_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('expanse_category.name')
                    ->numeric()
                    ->sortable()
                    ->label('Kategori pengeluaran'),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable()
                    ->label('Pengeluaran'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->getStateUsing(
                        fn($record) =>
                        $record->expanse_operasional?->description
                            ?? $record->expanse_mendadak?->description
                    )
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
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
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListExpanses::route('/'),
            'create' => Pages\CreateExpanse::route('/create'),
            'edit' => Pages\EditExpanse::route('/{record}/edit'),
        ];
    }
}
