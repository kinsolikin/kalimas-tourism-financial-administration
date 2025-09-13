<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\TotalIncome;
use App\Mail\IncomeReportMail;

class SendIncomeReport extends Command
{
    protected $signature = 'app:send-income-report';
    protected $description = 'Kirim laporan income setiap jam';

    public function handle()
    {
        // Ambil data terbaru (misal record terakhir)
        $income = TotalIncome::latest()->first();

        if (!$income) {
            $this->error('Tidak ada data income.');
            return;
        }

        // Kirim email pakai Mailable
        Mail::to('akbarsholikhin2@gmail.com')->send(new IncomeReportMail($income));

        $this->info('Laporan income berhasil dikirim.');
    }
}
