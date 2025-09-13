<?php

namespace App\Exports;

use App\Models\TotalIncome;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TotalIncomeExport implements FromCollection, WithHeadings
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    public function collection()
    {
        return TotalIncome::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->with(['total_expanse', 'net_income'])
            ->get()
            ->map(function ($row) {
                return [
                    'Total Parking'      => $row->total_parking_details,
                    'Total Ticket'       => $row->total_ticket_details,
                    'Total Bantuan'      => $row->total_bantuan_details,
                    'Total Resto'        => $row->total_resto_details,
                    'Total Toilet'       => $row->total_toilet_details,
                    'Total Wahana'       => $row->total_wahana_details,
                    'Total Expanse'      => optional($row->total_expanse->first())->total_amount ?? 0,
                    'Total Gross Income' => $row->total_amount,
                    'Total Net Income'   => optional($row->net_income)->net_income ?? 0,
                    'Tanggal Dibuat'     => optional($row->created_at)->format('Y-m-d'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Total Parking',
            'Total Ticket',
            'Total Bantuan',
            'Total Resto',
            'Total Toilet',
            'Total Wahana',
            'Total Expanse',
            'Total Gross Income',
            'Total Net Income',
            'Tanggal Dibuat',
        ];
    }
}
