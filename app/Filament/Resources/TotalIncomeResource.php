<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\TotalIncome;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Mail;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TotalIncomeResource\Pages;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TotalIncomeExport;

class TotalIncomeResource extends Resource
{
    protected static ?string $model = TotalIncome::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $modelLabel = 'Total Pendapatan';
    protected static ?string $navigationGroup = 'Pendapatan';

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
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(TotalIncome::with(['total_expanse', 'net_income']))
            ->columns([
                TextColumn::make('total_parking_details')->label('Total Parkir')->sortable()->searchable(),
                TextColumn::make('total_ticket_details')->label('Total Tiket')->sortable()->searchable(),
                TextColumn::make('total_bantuan_details')->label('Total Bantuan')->sortable()->searchable(),
                TextColumn::make('total_resto_details')->label('Total Resto')->sortable()->searchable(),
                TextColumn::make('total_toilet_details')->label('Total Toilet')->sortable()->searchable(),
                TextColumn::make('total_wahana_details')->label('Total Wahana')->sortable()->searchable(),
                TextColumn::make('total_expanse.total_amount')->label('Total Pengeluaran')->sortable()->searchable(),
                TextColumn::make('total_amount')->label('Total Pendapatan Kotor')->sortable()->searchable(),
                TextColumn::make('net_income.net_income')
                    ->label('Total Pendapatan Bersih')
                    ->sortable()
                    ->searchable()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->label('Laba Total')
                            ->money('idr', true),
                    ]),
            ])
            ->headerActions([
                Action::make('Export')
                    ->form([
                        DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->maxDate(now()),

                        DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->required()
                            ->maxDate(now()),

                        Forms\Components\Select::make('format')
                            ->label('Format')
                            ->options(['excel' => 'Excel', 'pdf' => 'PDF'])
                            ->default('excel')
                            ->required(),

                        TextInput::make('email')
                            ->label('Kirim ke Email (Opsional)')
                            ->email()
                            ->placeholder('contoh@email.com'),
                    ])
                    ->action(function (array $data) {
                        $startDate = \Carbon\Carbon::parse($data['start_date'])->startOfDay();
                        $endDate   = \Carbon\Carbon::parse($data['end_date'])->endOfDay();
                        $format    = $data['format'] ?? 'excel';
                        $email     = $data['email'] ?? null;

                        // Validasi backend berdasarkan tanggal saja (bukan jam)
                        if ($endDate->toDateString() > now()->toDateString()) {
                            Notification::make()
                                ->title('Error')
                                ->body('Tanggal selesai tidak boleh melebihi hari ini.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $records = TotalIncome::with(['total_expanse', 'net_income'])
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->get();

                        if ($format === 'pdf') {
                            $pdf = Pdf::loadView('exports.total-income-pdf', [
                                'records' => $records,
                                'startDate' => $startDate->format('Y-m-d'),
                                'endDate' => $endDate->format('Y-m-d'),
                            ]);
                            $filePath = storage_path('app/TotalPemasukanKalimas.pdf');
                            $pdf->save($filePath);

                            if ($email) {
                                Mail::to($email)->send(new \App\Mail\ExportEmail($filePath, 'TotalPemasukanKalimas.pdf'));
                            }

                            return response()->download($filePath)->deleteFileAfterSend();
                        } else {
                            $fileName = 'TotalPemasukanKalimas.xlsx';
                            Excel::store(new TotalIncomeExport($startDate, $endDate), $fileName);
                            $filePath = storage_path('app/' . $fileName);

                            if ($email) {
                                Mail::to($email)->send(new \App\Mail\ExportEmail($filePath, $fileName));
                            }

                            Notification::make()
                                ->title('Sukses!')
                                ->body('File Excel berhasil dibuat' . ($email ? ' dan dikirim ke email.' : '.'))
                                ->success()
                                ->send();

                            return response()->download($filePath)->deleteFileAfterSend();
                        }
                    })
                    ->icon('heroicon-o-arrow-down-tray'),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')->label('Tanggal Mulai'),
                        DatePicker::make('until')->label('Tanggal Selesai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn(Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn(Builder $q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTotalIncomes::route('/'),
            'create' => Pages\CreateTotalIncome::route('/create'),
            'edit' => Pages\EditTotalIncome::route('/{record}/edit'),
        ];
    }
}
