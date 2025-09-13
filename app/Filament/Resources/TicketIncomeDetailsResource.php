<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketIncomeDetailsResource\Pages;
use App\Filament\Resources\TicketIncomeDetailsResource\RelationManagers;
use App\Models\TicketIncomeDetails;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Ticket_income_details;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;

class TicketIncomeDetailsResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Detail Pendapatan';


    protected static ?string $modelLabel = 'Tiket';


    protected static ?string $model = Ticket_income_details::class;

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
                Hidden::make('user_id')->default(3),
                TextInput::make('jumlah_orang')->label('Jumlah Orang')
                    ->afterStateUpdated(fn($set, $get) => self::hitungTotal($set, $get)),
                TextInput::make('harga_satuan')->label('Harga Satuan')
                    ->afterStateUpdated(fn($set, $get) => self::hitungTotal($set, $get)),
                Hidden::make('income_id')
                    ->label('Pilih Pendapatan')
                    ->default(1) // sesuaikan nama kolom
                    ->required(),
                TextInput::make('total')
                    ->label('Total')
                    ->disabled() // Supaya user tidak bisa input manual
                    ->dehydrated() // Supaya tetap disimpan ke database
                    ->reactive()
                    ->afterStateHydrated(function ($set, $get) {
                        // Saat form pertama kali dibuka
                        $set('total', (
                            $get('jumlah_orang') * $get('harga_satuan')
                        ));
                    })
                    ->afterStateUpdated(function ($set, $get) {
                        // Saat field mana pun berubah, hitung ulang
                        $set('total', (
                            $get('jumlah_orang') * $get('harga_satuan')
                        ));
                    }),




            ]);
    }

    private static function hitungTotal($set, $get)
    {
        $jumlahorang = (int) $get('jumlah_orang');
        $hargasatuan = (int) $get('harga_satuan');


        $total = ($jumlahorang * $hargasatuan);

        $set('total', $total);

        if ($get('total') !== $total) {
            $set('total', $total);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jumlah_orang')
                    ->label('Jumlah Orang')->searchable()
                    ->sortable(),
                TextColumn::make('harga_satuan')
                    ->label('Harga Satuan')->searchable()
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
            'index' => Pages\ListTicketIncomeDetails::route('/'),
            'create' => Pages\CreateTicketIncomeDetails::route('/create'),
            'edit' => Pages\EditTicketIncomeDetails::route('/{record}/edit'),
        ];
    }
}
