{{-- filepath: d:\skripsi sok\proyek\administrasikalimas\resources\views\exports\wahana-income-details-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Wahana Pendapatan Kalimas</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Export Wahana Pendapatan Kalimas</h2>
    <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>
    <table>
        <thead>
            <tr>
                <th>Nama Wahana</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $row)
                <tr>
                    <td>{{ $row->jenisWahana->jeniswahana ?? $row->nama_wahana ?? '-' }}</td>
                    <td>{{ number_format($row->harga, 0, ',', '.') }}</td>
                    <td>{{ $row->jumlah }}</td>
                    <td>{{ number_format($row->total, 0, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h3>Total Laba Wahana: Rp {{ number_format($totalLabaWahana, 0, ',', '.') }}</h3>
</body>
</html>