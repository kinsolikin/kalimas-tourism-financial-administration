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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;

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

                    // Eager load relasi jenisKendaraan
                    $records = \App\Models\Parking_income_details::with('jenisKendaraan')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->get();

                    if ($format === 'pdf') {
                        $totalLabaParkir = $records->sum('total');
                        $pdf = Pdf::loadView('exports.parking-income-details-pdf', [
                            'records' => $records,
                            'startDate' => $startDate->format('Y-m-d'),
                            'endDate' => $endDate->format('Y-m-d'),
                            'totalLabaParkir' => $totalLabaParkir,
                        ]);
                        $filePath = storage_path('app/ParkirPendapatanKalimas.pdf');
                        $pdf->save($filePath);

                        if ($email) {
                            Mail::to($email)->send(new \App\Mail\ExportEmail($filePath, 'ParkirPendapatanKalimas.pdf'));
                        }

                        return response()->download($filePath)->deleteFileAfterSend();
                    } else {
                        $fileName = 'ParkirPendapatanKalimas.xlsx';
                        Excel::store(new \App\Exports\ParkingIncomeDetailsExport($startDate, $endDate), $fileName);
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
                            ->label('Total Laba Parkir')
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
