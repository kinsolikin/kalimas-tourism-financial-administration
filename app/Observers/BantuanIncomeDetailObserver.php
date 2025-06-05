<?php

namespace App\Observers;

use App\Models\Bantuan_income_details;

class BantuanIncomeDetailObserver
{
    /**
     * Handle the Bantuan_income_details "created" event.
     */
    public function created(Bantuan_income_details $bantuan_income_details): void
    {
        $this->updateIncomeAmount($bantuan_income_details);
    }

    /**
     * Handle the Bantuan_income_details "updated" event.
     */
    public function updated(Bantuan_income_details $bantuan_income_details): void
    {
        $this->updateIncomeAmount($bantuan_income_details);
        
    }

    /**
     * Handle the Bantuan_income_details "deleted" event.
     */
    public function deleted(Bantuan_income_details $bantuan_income_details): void
    {
        $income = $bantuan_income_details->income;

        $this->updateIncomeAmount($bantuan_income_details);

        if(!$income->donationDetail()->exists() ){
            $income->delete();
        }
    }

    /**
     * Handle the Bantuan_income_details "restored" event.
     */
    public function restored(Bantuan_income_details $bantuan_income_details): void
    {
        //
    }

    /**
     * Handle the Bantuan_income_details "force deleted" event.
     */
    public function forceDeleted(Bantuan_income_details $bantuan_income_details): void
    {
        //
    }

    private function updateIncomeAmount(Bantuan_income_details $bantuan_incomde_details): void

    {

            $income = $bantuan_incomde_details->income;

            if($income){

                $newAmount = $income->donationDetail()->sum('total');

                $income->update(['amount' => $newAmount]);
            }
    }
}
