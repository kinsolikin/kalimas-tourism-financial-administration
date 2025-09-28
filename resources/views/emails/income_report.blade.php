@component('mail::message')
# 📊 Laporan Pendapatan Tiap Kasir Hari Ini

Halo, berikut adalah ringkasan pendapatan dan pengeluaran terbaru:

---

**Pendapatan**
- 🎟️ Tiket: Rp {{ number_format($income->total_ticket_details, 0, ',', '.') }}
- 🚗 Parkir: Rp {{ number_format($income->total_parking_details, 0, ',', '.') }}
- 🍽️ Resto: Rp {{ number_format($income->total_resto_details, 0, ',', '.') }}
- 🎢 Wahana: Rp {{ number_format($income->total_wahana_details, 0, ',', '.') }}

**Pengeluaran**
- 💸 Rp {{ number_format($income->total_expanse->sum('total_amount'), 0, ',', '.') }}

---

### 💰 Total Pemasukan Kotor  
**Rp {{ number_format($income->total_amount, 0, ',', '.') }}**

---
### 💰 Total Pemasukan Bersih  
**Rp {{ number_format($income->net_income->net_income, 0, ',', '.') }}**

---

@component('mail::button', ['url' => 'http://kalimasmenyala.my.id/admin/login'])
👉 Lihat Detail di Dashboard
@endcomponent

Terima kasih,  
**Sistem Administrasi Kalimas**
@endcomponent
