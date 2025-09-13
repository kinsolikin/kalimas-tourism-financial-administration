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
use Filament\Forms\Components\Select;
use App\Models\JenisKendaraan;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\TrashedFilter;

class ParkingIncomeDetailsResource extends Resource
{
    protected static ?string $navigationGroup = 'Detail Pendapatan';


    protected static ?string $model = Parking_income_details::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $modelLabel = 'Parkir';

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
                Hidden::make('user_id')->default(2),
                Select::make('jenis_kendaraan')
                    ->label('Jenis Kendaraan')
                    ->options(JenisKendaraan::pluck('namakendaraan', 'namakendaraan')->toArray())
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $harga = JenisKendaraan::where('namakendaraan', $state)->first()?->price ?? 0;
                        $set('harga_satuan', $harga);
                    }),
                TextInput::make('jumlah_kendaraan')
                    ->label('Jumlah Kendaraan')
                    ->numeric()
                    ->required()
                    ->afterStateUpdated(function ($set, $get) {
                        $set('total', (
                            $get('jumlah_kendaraan') * $get('harga_satuan')
                        ));
                    })->afterStateUpdated(function ($set, $get) {
                        // Saat field mana pun berubah, hitung ulang
                        $set('total', (
                            $get('jumlah_kendaraan') * $get('harga_satuan')
                        ));
                    }),
                TextInput::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->numeric()
                    ->required()
                    ->afterStateUpdated(function ($set, $get) {
                        $set('total', (
                            $get('jumlah_kendaraan') * $get('harga_satuan')
                        ));
                    })->afterStateUpdated(function ($set, $get) {
                        // Saat field mana pun berubah, hitung ulang
                        $set('total', (
                            $get('jumlah_kendaraan') * $get('harga_satuan')
                        ));
                    })
                    ->disabled() // agar user tidak bisa ubah manual
                    ->dehydrated(), // tetap disimpan ke database

                Hidden::make('income_id')
                    ->label('Pilih Pendapatan')
                    ->default(1) // sesuaikan nama kolom
                    ->required(),
                TextInput::make('total')
                    ->label('Total')
                    ->disabled() // Supaya user tidak bisa input manual
                    ->dehydrated() // Supaya tetap disimpan ke database
                    ->reactive()





            ]);
    }

   public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('jenisKendaraan.namakendaraan')
                ->label('Jenis Kendaraan')
                ->searchable()
                ->sortable(),

            TextColumn::make('jumlah_kendaraan')
                ->label('Jumlah Kendaraan')
                ->searchable()
                ->sortable(),

            TextColumn::make('harga_satuan')
                ->label('Harga Satuan')
                ->money('idr', true) // format IDR otomatis
                ->sortable(),

            TextColumn::make('total')
                ->label('Total')
                ->money('idr', true) // format IDR otomatis
                ->sortable()
                ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->label('Laba Total')
                            ->money('idr', true),
                    ])
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
            'index' => Pages\ListParkingIncomeDetails::route('/'),
            'create' => Pages\CreateParkingIncomeDetails::route('/create'),
            'edit' => Pages\EditParkingIncomeDetails::route('/{record}/edit'),
        ];
    }
}
