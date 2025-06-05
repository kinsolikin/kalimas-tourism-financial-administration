<?php

namespace App\Observers;

use Carbon\Carbon;
use App\Models\NetIncome;
use App\Models\TotalIncome;
use App\Models\TotalExpanse;

class TotalExpanseObserver
{
    /**
     * Handle the TotalExpanse "created" event.
     */

     
    public function created(TotalExpanse $totalExpanse): void
    {
        //
    }

    /**
     * Handle the TotalExpanse "updated" event.
     */
    public function updated(TotalExpanse $totalExpanse): void
    {
        //
    }

    /**
     * Handle the TotalExpanse "deleted" event.
     */
    public function deleted(TotalExpanse $totalExpanse): void
    {
        //
    }

    /**
     * Handle the TotalExpanse "restored" event.
     */
    public function restored(TotalExpanse $totalExpanse): void
    {
        //
    }

    /**
     * Handle the TotalExpanse "force deleted" event.
     */
    public function forceDeleted(TotalExpanse $totalExpanse): void
    {
        //
    }

    public function saved(TotalExpanse $totalExpanse)
    {
        
        $totalIncome = TotalIncome::whereDate('created_at', now()->toDateString())
        ->sum('total_amount');
    
        $totalExpense = $totalExpanse->user->totalexpanse()->whereDate('created_at', now()->toDateString())->sum('total_amount');

        NetIncome::updateOrCreate(
            ['created_at' => now()->toDateString()],
            [
                'user_id' => $totalExpanse->user_id,
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
            ]
        );
    }
}
