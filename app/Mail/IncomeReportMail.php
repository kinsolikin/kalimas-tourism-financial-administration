<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\TotalIncome;

class IncomeReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $income;

    public function __construct(TotalIncome $income)
    {
        $this->income = $income;
    }

    public function build()
    {
        return $this->subject('Laporan Income Harian')
            ->markdown('emails.income_report');
    }
}
