<?php

namespace App\Observers;

use App\Models\Parking_income_details;

class ParkingIncomeDetailObserver
{
    public function created(Parking_income_details $parking_income_details): void
    {
        $this->updateIncomeAmount($parking_income_details);
    }

    public function updated(Parking_income_details $parking_income_details): void
    {
        $this->updateIncomeAmount($parking_income_details);
    }

    public function deleted(Parking_income_details $parking_income_details): void
    {
        $income = $parking_income_details->income;

        // Update total amount terlebih dahulu
        $this->updateIncomeAmount($parking_income_details);

        // Jika tidak ada detail lagi, hapus income
        if (! $income->parkingDetail()->exists()) {
            $income->delete();
        }
    }

    private function updateIncomeAmount(Parking_income_details $parking_income_details): void
    {
        $income = $parking_income_details->income;

        if ($income) {
            // Jumlahkan ulang semua detail parking yang tersisa
            $newAmount = $income->parkingDetail()->sum('total');

            // Update ke tabel income
            $income->update(['amount' => $newAmount]);
        }
    }
}
