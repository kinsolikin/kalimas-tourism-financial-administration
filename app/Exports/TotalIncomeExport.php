<?php

namespace App\Exports;

use App\Models\TotalIncome;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TotalIncomeExport implements FromCollection, WithHeadings
{
    protected $startDate;
    protected $endDate;

    protected $totalNetIncome = 0; // buat nampung total pendapatan bersih

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    public function collection()
    {
        $records = TotalIncome::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->with(['total_expanse', 'net_income'])
            ->get()
            ->map(function ($row) {
                $netIncome   = optional($row->net_income)->net_income ?? 0;
                $expanse     = $row->total_expanse->sum('total_amount') ?? 0;
                $labaBersih  = $netIncome - $expanse;

                // akumulasi total pendapatan bersih
                $this->totalNetIncome += $labaBersih;

                return [
                    'Total Parking'      => 'Rp ' . number_format($row->total_parking_details, 0, ',', '.'),
                    'Total Ticket'       => 'Rp ' . number_format($row->total_ticket_details, 0, ',', '.'),
                    'Total Bantuan'      => 'Rp ' . number_format($row->total_bantuan_details, 0, ',', '.'),
                    'Total Resto'        => 'Rp ' . number_format($row->total_resto_details, 0, ',', '.'),
                    'Total Toilet'       => 'Rp ' . number_format($row->total_toilet_details, 0, ',', '.'),
                    'Total Wahana'       => 'Rp ' . number_format($row->total_wahana_details, 0, ',', '.'),
                    'Total Expanse'      => 'Rp ' . number_format($expanse, 0, ',', '.'),
                    'Total Gross Income' => 'Rp ' . number_format($row->total_amount, 0, ',', '.'),
                    'Total Net Income'   => 'Rp ' . number_format($netIncome, 0, ',', '.'),
                    'Laba Bersih'        => 'Rp ' . number_format($labaBersih, 0, ',', '.'),
                    'Tanggal Dibuat'     => optional($row->created_at)->format('Y-m-d'),
                ];
            });

        // tambahin row terakhir: total laba bersih keseluruhan
        $records->push([
            'Total Parking'      => '',
            'Total Ticket'       => '',
            'Total Bantuan'      => '',
            'Total Resto'        => '',
            'Total Toilet'       => '',
            'Total Wahana'       => '',
            'Total Expanse'      => '',
            'Total Gross Income' => '',
            'Total Net Income'   => '',
            'Laba Bersih'        => 'Rp ' . number_format($this->totalNetIncome, 0, ',', '.'),
            'Tanggal Dibuat'     => 'TOTAL',
        ]);

        return $records;
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
            'Laba Bersih',
            'Tanggal Dibuat',
        ];
    }
}
