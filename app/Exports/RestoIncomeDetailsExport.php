<?php

namespace App\Exports;

use App\Models\Resto_income_details;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RestoIncomeDetailsExport implements FromCollection, WithHeadings
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
        return Resto_income_details::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get([
                'name_customer',
                'makanan',
                'minuman',
                'qty_makanan',
                'qty_minuman',
                'harga_satuan_makanan',
                'harga_satuan_minuman',
                'total',
                'created_at'
            ]);
    }

    public function headings(): array
    {
        return [
            'Nama Pembeli',
            'Makanan',
            'Minuman',
            'Jumlah Makanan',
            'Jumlah Minuman',
            'Harga Satuan Makanan',
            'Harga Satuan Minuman',
            'Total',
            'Tanggal',
        ];
    }
}
