<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use App\Notifications\NetIncome;
use App\Models\NetIncome as NetIncomeModel;
use Carbon\Carbon;

class sendNetIncome extends Command

{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-net-income';

    /**
     * The console command description.
     *
     * @var string
     * 
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // total income yang ingin dikirim

        $admin = Admin::first(); 
        // ambil admin pertama (karena hanya satu)
        $total = NetIncomeModel::where('created_at', Carbon::now()->subHour())
            ->sum('net_income'); // ambil total income dalam 1 jam terakhir
           
        if ($admin) {
            $admin->notify(new NetIncome($total));
        }
    }
}
