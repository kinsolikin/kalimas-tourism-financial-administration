<?php

namespace App\Observers;

use App\Models\Resto_income_details;

class RestoIncomeDetailObserver
{
    /**
     * Handle the Resto_income_details "created" event.
     */
    public function created(Resto_income_details $resto_income_details): void
    {
        $this->updateIncomeAmount($resto_income_details);
    }

    /**
     * Handle the Resto_income_details "updated" event.
     */
    public function updated(Resto_income_details $resto_income_details): void
    {
        $this->updateIncomeAmount($resto_income_details);
    }

    /**
     * Handle the Resto_income_details "deleted" event.
     */
    public function deleted(Resto_income_details $resto_income_details): void
    {
        $income = $resto_income_details->income;

        // Update total amount terlebih dahulu
        $this->updateIncomeAmount($resto_income_details);

        // Jika tidak ada detail lagi, hapus income
        if (! $income->restoDetail()->exists()) {
            $income->delete();
        }
    }

    /**
     * Handle the Resto_income_details "restored" event.
     */
    public function restored(Resto_income_details $resto_income_details): void
    {
        //
    }

    /**
     * Handle the Resto_income_details "force deleted" event.
     */
    public function forceDeleted(Resto_income_details $resto_income_details): void
    {
        //
    }

    private function updateIncomeAmount(Resto_income_details $resto_income_details): void
    {
        $income = $resto_income_details->income;

        if ($income) {
            // Jumlahkan ulang semua detail parking yang tersisa
            $newAmount = $income->restoDetail()->sum('total');

            // Update ke tabel income
            $income->update(['amount' => $newAmount]);
        }
    }
}
