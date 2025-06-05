<?php

namespace App\Observers;

use App\Models\TotalIncome;
use App\Models\NetIncome;
use App\Models\TotalExpanse;
use Carbon\Carbon;

class TotalIncomeObserver
{
    /**
     * Handle the TotalIncome "created" event.
     */
    public function created(TotalIncome $totalIncome): void
    {
        //
    }

    /**
     * Handle the TotalIncome "updated" event.
     */
    public function updated(TotalIncome $totalIncome): void
    {
        //
    }

    /**
     * Handle the TotalIncome "deleted" event.
     */
    public function deleted(TotalIncome $totalIncome): void
    {
        //
    }

    /**
     * Handle the TotalIncome "restored" event.
     */
    public function restored(TotalIncome $totalIncome): void
    {
        //
    }

    /**
     * Handle the TotalIncome "force deleted" event.
     */
    public function forceDeleted(TotalIncome $totalIncome): void
    {
        //
    }

    public function saved(TotalIncome $totalIncome)
    {

        $totalIncomeToday = $totalIncome->user->totalIncomes()->whereDate('created_at', now()->toDateString())->sum('total_amount');
        $totalExpenseToday = TotalExpanse::whereDate('created_at', now()->toDateString())->sum('total_amount');
        $totalincomeid = TotalIncome::whereDate('created_at', now()->toDateString())->first()->value('id');

        NetIncome::updateOrCreate(
            ['created_at' => now()->toDateString()],
            [
                'user_id' => $totalIncome->user_id,
                'total_income_id' => $totalincomeid,
                'total_income' => $totalIncomeToday,
                'total_expense' => $totalExpenseToday,
            ]
        );
    }
}
