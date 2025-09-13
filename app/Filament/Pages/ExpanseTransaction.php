<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\Expanse;
use Filament\Pages\Page;
use App\Models\Expanse_Mendadak;
use App\Models\Expanse_Operasional;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;


class ExpanseTransaction extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Pengeluaran';
    protected static ?string $navigationLabel = 'Input Pengeluaran';
    protected static ?string $title = 'Input Pengeluaran';
    protected static string $view = 'filament.pages.expanse-transaction';

    // property untuk form
    public $expanse_id;
    public $amount;
    public $description;
    public $user_id;

    public function mount(): void
    {
        $user = Auth::guard('admin')->user();

        if ($user?->role !== 'admin') {
            abort(403);
            // redirect()->to(TotalIncomeResource::getUrl());
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::guard('admin')->check()
            && Auth::guard('admin')->user()->role === 'admin';
    }


    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('user_id')
                ->label('User')
                ->options(\App\Models\User::pluck('name', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\Select::make('expanse_id')
                ->label('Jenis Pengeluaran')
                ->options([
                    1 => 'Operasional',
                    2 => 'Mendadak',
                ])
                ->required(),

            Forms\Components\TextInput::make('amount')
                ->label('Jumlah')
                ->required()
                ->numeric()
                ->prefix('Rp')
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    // Format angka jadi rupiah dengan titik
                    $set('amount', number_format((int) preg_replace('/\D/', '', $state), 0, ',', '.'));
                })
                ->dehydrateStateUsing(fn($state) => (int) str_replace('.', '', $state)),


            Forms\Components\Textarea::make('description')
                ->label('Deskripsi')
                ->required(),
        ];
    }

    public function submit()
    {
        $user_id = $this->user_id; // dari form
        $expanse = Expanse::where('expanse_category_id', $this->expanse_id)
            ->where('user_id', $user_id)
            ->whereDate('created_at', Carbon::today())
            ->first();

        if (!$expanse) {
            $expanse = Expanse::create([
                'expanse_category_id' => $this->expanse_id,
                'user_id' => $user_id,
                'amount' => 0,
            ]);
        }

        if ($this->expanse_id == 1) {
            Expanse_Operasional::create([
                'expanse_id' => $expanse->id,
                'user_id' => $user_id,
                'amount' => $this->amount,
                'description' => $this->description,
            ]);
        } else {
            Expanse_Mendadak::create([
                'expanse_id' => $expanse->id,
                'user_id' => $user_id,
                'amount' => $this->amount,
                'description' => $this->description,
            ]);
        }

        $expanse->increment('amount', $this->amount);

        $this->reset(['user_id', 'expanse_id', 'amount', 'description']);

        Notification::make()
            ->title('Pengeluaran berhasil disimpan!')
            ->success()
            ->send();
    }
}
