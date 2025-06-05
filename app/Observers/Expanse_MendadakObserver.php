<?php

namespace App\Observers;

use App\Models\TotalIncome;
use App\Models\TotalExpanse;
use App\Models\Expanse_Mendadak;
use App\Services\ExpanseService;
use App\Models\Expanse_Operasional;

class Expanse_MendadakObserver
{
    /**
     * Handle the Expanse_Mendadak "created" event.
     */
    public function created(Expanse_Mendadak $expanse_Mendadak): void
    {
        //
    }

    /**
     * Handle the Expanse_Mendadak "updated" event.
     */
    public function updated(Expanse_Mendadak $expanse_Mendadak): void
    {
        //
    }

    /**
     * Handle the Expanse_Mendadak "deleted" event.
     */
    public function deleted(Expanse_Mendadak $expanse_Mendadak): void
    {
        
    }

    /**
     * Handle the Expanse_Mendadak "restored" event.
     */
    public function restored(Expanse_Mendadak $expanse_Mendadak): void
    {
        //
    }

    /**
     * Handle the Expanse_Mendadak "force deleted" event.
     */
    public function forceDeleted(Expanse_Mendadak $expanse_Mendadak): void
    {
        //
    }

    public function saved(Expanse_Mendadak $expanse_Mendadak)
    {
        ExpanseService::syncIncomeAndExpanse(
            $expanse_Mendadak->user_id,
            Expanse_Mendadak::whereDate('created_at', now()->toDateString())->sum('amount'),
            Expanse_Operasional::whereDate('created_at', now()->toDateString())->sum('amount'),
        );
    }
}
