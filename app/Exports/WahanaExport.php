<?php

namespace App\Exports;

use App\Models\Wahana;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WahanaExport implements FromCollection, WithHeadings
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
        return Wahana::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get(['jeniswahana', 'price', 'created_at']);
    }

    public function headings(): array
    {
        return [
            'Jenis Wahana',
            'Harga',
            'Tanggal',
        ];
    }
}
