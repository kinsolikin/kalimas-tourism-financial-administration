<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\TotalIncome;
use App\Models\NetIncome;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '2s';

    
    
    protected function getStats(): array
    {


        $totalgrossincome = TotalIncome::whereDate('created_at', now()->toDateString())->sum('total_amount');

        $totalyesterdayincome = TotalIncome::whereDate('created_at', Carbon::yesterday())->sum('total_amount');

        $increase = $totalgrossincome - $totalyesterdayincome;

        $chartData = [
            $totalgrossincome, // Data hari kemarin
            $totalyesterdayincome,     // Data hari ini
        ];

        $totalnetincometoday = (int) NetIncome::whereDate('created_at', Carbon::today())->value('net_income');

        $totalnetincomeyesterday = Netincome::whereDate('created_at', carbon::yesterday())->sum('net_income');

        $increasenetincome = $totalnetincometoday - $totalnetincomeyesterday;

        $chartdatanetincome = [
            $totalnetincometoday,
            $totalnetincomeyesterday
        ];

        $totalexpanse = (int) NetIncome::wheredate('created_at', carbon::today())->value('total_expense');

        $totalexpanseyesterday = NetIncome::wheredate('created_at',carbon::yesterday())->sum('total_expense');

        $increaseexpanse = $totalexpanse - $totalexpanseyesterday;
        
        $chartexpanse =[
            $totalexpanse,
            $totalexpanseyesterday
        ];



        return [


            Stat::make('Total Pendapatan Kotor Hari ini', number_format($totalgrossincome, 2))
                ->description(($increase >= 0 ? '+' : '') . number_format($increase, 2) . ' dari hari kemarin')
                ->descriptionIcon($increase >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($chartData) // Grafik dinamis berdasarkan data hari ini dan kemarin
                ->color($increase >= 0 ? 'success' : 'danger'),
                
            Stat::make('Total Pendapatan Bersih Hari ini', number_format($totalnetincometoday, 2))
                ->description(($increasenetincome >= 0 ? '+' : '') . number_format($increasenetincome, 2) . ' dari hari kemarin')
                ->descriptionIcon($increasenetincome >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($chartdatanetincome) // Grafik dinamis berdasarkan data hari ini dan kemarin
                ->color($increasenetincome >= 0 ? 'success' : 'danger'),
 
            Stat::make('Total Pengeluaran Hari ini', number_format($totalexpanse, 2))
                ->description(($increaseexpanse >= 0 ? '+' : '') . number_format($increaseexpanse, 2) . ' dari hari kemarin')
                ->descriptionIcon($increaseexpanse >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($chartexpanse) // Grafik dinamis berdasarkan data hari ini dan kemarin
                ->color($increaseexpanse >= 0 ? 'success' : 'danger'),
        ];
    }
}
