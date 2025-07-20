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
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use App\Models\Income;

use Filament\Tables\Columns\TextColumn;

class RestoIncomeDetailsResource extends Resource
{
    protected static ?string $model = Resto_income_details::class;

    protected static ?string $navigationGroup = 'Detail Pendapatan';


    protected static ?string $navigationIcon = 'heroicon-o-cake';

    protected static ?string $modelLabel = 'Resto';


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function form(Form $form): Form

    {

        return $form
            ->schema([
                //
                Hidden::make('user_id')->default(3),
                TextInput::make('name_customer')->label('Nama Pembeli'),
                TextInput::make('makanan')->label('Makanan'),
                TextInput::make('minuman')->label('Minuman'),
                TextInput::make('qty_makanan')->label('Jumlah Makanan')->afterStateUpdated(fn($set, $get) => self::hitungTotal($set, $get)),
                TextInput::make('qty_minuman')->label('Jumlah Minuman')->afterStateUpdated(fn($set, $get) => self::hitungTotal($set, $get)),
                TextInput::make('harga_satuan_makanan')->label('Harga Satuan Makanan')->afterStateUpdated(fn($set, $get) => self::hitungTotal($set, $get)),
                TextInput::make('harga_satuan_minuman')->label('Harga Satuan Minuman')->afterStateUpdated(fn($set, $get) => self::hitungTotal($set, $get)),
                Hidden::make('income_id')
                    ->label('Pilih Pendapatan')
                    ->default(3) // sesuaikan nama kolom
                    ->required(),
                TextInput::make('total')
                    ->label('Total')
                    ->disabled() // Supaya user tidak bisa input manual
                    ->dehydrated() // Supaya tetap disimpan ke database
                    ->reactive()
                    ->afterStateHydrated(function ($set, $get) {
                        // Saat form pertama kali dibuka
                        $set('total', (
                            $get('qty_makanan') * $get('harga_satuan_makanan') +
                            $get('qty_minuman') * $get('harga_satuan_minuman')
                        ));
                    })
                    ->afterStateUpdated(function ($set, $get) {
                        // Saat field mana pun berubah, hitung ulang
                        $set('total', (
                            $get('qty_makanan') * $get('harga_satuan_makanan') +
                            $get('qty_minuman') * $get('harga_satuan_minuman')
                        ));
                    }),


            ]);
    }

    private static function hitungTotal($set, $get)
    {
        $qtyMakanan = (int) $get('qty_makanan');
        $hargaMakanan = (int) $get('harga_satuan_makanan');
        $qtyMinuman = (int) $get('qty_minuman');
        $hargaMinuman = (int) $get('harga_satuan_minuman');

        $total = ($qtyMakanan * $hargaMakanan) + ($qtyMinuman * $hargaMinuman);

        $set('total', $total);

        if ($get('total') !== $total) {
            $set('total', $total);
        }
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
