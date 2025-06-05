<?php

namespace App\Observers;

use App\Models\Wahana_income_details;

class WahanaIncomeDetailObserver
{
    /**
     * Handle the Wahana_income_details "created" event.
     */
    public function created(Wahana_income_details $wahana_income_details): void
    {
        $this->updateIncomeAmount($wahana_income_details);
    }

    /**
     * Handle the Wahana_income_details "updated" event.
     */
    public function updated(Wahana_income_details $wahana_income_details): void
    {
        //panggil function untuk update total amount
    
        $this->UpdateIncomeAmount($wahana_income_details);
    }

    /**
     * Handle the Wahana_income_details "deleted" event.
     */
    public function deleted(Wahana_income_details $wahana_income_details): void
    {
         $income = $wahana_income_details->income;

        // Update total amount terlebih dahulu
        $this->updateIncomeAmount($wahana_income_details);

        // Jika tidak ada detail lagi, hapus income
        if (! $income->wahanaDetail()->exists()) {
            $income->delete();
        }
    }

    /**
     * Handle the Wahana_income_details "restored" event.
     */
    public function restored(Wahana_income_details $wahana_income_details): void
    {
        //
    }

    /**
     * Handle the Wahana_income_details "force deleted" event.
     */
    public function forceDeleted(Wahana_income_details $wahana_income_details): void
    {
        //
    }

    private function updateIncomeAmount(Wahana_income_details $wahana_income_details):void
    {
        $income = $wahana_income_details->income;

        
        if($income){
            // Jumlahkan ulang semua detail wahana yang tersisa
            $newAmount = $income->wahanaDetail()->sum('total');

            // Update ke tabel income
            $income->update(['amount' => $newAmount]);
        }

    }
}
