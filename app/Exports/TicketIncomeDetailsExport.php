<?php

namespace App\Exports;

use App\Models\Ticket_income_details;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TicketIncomeDetailsExport implements FromCollection, WithHeadings
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
        return Ticket_income_details::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get(['jumlah_orang', 'harga_satuan', 'total', 'created_at']);
    }

    public function headings(): array
    {
        return [
            'Jumlah Orang',
            'Harga Satuan',
            'Total',
            'Tanggal',
        ];
    }
}
