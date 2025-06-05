<?php

namespace App\Observers;

use App\Models\Income;
use App\Models\TotalIncome;
use Carbon\Carbon;
class IncomeObserver
{
    /**
     * Handle the Income "created" event.
     */
    public function created(Income $income): void
    {
         // Panggil method untuk update TotalIncome setelah income ditambahkan
         $this->updateTotalIncome($income);
    }

    /**
     * Handle the Income "updated" event.
     */
    public function updated(Income $income): void
    {
          // Panggil method untuk update TotalIncome setelah income diperbarui
          $this->updateTotalIncome($income);
    }

    /**
     * Handle the Income "deleted" event.
     */
    public function deleted(Income $income): void
    {
        $this->updateTotalIncome($income);
    }

    /**
     * Handle the Income "restored" event.
     */
    public function restored(Income $income): void
    {
        //
    }

    /**
     * Handle the Income "force deleted" event.
     */
    public function forceDeleted(Income $income): void
    {
        //
    }


    private function updateTotalIncome($user)
    {

        $user = auth()->user();
        // Ambil total dari tiap kategori income berdasarkan user dan hari ini
        $ticketincome = Income::where('income_categori_id', 1)->whereDate('created_at', now()->toDateString())->sum('amount');
        $parkingincome = Income::where('income_categori_id', 2)->whereDate('created_at', now()->toDateString())->sum('amount');
        $restoincome = Income::where('income_categori_id', 3)->whereDate('created_at', now()->toDateString())->sum('amount');
        $wahanaincome = Income::where('income_categori_id', 4)->whereDate('created_at', now()->toDateString())->sum('amount');
        $toiletincome = Income::where('income_categori_id', 5)->whereDate('created_at', now()->toDateString())->sum('amount');
        $bantuanincome = Income::where('income_categori_id', 6)->whereDate('created_at', now()->toDateString())->sum('amount');

        // Hitung total income
        $totalIncome = $ticketincome + $parkingincome + $restoincome + $toiletincome + $bantuanincome + $wahanaincome;

        // Update atau buat entri baru di tabel TotalIncome berdasarkan user_id dan tanggal hari ini
        TotalIncome::updateOrCreate(
            [
                'created_at' => Carbon::now()->toDateString(),
            ],
            [
                'user_id' => $user->id,
                'total_parking_details' => $parkingincome,
                'total_ticket_details' => $ticketincome,
                'total_bantuan_details' => $bantuanincome,
                'total_resto_details' => $restoincome,
                'total_toilet_details' => $toiletincome,
                'total_wahana_details' => $wahanaincome,
                'total_amount' => $totalIncome,
            ]
        );
    }
}
