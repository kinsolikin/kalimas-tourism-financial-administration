<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use App\Models\BlogPost;
use App\Models\TotalIncome;
use Flowframe\Trend\TrendValue;

class BlogPostsChart extends ChartWidget
{
    protected static ?string $heading = 'Gross income table';

    protected static ?string $pollingInterval = '2s';

    public ?string $filter = 'week';
    

    protected static ?int $sort = 2;

    protected function getData(): array


    {

        if ($this->filter === 'today') {
            return $this->getTodayData();
        } elseif ($this->filter === 'week') {
            return $this->getWeekData();
        } elseif ($this->filter === 'month') {
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
                    'label' => 'total income week',
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
                    'label' => 'total income today',
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
                    'label' => 'total income perHour',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],

            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'This week',
            'month' => 'This month',


        ];
    }
    protected function getType(): string
    {
        return 'bar';
    }

    public function getDescription(): ?string
    {
        return 'gross income graph';
    }
}
