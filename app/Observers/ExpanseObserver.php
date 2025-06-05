<?php

namespace App\Observers;

use App\Models\Expanse;
use App\Models\TotalExpanse;
use App\Models\Expanse_Mendadak;
use App\Services\ExpanseService;
use App\Models\Expanse_Operasional;
class ExpanseObserver
{
    /**
     * Handle the Expanse "created" event.
     */
    public function created(Expanse $expanse): void
    {
        //
    }

    /**
     * Handle the Expanse "updated" event.
     */
    public function updated(Expanse $expanse): void
    {
        //
    }

    /**
     * Handle the Expanse "deleted" event.
     */
    public function deleted(Expanse $expanse): void
    {

        ExpanseService::syncIncomeAndExpanse(
            $expanse->user_id,
            Expanse_Mendadak::whereDate('created_at', now()->toDateString())->sum('amount'),
            Expanse_Operasional::whereDate('created_at', now()->toDateString())->sum('amount'),
        );
    }

    /**
     * Handle the Expanse "restored" event.
     */
    public function restored(Expanse $expanse): void
    {
        //
    }

    /**
     * Handle the Expanse "force deleted" event.
     */
    public function forceDeleted(Expanse $expanse): void
    {
        //
    }

  
    
    
}
