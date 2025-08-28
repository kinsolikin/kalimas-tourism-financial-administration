<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Ticket_income_details;
use Flowframe\Trend\TrendValue;
use Flowframe\Trend\Trend;
class visitors extends ChartWidget
{
    protected static ?string $heading = 'Grafik Pengunjung';

    protected static ?int $sort = 3;
    
    protected static ?string $pollingInterval = '2s';

    public ?string $filter = 'Mingguan';
    
    
    protected function getTodaydata() : array 
    
    {
        $data = Trend::model(Ticket_income_details::class)
        ->between(
            start: now()->startOfDay(),
            end: now()->endOfDay(),
        )
        ->perHour()
        ->sum('jumlah_orang');

    return [
        'datasets' => [
            [
                'label' => 'Pengunjung Hari ini',
                'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
            ],

        ],
        'labels' => $data->map(fn(TrendValue $value) => $value->date),
    ];

    }

    protected function getWeekData() : array {
        $data = Trend::model(Ticket_income_details::class)
        ->between(
            start: now()->startOfWeek(),
            end: now()->endOfWeek(),
        )
        ->perDay()
        ->sum('jumlah_orang');

    return [
        'datasets' => [
            [
                'label' => 'Pengunjung Hari ini',
                'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
            ],

        ],
        'labels' => $data->map(fn(TrendValue $value) => $value->date),
    ];

    }

    protected function getMonthData() : array 
    {
        $data = Trend::model(Ticket_income_details::class)
        ->between(
            start: now()->startOfMonth(),
            end: now()->endOfMonth(),
        )
        ->perWeek()
        ->sum('jumlah_orang');

    return [
        'datasets' => [
            [
                'label' => 'Pengunjung Minggu ini',
                'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
            ],

        ],
        'labels' => $data->map(fn(TrendValue $value) => $value->date),
    ];
}
    

    protected function getData(): array
    {
        if ($this->filter === 'Harian') {
            return $this->getTodaydata();
        } elseif ($this->filter === 'Mingguan') {
            return $this->getWeekData();
        } elseif ($this->filter === 'Bulanan') {
            return $this->getMonthData();
        }

        return [];
                  
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'Harian' => 'Hari ini',
            'Mingguan' => 'Minggu ini',
            'Bulanan' => 'Bulan ini',


        ];
    }

    public function getDescription(): ?string
    {
        return 'Total pengunung merupakan jumlah total pengunjung yang masuk ke tempat wisata pada hari ini, minggu ini, atau bulan ini. Data ini diambil dari jumlah orang yang membeli tiket masuk.';
    }
}
