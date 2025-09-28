<?php

namespace App\Exports;

use App\Models\Expanse;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpanseExport implements FromCollection, WithHeadings
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
        return Expanse::with(['user', 'kategori'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get()
            ->map(function ($item) {
                return [
                    $item->user->name ?? '-',
                    $item->kategori->nama ?? '-',
                    $item->description,
                    $item->total_amount,
                    $item->created_at->format('d M Y'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'User',
            'Kategori',
            'Keterangan',
            'Jumlah',
            'Tanggal',
        ];
    }
}

