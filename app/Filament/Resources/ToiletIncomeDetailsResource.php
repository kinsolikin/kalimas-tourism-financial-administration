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
use Filament\Forms\components\TextInput;
use Filament\Forms\Components\Hidden;

class ToiletIncomeDetailsResource extends Resource
{

    protected static ?string $navigationGroup = 'Detail Pendapatan';

    protected static ?string $model = Toilet_income_details::class;

    protected static ?string $modelLabel = 'Toilet';


    protected static ?string $navigationIcon = 'heroicon-o-users';

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
                Hidden::make('user_id')->default(5),
                TextInput::make('jumlah_pengguna')
                    ->label('Jumlah Pengguna')
                    ->afterStateUpdated(fn($set, $get) => self::hitungTotal($set, $get)),
                TextInput::make('harga_per_orang')
                    ->label('Harga per Orang')
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
                            $get('jumlah_pengguna') * $get('harga_per_orang')
                        ));
                    })
                    ->afterStateUpdated(function ($set, $get) {
                        // Saat field mana pun berubah, hitung ulang
                        $set('total', (
                            $get('jumlah_pengguna') * $get('harga_per_orang')
                        ));
                    }),




            ]);
    }

    private static function hitungTotal($set, $get)
    {
        $jumlahPengguna = $get('jumlah_pengguna') ?? 0;
        $hargaPerOrang = $get('harga_per_orang') ?? 0;

        // Hitung total
        $total = $jumlahPengguna * $hargaPerOrang;

        // Set nilai total
        $set('total', $total);

        if ($total < 0) {
            $set('total', 0); // Pastikan total tidak negatif
        }
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
                    ->sortable()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->label('Laba Total')
                            ->money('idr', true),
                    ]),

            ])
           ->filters([
            \Filament\Tables\Filters\Filter::make('created_at')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('from')
                        ->label('Tanggal Mulai'),
                    \Filament\Forms\Components\DatePicker::make('until')
                        ->label('Tanggal Selesai'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['from'],
                            fn (Builder $q, $date): Builder => $q->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['until'],
                            fn (Builder $q, $date): Builder => $q->whereDate('created_at', '<=', $date),
                        );
                }),
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
