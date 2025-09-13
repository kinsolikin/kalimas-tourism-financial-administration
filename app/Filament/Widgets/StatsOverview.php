<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\TotalIncome;
use App\Models\NetIncome;
use App\Models\Ticket_income_details;
use App\Models\Parking_income_details;
use App\Models\Resto_income_details;
use App\Models\Wahana_income_details;
use Carbon\Carbon;
use Illuminate\Support\HtmlString;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 4; // Pastikan lebih besar dari widget lain

    protected static ?string $pollingInterval = '2s';

    protected function getStats(): array
    {

        // saldo laba
        $saldoLaba = NetIncome::sum('net_income');

        // Ambil tanggal 7 hari terakhir
        $dates = collect(range(0, 6))->map(fn($i) => Carbon::today()->subDays(6 - $i)->toDateString());

        // Pendapatan Kotor 7 hari terakhir
        $grossData = $dates->map(
            fn($date) =>
            TotalIncome::whereDate('created_at', $date)->sum('total_amount')
        )->toArray();

        $totalgrossincome = end($grossData);
        $totalgrossincome_yesterday = $grossData[count($grossData) - 2] ?? 0;
        $increase_gross = $totalgrossincome - $totalgrossincome_yesterday;
        $color_gross = $increase_gross >= 0 ? 'success' : 'danger';

        // Pendapatan Bersih 7 hari terakhir
        $netData = $dates->map(
            fn($date) =>
            (int) NetIncome::whereDate('created_at', $date)->value('net_income')
        )->toArray();

        $totalnetincometoday = end($netData);
        $totalnetincomeyesterday = $netData[count($netData) - 2] ?? 0;
        $increase_net = $totalnetincometoday - $totalnetincomeyesterday;
        $color_net = $increase_net >= 0 ? 'success' : 'danger';

        // Pengeluaran 7 hari terakhir
        $expanseData = $dates->map(
            fn($date) =>
            (int) NetIncome::whereDate('created_at', $date)->value('total_expense')
        )->toArray();

        $totalexpanse = end($expanseData);
        $totalexpanseyesterday = $expanseData[count($expanseData) - 2] ?? 0;
        $increase_expanse = $totalexpanse - $totalexpanseyesterday;
        $color_expanse = $increase_expanse >= 0 ? 'success' : 'danger';

        // Ambil pendapatan hari ini dari TotalIncome
        $ticketIncomeToday = TotalIncome::whereDate('created_at', Carbon::today())->value('total_ticket_details');
        $parkingIncomeToday = TotalIncome::whereDate('created_at', Carbon::today())->value('total_parking_details');
        $restoIncomeToday = TotalIncome::whereDate('created_at', Carbon::today())->value('total_resto_details');
        $wahanaIncomeToday = TotalIncome::whereDate('created_at', Carbon::today())->value('total_wahana_details');

        // Hitung total transaksi hari ini dari masing-masing tabel
        $transaksiTiket = Ticket_income_details::whereDate('created_at', Carbon::today())
            ->where('total', '!=', 0)
            ->count();
        $transaksiParkir = Parking_income_details::whereDate('created_at', Carbon::today())
            ->where('total', '!=', 0)
            ->count();
        $transaksiResto = Resto_income_details::whereDate('created_at', Carbon::today())
            ->where('total', '!=', 0)
            ->count();
        $transaksiWahana = Wahana_income_details::whereDate('created_at', Carbon::today())
            ->where('total', '!=', 0)
            ->count();

        // Hitung total transaksi hari ini dari masing-masing tabel
        $Tiketterjual = Ticket_income_details::whereDate('created_at', Carbon::today())
            ->where('jumlah_orang', '!=', 0)
            ->sum('jumlah_orang');
        $JumlahKendaraan = Parking_income_details::whereDate('created_at', Carbon::today())
            ->where('Jumlah_kendaraan', '!=', 0)
            ->sum('Jumlah_kendaraan');
        $Jumlahmakanan = Resto_income_details::whereDate('created_at', Carbon::today())
            ->where('qty_makanan', '!=', 0)
            ->sum('qty_makanan');
        $Jumlahminuman = Resto_income_details::whereDate('created_at', Carbon::today())
            ->where('qty_minuman', '!=', 0)
            ->sum('qty_minuman');
        $Jumlahwahana = Wahana_income_details::whereDate('created_at', Carbon::today())
            ->where('jumlah', '!=', 0)
            ->sum('jumlah');

        return [
            Stat::make(
                'Rincian Total Transaksi Tiket Hari ini',
                'Rp ' . number_format($ticketIncomeToday, 2)
            )
                ->description(new HtmlString("
                    <span class='text-lg font-semibold text-green-600'>
                        Transaksi: {$transaksiTiket} kali  
                    </span>
                    <br>
                    <span class='text-lg font-semibold text-green-600'>
                    Tiket Terjual: {$Tiketterjual} tiket
                    </span>
                "))
                ->color('success'),


            Stat::make('Rincian Total Transaksi Parkir Hari ini', 'Rp ' . number_format($parkingIncomeToday, 2))
                ->description(new HtmlString("
                    <span class='text-lg font-semibold text-green-600'>
                        Transaksi: {$transaksiParkir} kali  
                    </span>
                    <br>
                    <span class='text-lg font-semibold text-green-600'>
                    Jumlah Kendaraan: {$JumlahKendaraan} kendaraan
                    </span>
                "))->color('success'),

            Stat::make('Rincian Total Transaksi Resto Hari ini', 'Rp ' . number_format($restoIncomeToday, 2))
                ->description(new HtmlString("
                    <span class='text-lg font-semibold text-green-600'>
                        Transaksi: {$transaksiResto} kali
                    </span>
                    <br>
                    <span class='text-lg font-semibold text-green-600'>
                        Jumlah Makanan: {$Jumlahmakanan} porsi
                    </span>
                    <br>
                    <span class='text-lg font-semibold text-green-600'>
                        Jumlah Minuman: {$Jumlahminuman} gelas
                    </span>
                "))
                ->color('success'),

            Stat::make('Rincian Total Transaksi Wahana Hari ini', 'Rp ' . number_format($wahanaIncomeToday, 2))

                ->description(new HtmlString("
                    <span class='text-lg font-semibold text-green-600'>
                        Transaksi: {$transaksiWahana} kali
                    </span>
                    <br>
                    <span class='text-lg font-semibold text-green-600'>
                        Jumlah disewa: {$Jumlahwahana} wahana
                    </span>
                   
                "))
                ->color('success'),

            Stat::make('Total Pendapatan Kotor Hari ini', 'Rp ' . number_format($totalgrossincome, 2))
                ->description(new HtmlString("
        <span class='text-lg font-medium " . ($increase_gross >= 0 ? 'text-green-600' : 'text-red-600') . "'>
            " . ($increase_gross >= 0 ? '+' : '') . "Rp " . number_format($increase_gross, 2) . " dari hari kemarin
        </span>
    "))
                ->chart($grossData)
                ->color($color_gross),



            Stat::make('Total Pendapatan Bersih Hari ini', 'Rp ' . number_format($totalnetincometoday, 2))
                ->description(new HtmlString("
        <span class='text-lg font-medium " . ($increase_net >= 0 ? 'text-green-600' : 'text-red-600') . "'>
            " . ($increase_net >= 0 ? '+' : '') . "Rp " . number_format($increase_net, 2) . " dari hari kemarin
        </span>
    "))
                ->chart($netData)
                ->color($color_net),

            Stat::make('Total Pengeluaran Hari ini', 'Rp ' . number_format($totalexpanse, 2))
                ->description(new HtmlString("
        <span class='text-lg font-medium " . ($increase_expanse >= 0 ? 'text-green-600' : 'text-red-600') . "'>
            " . ($increase_expanse >= 0 ? '+' : '') . "Rp " . number_format($increase_expanse, 2) . " dari hari kemarin
        </span>
    "))
                ->chart($expanseData)
                ->color($color_expanse),


          Stat::make('Saldo Laba', 'Rp ' . number_format($saldoLaba, 2))
        ->description(new HtmlString("
            <span class='text-lg font-medium text-blue-600'>
                Akumulasi dari seluruh pendapatan bersih dari waktu ke waktu
            </span>
        "))
        ->color('info'),








        ];
    }
}
