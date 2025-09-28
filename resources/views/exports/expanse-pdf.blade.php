{{-- filepath: d:\skripsi sok\proyek\administrasikalimas\resources\views\exports\expanse-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Pengeluaran</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Export Pengeluaran</h2>
    <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Kategori</th>
                <th>Keterangan</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            {{-- ...existing code... --}}
            @forelse($records as $row)
                <tr>
                    <td>{{ $row->user->name ?? '-' }}</td>
                    <td>
                        {{ $row->kategori->nama ?? ($row->expanse_category->name ?? ($row->expanse_category->nama ?? '-')) }}
                    </td>
                    <td>
                        {{ $row->description ??
                            ($row->expanse_operasional->description ?? ($row->expanse_mendadak->description ?? '-')) }}
                    </td>
                    <td>{{ number_format($row->total_amount ?? $row->amount, 0, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;">Data tidak ditemukan</td>
                </tr>
            @endforelse
            {{-- ...existing code... --}}
        </tbody>
    </table>
    <h3>
        Total Pengeluaran: Rp {{
            number_format(
                $totalExpanse > 0
                    ? $totalExpanse
                    : ($records->sum('total_amount') ?: $records->sum('amount')),
                0, ',', '.'
            )
        }}
    </h3>
</body>
</html>