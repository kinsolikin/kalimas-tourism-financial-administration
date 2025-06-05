<?php

namespace App\Observers;

use App\Models\Expanse;
use App\Models\TotalIncome;
use App\Models\TotalExpanse;
use App\Models\Expanse_Mendadak;
use App\Models\Expanse_Operasional;
use App\Services\ExpanseService;

class Expanse_OperasionalObserver
{
    /**
     * Handle the Expanse_Operasional "created" event.
     */
    public function created(Expanse_Operasional $expanse_Operasional): void
    {
        //
    }

    /**
     * Handle the Expanse_Operasional "updated" event.
     */
    public function updated(Expanse_Operasional $expanse_Operasional): void
    {
        //
    }

    /**
     * Handle the Expanse_Operasional "deleted" event.
     */
    public function deleted(Expanse_Operasional $expanse_Operasional): void
    {
        //
    }

    /**
     * Handle the Expanse_Operasional "restored" event.
     */
    public function restored(Expanse_Operasional $expanse_Operasional): void
    {
        //
    }

    /**
     * Handle the Expanse_Operasional "force deleted" event.
     */
    public function forceDeleted(Expanse_Operasional $expanse_Operasional): void
    {
        //
    }

    public function saved(Expanse_Operasional $expanse_Operasional)
    {
        ExpanseService::syncIncomeAndExpanse(
            $expanse_Operasional->user_id,
            Expanse_Mendadak::whereDate('created_at', now()->toDateString())->sum('amount'),
            Expanse_Operasional::whereDate('created_at', now()->toDateString())->sum('amount'),
        );
    }
}
