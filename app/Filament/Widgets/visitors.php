<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Ticket_income_details;
use Flowframe\Trend\TrendValue;
use Flowframe\Trend\Trend;
class visitors extends ChartWidget
{
    protected static ?string $heading = 'Graph Visitors';

    protected static ?int $sort = 3;
    
    protected static ?string $pollingInterval = '2s';

    public ?string $filter = 'week';
    
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
                'label' => 'total visitors today',
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
                'label' => 'total visitors Week',
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
                'label' => 'total visitors Week',
                'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
            ],

        ],
        'labels' => $data->map(fn(TrendValue $value) => $value->date),
    ];
}
    

    protected function getData(): array
    {
        if ($this->filter === 'today') {
            return $this->getTodaydata();
        } elseif ($this->filter === 'week') {
            return $this->getWeekData();
        } elseif ($this->filter === 'month') {
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
            'today' => 'Today',
            'week' => 'This week',
            'month' => 'This month',


        ];
    }

    public function getDescription(): ?string
    {
        return 'total visitor graph taken from ticket purchases';
    }
}
