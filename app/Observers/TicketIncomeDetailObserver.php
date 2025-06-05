<?php

namespace App\Observers;

use App\Models\Ticket_income_details;

class TicketIncomeDetailObserver
{
    public function created(Ticket_income_details $ticket_income_details): void
    {
        $this->updateIncomeAmount($ticket_income_details);
    }

    public function updated(Ticket_income_details $ticket_income_details): void
    {
        $this->updateIncomeAmount($ticket_income_details);
    }

    public function deleted(Ticket_income_details $ticket_income_details): void
    {
        $income = $ticket_income_details->income;

        // Update total amount terlebih dahulu
        $this->updateIncomeAmount($ticket_income_details);

        // Jika tidak ada lagi detail ticket untuk income ini, hapus income-nya
        if (! $income->ticketDetail()->exists()) {
            $income->delete();
        }
    }

    private function updateIncomeAmount(Ticket_income_details $ticket_income_details): void
    {
        $income = $ticket_income_details->income;

        if ($income) {
            // Jumlahkan ulang semua detail ticket yang tersisa
            $newAmount = $income->ticketDetail()->sum('total');

            // Update ke tabel income
            $income->update(['amount' => $newAmount]);
        }
    }
}
