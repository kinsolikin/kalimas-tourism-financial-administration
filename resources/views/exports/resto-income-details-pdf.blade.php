{{-- filepath: d:\skripsi sok\proyek\administrasikalimas\resources\views\exports\resto-income-details-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Resto Pendapatan Kalimas</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Export Resto Pendapatan Kalimas</h2>
    <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>
    <table>
        <thead>
            <tr>
                <th>Nama Pembeli</th>
                <th>Makanan</th>
                <th>Minuman</th>
                <th>Jumlah Makanan</th>
                <th>Jumlah Minuman</th>
                <th>Harga Satuan Makanan</th>
                <th>Harga Satuan Minuman</th>
                <th>Total</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $row)
                <tr>
                    <td>{{ $row->name_customer }}</td>
                    <td>{{ $row->makanan }}</td>
                    <td>{{ $row->minuman }}</td>
                    <td>{{ $row->qty_makanan }}</td>
                    <td>{{ $row->qty_minuman }}</td>
                    <td>{{ number_format($row->harga_satuan_makanan, 0, ',', '.') }}</td>
                    <td>{{ number_format($row->harga_satuan_minuman, 0, ',', '.') }}</td>
                    <td>{{ number_format($row->total, 0, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h3>Total Laba Resto: Rp {{ number_format($totalLabaResto, 0, ',', '.') }}</h3>
</body>
</html>