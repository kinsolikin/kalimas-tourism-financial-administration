<?php

namespace App\Exports;

use App\Models\Wahana_income_details;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WahanaIncomeDetailsExport implements FromCollection, WithHeadings
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
        return Wahana_income_details::with('jenisWahana')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get()
            ->map(function ($item) {
                return [
                    $item->jenisWahana->jeniswahana ?? $item->nama_wahana ?? '-',
                    $item->harga,
                    $item->jumlah,
                    $item->total,
                    $item->created_at->format('d M Y'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Nama Wahana',
            'Harga',
            'Jumlah',
            'Total',
            'Tanggal',
        ];
    }
}
