<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use App\Models\NetIncome as NetIncomeModel;
use App\Models\Resto_income_details;
use App\Models\Parking_income_details;
use App\Models\Ticket_income_details;
use App\Models\Wahana_income_details;
use App\Models\Toilet_income_details;
use App\Models\Bantuan_income_details;
use App\Notifications\NetIncome as NetIncomeNotification;
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

        // ambil admin pertama (karena hanya satu)
        $total = NetIncomeModel::whereDate('created_at', Carbon::now())
        ->sum('net_income'); // ambil total income dalam 1 jam terakhir
        $admin = Admin::first(); 
           
        $resto = Resto_income_details::whereDate('created_at',Carbon::now())->sum('total'); 
        $parking  = Parking_income_details::whereDate('created_at',Carbon::now())->sum('total');
        $ticket = Ticket_income_details::whereDate('created_at',Carbon::now())->sum('total');
        $wahana = Wahana_income_details::whereDate('created_at',Carbon::now())->sum('total');
        $toilet = Toilet_income_details::whereDate('created_at',Carbon::now())->sum('total');
        $bantuan = Bantuan_income_details::whereDate('created_at',Carbon::now())->sum('total');

        if ($admin) {
            $admin->notify(new NetIncomeNotification([
                'total'=> $total,
                'resto' => $resto,
                'parking' => $parking,
                'ticket' => $ticket,
                'wahana' => $wahana,
                'toilet' => $toilet,
                'bantuan' => $bantuan,
            ]));
        }
    }
}
