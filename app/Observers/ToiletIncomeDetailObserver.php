<?php

namespace App\Observers;

use App\Models\Toilet_income_details;

class ToiletIncomeDetailObserver
{
    /**
     * Handle the Toilet_income_details "created" event.
     */
    public function created(Toilet_income_details $toilet_income_details): void
    {
        $this->updateIncomeAmount($toilet_income_details);
    }

    /**
     * Handle the Toilet_income_details "updated" event.
     */
    public function updated(Toilet_income_details $toilet_income_details): void
    {
        $this->updateIncomeAmount($toilet_income_details);
        
    }

    /**
     * Handle the Toilet_income_details "deleted" event.
     */
    public function deleted(Toilet_income_details $toilet_income_details): void
    {
        $income = $toilet_income_details->income;
        // Update total amount terlebih dahulu
        $this->updateIncomeAmount($toilet_income_details);
        // Jika tidak ada detail lagi, hapus income
        if(!$income->toiletDetail()->exists()){
            $income->delete();
        };
        
    }

    /**
     * Handle the Toilet_income_details "restored" event.
     */
    public function restored(Toilet_income_details $toilet_income_details): void
    {
        //
    }

    /**
     * Handle the Toilet_income_details "force deleted" event.
     */
    public function forceDeleted(Toilet_income_details $toilet_income_details): void
    {
        //
    }


    private function updateIncomeAmount(Toilet_income_details $toilet_income_details):void{

        $income  =$toilet_income_details->income;

        if($income){
            $newAmount = $income->toiletDetail()->sum('total');
        }

        $income->update(['amount' => $newAmount]);
    }
}
