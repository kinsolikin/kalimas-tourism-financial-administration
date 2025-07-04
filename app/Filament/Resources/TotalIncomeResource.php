<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TotalIncomeResource\Pages;
use App\Filament\Resources\TotalIncomeResource\RelationManagers;
use App\Models\TotalIncome;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Barryvdh\DomPDF\Facade\Pdf;


class TotalIncomeResource extends Resource
{

    protected static ?string $model = TotalIncome::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';



    protected static ?string $navigationGroup = 'Income';




    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                totalIncome::with([
                    'total_expanse',
                    'net_income'
                ])
            )

            ->columns([
                //
                TextColumn::make('total_parking_details')
                    ->label('Total Parkir')->sortable()->searchable(),
                TextColumn::make('total_ticket_details')
                    ->label('Total Tiket')->sortable()->searchable(),
                TextColumn::make('total_bantuan_details')
                    ->label('Total Bantuan')->sortable()->searchable(),
                TextColumn::make('total_resto_details')
                    ->label('Total Resto')->sortable()->searchable(),
                TextColumn::make('total_toilet_details')
                    ->label('Total Toilet')->sortable()->searchable(),
                TextColumn::make('total_wahana_details')
                    ->label('Total Wahana')->sortable()->searchable(),
                TextColumn::make('total_expanse.total_amount')
                    ->label('Total Pengeluaran')->sortable()->searchable(),
                TextColumn::make('total_amount')
                    ->label('Total Pendapatan Kotor')->sortable()->searchable(),
                TextColumn::make('net_income.net_income')
                    ->label('Total Pendapatan Bersih')->sortable()->searchable(),


            ])

            ->headerActions([
                Action::make('Export')
                    ->form([
                        DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->required(),
                        \Filament\Forms\Components\Select::make('format')
                            ->label('Format')
                            ->options([
                                'excel' => 'Excel',
                                'pdf' => 'PDF',
                            ])
                            ->default('excel')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $startDate = $data['start_date'];
                        $endDate = \Carbon\Carbon::parse($data['end_date'])->endOfDay();
                        $format = $data['format'] ?? 'excel';

                        $totakhir = \App\Models\TotalIncome::whereBetween('created_at', [$startDate, $endDate])->get();

                        if ($format === 'pdf') {
                            // Pass the original Eloquent collection and date range to the view
                            $pdf = Pdf::loadView(
                                'exports.total-income-pdf',
                                [
                                    'records'   => $totakhir,
                                    'startDate' => $startDate,
                                    'endDate'   => $endDate->format('Y-m-d'),
                                ]
                            );
                            $fileName = 'TotalPemasukanKalimas.pdf';
                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->stream();
                            }, $fileName);
                        } else {
                            $filePath = storage_path('app/TotalPemasukanKalimas.xlsx');
                            $writer = \Spatie\SimpleExcel\SimpleExcelWriter::create($filePath);

                            foreach ($totakhir as $row) {
                                $writer->addRow([
                                    'Total Parking'      => 'Rp ' . number_format($row->total_parking_details ?? 0, 0, ',', '.'),
                                    'Total Ticket'       => 'Rp ' . number_format($row->total_ticket_details ?? 0, 0, ',', '.'),
                                    'Total Bantuan'      => 'Rp ' . number_format($row->total_bantuan_details ?? 0, 0, ',', '.'),
                                    'Total Resto'        => 'Rp ' . number_format($row->total_resto_details ?? 0, 0, ',', '.'),
                                    'Total Toilet'       => 'Rp ' . number_format($row->total_toilet_details ?? 0, 0, ',', '.'),
                                    'Total Wahana'       => 'Rp ' . number_format($row->total_wahana_details ?? 0, 0, ',', '.'),
                                    'Total Expanse'      => 'Rp ' . number_format(optional(optional($row)->total_expanse->first())->total_amount ?? 0, 0, ',', '.'),
                                    'Total Gross Income' => 'Rp ' . number_format($row->total_amount ?? 0, 0, ',', '.'),
                                    'Total Net Income'   => 'Rp ' . number_format(optional(optional($row)->netIncome)->net_income ?? 0, 0, ',', '.'),
                                    'Tanggal Data Dibuat' => optional($row->created_at)->format('Y-m-d') ?? '-',
                                ]);
                            }
                            return response()->download($filePath)->deleteFileAfterSend();
                        }
                    })
                    ->icon('heroicon-o-arrow-down-tray'),
            ])

            ->filters([
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
                    // ...
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public function exportToCsv()
    {
        // Ambil data dari model TotalIncome
        $data = \App\Models\TotalIncome::all();

        // Buat file CSV
        $csvData = [];
        $csvData[] = ['Parking Details', 'Total Amount']; // Header CSV
        foreach ($data as $row) {
            $csvData[] = [
                $row->total_parking_details,
                $row->total_amount,
            ];
        }

        // Konversi array ke string CSV
        $csvString = '';
        foreach ($csvData as $line) {
            $csvString .= implode(',', $line) . "\n";
        }

        // Kirim file CSV sebagai response download
        return response()->streamDownload(function () use ($csvString) {
            echo $csvString;
        }, 'total_income.csv');
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
            'index' => Pages\ListTotalIncomes::route('/'),
            'create' => Pages\CreateTotalIncome::route('/create'),
            'edit' => Pages\EditTotalIncome::route('/{record}/edit'),
        ];
    }
}
