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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;

use App\Models\Wahana_income_details;

class WahanaIncomeDetailsResource extends Resource
{
    protected static ?string $navigationGroup = 'Details Income';


    protected static ?string $model = Wahana_income_details::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')->default(4),
                TextInput::make('nama_wahana')
                    ->label('Nama Wahana')
                    ->required()
                    ->maxLength(255),
                TextInput::make('harga')
                    ->label('Harga')
                    ->numeric()
                    ->required()
                    ->afterStateUpdated(fn($set, $get) => self::hitungTotal($set, $get)),
                TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->required()
                    ->afterStateUpdated(fn($set, $get) => self::hitungTotal($set, $get)),
                Hidden::make('income_id')
                    ->label('Pilih Pendapatan')
                    ->default(5) // sesuaikan nama kolom
                    ->required(),
                TextInput::make('total')
                    ->label('Total')
                    ->disabled() // Supaya user tidak bisa input manual
                    ->dehydrated() // Supaya tetap disimpan ke database
                    ->reactive()
                    ->afterStateHydrated(function ($set, $get) {
                        // Saat form pertama kali dibuka
                        $set('total', (
                            $get('harga') * $get('jumlah')
                        ));
                    })
                    ->afterStateUpdated(function ($set, $get) {
                        // Saat field mana pun berubah, hitung ulang
                        $set('total', (
                            $get('harga') * $get('jumlah')
                        ));
                    }),
            ]);
    }

    private static function hitungTotal($set, $get)
    {
        $harga = $get('harga') ?? 0;
        $jumlah = $get('jumlah') ?? 0;

        // Hitung total
        $total = $harga * $jumlah;

        // Set nilai total
        $set('total', $total);
    }
    public static function table(Table $table): Table
    {

        return $table

            ->columns([
                TextColumn::make('jenisWahana.jeniswahana')
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
