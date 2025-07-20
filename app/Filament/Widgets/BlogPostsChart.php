<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use App\Models\BlogPost;
use App\Models\TotalIncome;
use Flowframe\Trend\TrendValue;

class BlogPostsChart extends ChartWidget
{
    protected static ?string $heading = 'Tabel Penapatan Kotor';

    protected static ?string $pollingInterval = '2s';

    public ?string $filter = 'Mingguan';
    

    protected static ?int $sort = 2;

    
    protected function getData(): array


    {

        if ($this->filter === 'Harian') {
            return $this->getTodayData();
        } elseif ($this->filter === 'Mingguan') {
            return $this->getWeekData();
        } elseif ($this->filter === 'Bulanan') {
            return $this->getMonthData();
        }

        return [];
    }

    protected function getMonthData(): array
    {
        $data = Trend::model(TotalIncome::class)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perweek()
            ->sum('total_amount');

        return [
            'datasets' => [
                [
                    'label' => 'total Pendapatan Mingguan',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],

            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }


    protected function getWeekData(): array
    {
        $data = Trend::model(TotalIncome::class)
            ->between(
                start: now()->startOfWeek(),
                end: now()->endOfWeek(),
            )
            ->perDay()
            ->sum('total_amount');

        return [
            'datasets' => [
                [
                    'label' => 'total Pendaptan Hari ini',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],

            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getTodayData(): array
    {

        $data = Trend::model(TotalIncome::class)
            ->between(
                start: now()->startOfDay(),
                end: now()->endOfDay(),
            )
            ->perHour()
            ->sum('total_amount');

        return [
            'datasets' => [
                [
                    'label' => 'total Pendapatan per Jam',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],

            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'Harian' => 'Hari ini',
            'Mingguan' => 'Minggu ini',
            'Bulanan' => 'Bulan ini',


        ];
    }
    protected function getType(): string
    {
        return 'bar';
    }

    public function getDescription(): ?string
    {
        return 'Pendapatan kotor merupakan pendapatan yang belum dikurangi dengan pengeluaran.Pendapatan kotor ini mencakup dari semu loket wisat, serta dari bantuan';
    }
}
