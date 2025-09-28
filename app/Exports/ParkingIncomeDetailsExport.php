<?php

namespace App\Exports;

use App\Models\Parking_income_details;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ParkingIncomeDetailsExport implements FromCollection, WithHeadings
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
        return Parking_income_details::with('jenisKendaraan')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get()
            ->map(function ($item) {
                return [
                    $item->jenisKendaraan->namakendaraan ?? '-',
                    $item->jumlah_kendaraan,
                    $item->harga_satuan,
                    $item->total,
                    $item->created_at->format('d M Y'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Jenis Kendaraan',
            'Jumlah Kendaraan',
            'Harga Satuan',
            'Total',
            'Tanggal',
        ];
    }
}
