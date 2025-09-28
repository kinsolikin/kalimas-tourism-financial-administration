{{-- filepath: d:\skripsi sok\proyek\administrasikalimas\resources\views\exports\ticket-income-details-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Tiket Pendapatan Kalimas</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Export Tiket Pendapatan Kalimas</h2>
    <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>
    <table>
        <thead>
            <tr>
                <th>Jumlah Orang</th>
                <th>Harga Satuan</th>
                <th>Total</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $row)
                <tr>
                    <td>{{ $row->jumlah_orang }}</td>
                    <td>{{ number_format($row->harga_satuan, 0, ',', '.') }}</td>
                    <td>{{ number_format($row->total, 0, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h3>Total Laba Tiket: Rp {{ number_format($totalLabaTiket, 0, ',', '.') }}</h3>
</body>
</html>