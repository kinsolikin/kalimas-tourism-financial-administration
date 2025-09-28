<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpanseResource\Pages;
use App\Filament\Resources\ExpanseResource\RelationManagers;
use App\Models\Expanse;
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

class ExpanseResource extends Resource
{
    protected static ?string $model = Expanse::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
protected static ?string $navigationGroup = 'Pengeluaran';
    protected static ?string $navigationLabel = 'Rincian Pengeluaran';



     public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('expanse_category_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->mask(fn ($state) => str_replace('.', '', $state)) // hanya angka
                    ->afterStateHydrated(function ($component, $state) {
                        // Jika ada titik, ubah ke integer
                        $component->state(str_replace('.', '', $state));
                    })
                    ->dehydrateStateUsing(fn($state) => (int)str_replace('.', '', $state)), // pastikan ke DB jadi integer
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

                        // Eager load relasi jika ada, misal 'user' atau 'kategori'
                        $records = \App\Models\Expanse::with(['user', 'kategori'])
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->get();

                        if ($format === 'pdf') {
                            $totalExpanse = $records->sum('total_amount');
                            $pdf = Pdf::loadView('exports.expanse-pdf', [
                                'records' => $records,
                                'startDate' => $startDate->format('Y-m-d'),
                                'endDate' => $endDate->format('Y-m-d'),
                                'totalExpanse' => $totalExpanse,
                            ]);
                            $filePath = storage_path('app/ExpanseExport.pdf');
                            $pdf->save($filePath);

                            if ($email) {
                                Mail::to($email)->send(new \App\Mail\ExportEmail($filePath, 'ExpanseExport.pdf'));
                            }

                            return response()->download($filePath)->deleteFileAfterSend();
                        } else {
                            $fileName = 'ExpanseExport.xlsx';
                            Excel::store(new \App\Exports\ExpanseExport($startDate, $endDate), $fileName);
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
                Tables\Columns\TextColumn::make('expanse_category.name')
                    ->numeric()
                    ->sortable()
                    ->label('Kategori pengeluaran'),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable()
                    ->label('Pengeluaran'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->getStateUsing(
                        fn($record) =>
                        $record->expanse_operasional?->description
                            ?? $record->expanse_mendadak?->description
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.')), // tampilkan ribuan
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
            'index' => Pages\ListExpanses::route('/'),
            'create' => Pages\CreateExpanse::route('/create'),
            'edit' => Pages\EditExpanse::route('/{record}/edit'),
        ];
    }
}

