<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Total Income</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Laporan Total Income</h2>
    <p>Tanggal: {{ $startDate }} s.d {{ $endDate }}</p>

    <table>
        <thead>
            <tr>
                <th>Total Parking</th>
                <th>Total Ticket</th>
                <th>Total Bantuan</th>
                <th>Total Resto</th>
                <th>Total Toilet</th>
                <th>Total Wahana</th>
                <th>Total Expanse</th>
                <th>Total Gross</th>
                <th>Total Net</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $data)
                <tr>
                    <td>Rp {{ number_format($data->total_parking_details ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($data->total_ticket_details ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($data->total_bantuan_details ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($data->total_resto_details ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($data->total_toilet_details ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($data->total_wahana_details ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format(optional(optional($data->total_expanse->first())->total_amount ?? 0), 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($data->total_amount ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format(optional(optional($data->netIncome)->net_income ?? 0), 0, ',', '.') }}</td>
                    <td>{{ optional($data->created_at)->format('Y-m-d') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
