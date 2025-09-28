<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WahanaResource\Pages;
use App\Filament\Resources\WahanaResource\RelationManagers;
use App\Models\Wahana;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class WahanaResource extends Resource
{
    protected static ?string $model = Wahana::class;


    protected static ?string $navigationIcon = 'heroicon-o-cog';


    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $modelLabel = 'Jenis wahana';


     public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('jeniswahana')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0.00)
                    ->prefix('Rp'),
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

                        $records = \App\Models\Wahana::whereBetween('created_at', [$startDate, $endDate])->get();

                        if ($format === 'pdf') {
                            $totalHarga = $records->sum('price');
                            $pdf = Pdf::loadView('exports.wahana-pdf', [
                                'records' => $records,
                                'startDate' => $startDate->format('Y-m-d'),
                                'endDate' => $endDate->format('Y-m-d'),
                                'totalHarga' => $totalHarga,
                            ]);
                            $filePath = storage_path('app/WahanaExport.pdf');
                            $pdf->save($filePath);

                            if ($email) {
                                Mail::to($email)->send(new \App\Mail\ExportEmail($filePath, 'WahanaExport.pdf'));
                            }

                            return response()->download($filePath)->deleteFileAfterSend();
                        } else {
                            $fileName = 'WahanaExport.xlsx';
                            Excel::store(new \App\Exports\WahanaExport($startDate, $endDate), $fileName);
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
                Tables\Columns\TextColumn::make('jeniswahana')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
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
            'index' => Pages\ListWahanas::route('/'),
            'create' => Pages\CreateWahana::route('/create'),
            'edit' => Pages\EditWahana::route('/{record}/edit'),
        ];
    }
}
