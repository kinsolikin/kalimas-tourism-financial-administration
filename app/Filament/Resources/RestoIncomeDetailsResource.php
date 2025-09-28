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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;

use Filament\Tables\Columns\TextColumn;

class RestoIncomeDetailsResource extends Resource
{
    protected static ?string $model = Resto_income_details::class;

    protected static ?string $navigationGroup = 'Detail Pendapatan';


    protected static ?string $navigationIcon = 'heroicon-o-cake';

    protected static ?string $modelLabel = 'Resto';


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
            ->headerActions([
                Action::make('Export')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->maxDate(now()),
                        \Filament\Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->required()
                            ->maxDate(now()),
                        \Filament\Forms\Components\Select::make('format')
                            ->label('Format')
                            ->options(['excel' => 'Excel', 'pdf' => 'PDF'])
                            ->default('excel')
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('email')
                            ->label('Kirim ke Email (Opsional)')
                            ->email()
                            ->placeholder('contoh@email.com'),
                    ])
                    ->action(function (array $data) {
                        $startDate = \Carbon\Carbon::parse($data['start_date'])->startOfDay();
                        $endDate   = \Carbon\Carbon::parse($data['end_date'])->endOfDay();
                        $format    = $data['format'] ?? 'excel';
                        $email     = $data['email'] ?? null;

                        if ($endDate->toDateString() > now()->toDateString()) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error')
                                ->body('Tanggal selesai tidak boleh melebihi hari ini.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $records = \App\Models\Resto_income_details::whereBetween('created_at', [$startDate, $endDate])->get();

                        if ($format === 'pdf') {
                            $totalLabaResto = $records->sum('total');
                            $pdf = Pdf::loadView('exports.resto-income-details-pdf', [
                                'records' => $records,
                                'startDate' => $startDate->format('Y-m-d'),
                                'endDate' => $endDate->format('Y-m-d'),
                                'totalLabaResto' => $totalLabaResto,
                            ]);
                            $filePath = storage_path('app/RestoPendapatanKalimas.pdf');
                            $pdf->save($filePath);

                            if ($email) {
                                Mail::to($email)->send(new \App\Mail\ExportEmail($filePath, 'RestoPendapatanKalimas.pdf'));
                            }

                            return response()->download($filePath)->deleteFileAfterSend();
                        } else {
                            $fileName = 'RestoPendapatanKalimas.xlsx';
                            Excel::store(new \App\Exports\RestoIncomeDetailsExport($startDate, $endDate), $fileName);
                            $filePath = storage_path('app/' . $fileName);

                            if ($email) {
                                Mail::to($email)->send(new \App\Mail\ExportEmail($filePath, $fileName));
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Sukses!')
                                ->body('File Excel berhasil dibuat' . ($email ? ' dan dikirim ke email.' : '.'))
                                ->success()
                                ->send();

                            return response()->download($filePath)->deleteFileAfterSend();
                        }
                    })
                    ->icon('heroicon-o-arrow-down-tray'),
            ])
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
                    ->label('Total')->sortable()->searchable()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->label('Total Laba Resto')
                            ->money('idr', true),
                    ]),
                                TextColumn::make('created_at')->date('d M Y')->label('Tanggal')->sortable()->searchable(),


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
            'index' => Pages\ListRestoIncomeDetails::route('/'),
            'create' => Pages\CreateRestoIncomeDetails::route('/create'),
            'edit' => Pages\EditRestoIncomeDetails::route('/{record}/edit'),
        ];
    }
}
