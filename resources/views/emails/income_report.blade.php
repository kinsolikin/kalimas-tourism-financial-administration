@component('mail::message')
# Laporan Income

Berikut data income terbaru:

- Tiket: {{ $income->total_ticket_details }}
- Parkir: {{ $income->total_parking_details }}
- Bantuan: {{ $income->total_bantuan_details }}
- Resto: {{ $income->total_resto_details }}
- Toilet: {{ $income->total_toilet_details }}
- Wahana: {{ $income->total_wahana_details }}
- **Total: {{ $income->total_amount }}**

Terima kasih.

@endcomponent
