import React, { useEffect, useState } from "react";
import { Bar, Line } from "react-chartjs-2";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    LineElement, // tambahkan ini
    PointElement, // tambahkan ini
    Title,
    Tooltip,
    Legend,
} from "chart.js";

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    LineElement, // tambahkan ini
    PointElement, // tambahkan ini
    Title,
    Tooltip,
    Legend
);

function Dashboardguest() {
    const [data, setData] = useState({
        Harian: 0,
        PengunjungBulanan: 0,
        Incomes: [],
        PengunjungHarian: [], // Tambahkan ini
    });

    useEffect(() => {
        const fetchData = () => {
            fetch("/api/dashboard-data")
                .then((res) => res.json())
                .then((json) => setData(json));
        };

        fetchData();
        // Hapus interval update otomatis
        // const interval = setInterval(fetchData, 5000);
        // return () => clearInterval(interval);
    }, []);

     const total = Object.values(data.Incomes).reduce((sum, val) => sum + parseInt(val, 10), 0);

    console.log(data);
    const fasilitas = [
        {
            icon: "🌉",
            title: "Jembatan Ikonik",
            desc: "Spot foto favorit pengunjung.",
        },
        {
            icon: "🚤",
            title: "Perahu Wisata",
            desc: "Keliling Kalimas dengan perahu.",
        },
        {
            icon: "🍽️",
            title: "Kuliner Lokal",
            desc: "Nikmati makanan khas Surabaya.",
        },
        {
            icon: "🎶",
            title: "Live Music",
            desc: "Hiburan musik setiap akhir pekan.",
        },
    ];

    const chartHarian = {
        labels: ["Hari Ini"],
        datasets: [
            {
                label: "Pemasukan Harian",
                data: [data.Harian],
                backgroundColor: "rgba(59, 130, 246, 0.7)",
                borderColor: "rgba(59, 130, 246, 1)",
                borderWidth: 2,
            },
        ],
    };
    // Untuk chart pemasukan bulanan fluktuasi
    const bulananArray = Array.isArray(data.Bulanan) ? data.Bulanan : [];
    const chartBulanan = {
        labels: bulananArray.map(item => item.tanggal ?? item.bulan ?? ""), // gunakan tanggal/bulan
        datasets: [
            {
                label: "Pemasukan Bulanan",
                data: bulananArray.map(item => item.total ?? item.jumlah ?? 0),
                backgroundColor: "rgba(96, 165, 250, 0.2)",
                borderColor: "rgba(96, 165, 250, 1)",
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: "rgba(96, 165, 250, 1)",
                pointRadius: 4,
            },
        ],
    };

    // Untuk chart pengunjung harian (fluktuasi)
    const pengunjungHarianTerakhir = Array.isArray(data.PengunjungBulanan) ? data.PengunjungBulanan.slice(-14) : [];
    const chartPengunjung = {
        labels: pengunjungHarianTerakhir.map(item => item.tanggal),
        datasets: [
            {
                label: "Jumlah Pengunjung",
                data: pengunjungHarianTerakhir.map(item => item.total ?? item.jumlah),
                backgroundColor: "rgba(34, 197, 94, 0.2)",
                borderColor: "rgba(34, 197, 94, 1)",
                borderWidth: 2,
                fill: true,
                tension: 0.3, // garis lebih halus
                pointBackgroundColor: "rgba(34, 197, 94, 1)",
                pointRadius: 4,
            },
        ],
    };

    // Untuk chart pengeluaran harian (expanse) selama 1 bulan terakhir
    // Kelompokkan pengeluaran per tanggal
    const pengeluaranPerTanggal = {};
    if (Array.isArray(data.PengunjungBulanan)) {
        data.PengunjungBulanan.forEach(item => {
            const tgl = item.tanggal || item.created_at?.slice(0, 10) || "-";
            if (!pengeluaranPerTanggal[tgl]) pengeluaranPerTanggal[tgl] = 0;
            pengeluaranPerTanggal[tgl] += parseInt(item.amount, 10) || 0;
        });
    }
    // Ambil 30 hari terakhir, urutkan tanggal naik
    const tanggalPengeluaran = Object.keys(pengeluaranPerTanggal).sort().slice(-30);
    const chartExpanseHarian = {
        labels: tanggalPengeluaran,
        datasets: [
            {
                label: "Pengeluaran Harian",
                data: tanggalPengeluaran.map(tgl => pengeluaranPerTanggal[tgl]),
                backgroundColor: "rgba(239, 68, 68, 0.2)",
                borderColor: "rgba(239, 68, 68, 1)",
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: "rgba(239, 68, 68, 1)",
                pointRadius: 4,
            },
        ],
    };

    const optionsRupiah = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function (context) {
                        return `Rp ${context.parsed.y.toLocaleString("id-ID")}`;
                    },
                },
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function (value) {
                        return value >= 1000000
                            ? value / 1000000 + "jt"
                            : value;
                    },
                    color: "#1e40af",
                    font: { weight: "bold" },
                },
                grid: { color: "#dbeafe" },
            },
            x: {
                ticks: { color: "#1e40af", font: { weight: "bold" } },
                grid: { color: "#dbeafe" },
            },
        },
    };

    const optionsPengunjung = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function (context) {
                        return `${context.parsed.y.toLocaleString(
                            "id-ID"
                        )} Pengunjung`;
                    },
                },
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    color: "#1e40af",
                    font: { weight: "bold" },
                },
                grid: { color: "#dbeafe" },
            },
            x: {
                ticks: { color: "#1e40af", font: { weight: "bold" } },
                grid: { color: "#dbeafe" },
            },
        },
    };

    // State untuk testimoni
    const [testimoni, setTestimoni] = useState([]);
    const [loadingTestimoni, setLoadingTestimoni] = useState(false);
    const [showTestimoni, setShowTestimoni] = useState(false);

    // Fungsi untuk mengambil testimoni dari backend
    const handleShowTestimoni = () => {
        setShowTestimoni(true);
        setLoadingTestimoni(true);
        fetch("/api/dashboard/ambilReview")
            .then((res) => res.json())
            .then((json) => {
                setTestimoni(json.reviews || []);
                setLoadingTestimoni(false);
            })
            .catch(() => setLoadingTestimoni(false));
    };

    // Animasi fade-in untuk section
    const [fadeIn, setFadeIn] = useState(false);
    useEffect(() => {
        setTimeout(() => setFadeIn(true), 100);
    }, []);

    // Data pengeluaran dari backend
    const [pengeluaran, setPengeluaran] = useState([]);

    useEffect(() => {
        const fetchPengeluaran = () => {
            fetch("/dashboard/expanse/transactions/guest")
                .then((res) => res.json())
                .then((json) => setPengeluaran(json.expanses || []));
        };
        fetchPengeluaran();
        // Hapus interval update otomatis
        // const interval = setInterval(fetchPengeluaran, 10000); // update tiap 10 detik
        // return () => clearInterval(interval);
    }, []);

    console.log("Pengeluaran:", pengeluaran);
    
    return (
        <div className="min-h-screen bg-[#f7f8fa] font-sans">
            {/* Header */}
            <header className="w-full py-4 px-4 flex flex-col md:flex-row items-center justify-between border-b border-gray-200 bg-white fixed top-0 left-0 z-50 shadow"
                style={{ backdropFilter: "blur(2px)" }}>
                <div className="flex items-center gap-3">
                    <img
                        src="/assets/images/logo.jpeg"
                        alt="Kalimas Logo"
                        className="w-12 h-12 "
                    />
                    <span className="text-2xl font-bold text-gray-800 tracking-wide">
                        Wisata Kalimas
                    </span>
                </div>
                <nav className="mt-2 md:mt-0 flex gap-6 text-gray-700 text-base font-medium">
                    <a href="#data" className="hover:text-blue-900 transition">Data</a>
                    <a href="#fasilitas" className="hover:text-blue-900 transition">Fasilitas</a>
                    <a href="#testimoni" className="hover:text-blue-900 transition">Testimoni</a>
                </nav>
            </header>

            {/* Spacer for fixed navbar */}
            <div className="h-24"></div>

            {/* Hero Section */}
            <section className="flex flex-col md:flex-row items-center justify-between px-4 md:px-16 py-12 gap-8 bg-white border-b border-gray-200">
                <div className="flex-1">
                    <h1 className="text-3xl font-bold text-gray-900 mb-4 leading-tight">
                        Selamat Datang di <span className="text-blue-900">Wisata Kalimas</span>
                    </h1>
                    <p className="text-base text-gray-700 mb-4">
                        Nikmati keindahan dan keseruan wisata sungai Kalimas, destinasi wisata keluarga yang memadukan pesona alam, budaya, dan modernitas di jantung kota Surabaya. Kami menyajikan pengalaman menyenangkan untuk semua kalangan: mulai dari susur sungai yang tenang, aneka kuliner khas yang menggugah selera, hingga hiburan menarik untuk seluruh keluarga.
                    </p>
                    <p className="text-base text-gray-700 mb-4">
                        Di <strong>Wisata Kalimas</strong>, kami juga mengedepankan prinsip <strong>transparansi</strong> dalam pengelolaan. Untuk itu, kami menyediakan data terbuka yang mencakup: jumlah pengunjung harian dan bulanan, pemasukan dari tiket dan kegiatan wisata, serta aktivitas yang sedang dan akan berlangsung.
                    </p>
                    <p className="text-base text-gray-700 mb-6">
                        Jelajahi Kalimas dan lihat bagaimana setiap kunjungan Anda ikut berkontribusi untuk kemajuan wisata lokal.
                    </p>
                    <a
                        href="#data"
                        className="inline-block bg-blue-900 text-white px-6 py-2  font-semibold text-base hover:bg-blue-800 transition"
                    >
                        Lihat Data
                    </a>
                </div>
                <div className="flex-1 flex justify-center">
                    <img
                        src="/assets/images/kalimas.jpg"
                        alt="Wisata Kalimas"
                        className="w-full max-w-md min-h-[220px] object-cover  shadow border border-gray-200"
                    />
                </div>
            </section>

            {/* Data Section */}
            <section className="py-10 px-4 md:px-16 bg-[#f7f8fa] border-b border-gray-200">
                <h2 className="text-2xl font-semibold text-gray-800 mb-8 text-center tracking-wide">
                    Statistik Terkini
                </h2>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div className="bg-white p-6 flex flex-col items-center border border-gray-200 shadow-sm">
                        <h3 className="font-medium text-blue-900 mb-2 text-base">
                            Pengeluaran Harian
                        </h3>
                        <div className="overflow-x-auto w-full">
                            <div style={{ minWidth: 600, height: 260 }}>
                                <Line data={chartExpanseHarian} options={optionsRupiah} />
                            </div>
                        </div>
                        <div className="text-xs text-gray-500 mt-2 text-center italic">
                            Grafik ini menampilkan fluktuasi pengeluaran harian Wisata Kalimas selama 1 bulan terakhir. Setiap titik pada grafik merepresentasikan total pengeluaran yang terjadi pada hari tersebut, baik untuk kebutuhan operasional maupun pengeluaran mendadak. Anda dapat menggeser grafik ke samping untuk melihat data hari-hari sebelumnya atau berikutnya secara lebih detail.
                        </div>
                    </div>
                    <div className="bg-white p-6 flex flex-col items-center border border-gray-200 shadow-sm">
                        <h3 className="font-medium text-blue-900 mb-2 text-base">
                            Pemasukan Bulanan
                        </h3>
                        <div className="overflow-x-auto w-full">
                            <div style={{ minWidth: 600, height: 260 }}>
                                <Line data={chartBulanan} options={optionsRupiah} />
                            </div>
                        </div>
                        <div className="text-xs text-gray-500 mt-2 text-center italic">
                            Grafik ini memperlihatkan perubahan pemasukan bulanan dari berbagai sumber seperti tiket, parkir, resto, dan wahana. Setiap titik pada grafik menunjukkan total pemasukan yang diterima pada bulan atau tanggal tertentu. Silakan geser grafik ke samping untuk menelusuri tren pemasukan dari waktu ke waktu secara lebih rinci.
                        </div>
                    </div>
                    <div className="bg-white p-6 flex flex-col items-center border border-gray-200 shadow-sm">
                        <h3 className="font-medium text-blue-900 mb-2 text-base">
                            Jumlah Pengunjung
                        </h3>
                        <div className="overflow-x-auto w-full">
                            <div style={{ minWidth: 600, height: 260 }}>
                                <Line
                                    data={chartPengunjung}
                                    options={optionsPengunjung}
                                />
                            </div>
                        </div>
                        <div className="text-xs text-gray-500 mt-2 text-center italic">
                            Grafik ini menunjukkan fluktuasi jumlah pengunjung harian selama 14 hari terakhir di Wisata Kalimas. Setiap titik pada grafik merepresentasikan total pengunjung yang datang pada hari tersebut. Anda dapat menggeser grafik ke samping untuk melihat data hari-hari lainnya dan memantau tren kunjungan secara lebih menyeluruh.
                        </div>
                    </div>
                </div>
            </section>

            {/* Keterbukaan Informasi Publik */}
            <section id="keterbukaan" className="py-10 px-4 md:px-16 bg-white border-b border-gray-200">
                <h2 className="text-2xl font-semibold text-gray-800 mb-6 text-center tracking-wide">
                    Keterbukaan Informasi Publik
                </h2>
                <div className="max-w-4xl mx-auto mb-8">
                    <p className="text-gray-700 text-base text-center mb-4">
                        Sebagai bentuk transparansi, berikut adalah ringkasan pemasukan dan pengeluaran Wisata Kalimas. Dana yang terkumpul digunakan untuk mendukung operasional, perawatan fasilitas, promosi, dan berbagai kegiatan demi kenyamanan pengunjung.
                    </p>
                </div>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                    {/* Ringkasan Pemasukan */}
                    <div className="bg-[#f7f8fa] border border-gray-200  p-6 shadow-sm self-start">
                        <h3 className="text-lg font-semibold text-blue-900 mb-3">Ringkasan Pemasukan</h3>
                        <table className="w-full text-base text-left border">
                            <tbody>
                                <tr>
                                    <td className="py-2 px-3 border-b font-medium">Tiket Masuk</td>
                                    <td className="py-2 px-3 border-b text-blue-900 font-bold">
                                        Rp {data.Incomes.ticket_total?.toLocaleString("id-ID")}
                                    </td>
                                </tr>
                                <tr>
                                    <td className="py-2 px-3 border-b font-medium">Total Parkir</td>
                                    <td className="py-2 px-3 border-b text-blue-900 font-bold">
                                        Rp {data.Incomes.parking_total?.toLocaleString("id-ID") || 0}
                                    </td>
                                </tr>
                                <tr>
                                    <td className="py-2 px-3 border-b font-medium">Total Bantuan</td>
                                    <td className="py-2 px-3 border-b text-blue-900 font-bold">
                                        Rp {data.Incomes.bantuan_total?.toLocaleString("id-ID") || 0}
                                    </td>
                                </tr>
                                <tr>
                                    <td className="py-2 px-3 border-b font-medium">Total Resto</td>
                                    <td className="py-2 px-3 border-b text-blue-900 font-bold">
                                        Rp {data.Incomes.resto_total?.toLocaleString("id-ID") || 0}
                                    </td>
                                </tr>
                                <tr>
                                    <td className="py-2 px-3 border-b font-medium">Total Toilet</td>
                                    <td className="py-2 px-3 border-b text-blue-900 font-bold">
                                        Rp {data.Incomes.toilet_total?.toLocaleString("id-ID") || 0}
                                    </td>
                                </tr>
                                <tr>
                                    <td className="py-2 px-3 border-b font-medium">Total Wahana</td>
                                    <td className="py-2 px-3 border-b text-blue-900 font-bold">
                                        Rp {data.Incomes.wahana_total?.toLocaleString("id-ID") || 0}
                                    </td>
                                </tr>
                                <tr>
                                    <td className="py-2 px-3 border-b font-medium">Total Pemasukan</td>
                                    <td className="py-2 px-3 border-b text-blue-900 font-bold"><span>{""}</span>
                                        Rp {data.Incomes.total_income?.toLocaleString("id-ID")}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div className="text-xs text-gray-500 mt-2">
                            <em>
                                <span>
                                    Seluruh angka pada tabel di atas merupakan total pemasukan setelah dikurangi pengeluaran. 
                                    <br />
                                    <b>Catatan:</b> Nilai pemasukan sudah dikurangi oleh pengeluaran operasional maupun mendadak.
                                </span>
                            </em>
                        </div>
                    </div>
                    {/* Ringkasan Pengeluaran */}
                    <div className="bg-[#f7f8fa] border border-gray-200  p-6 shadow-sm">
                        <h3 className="text-lg font-semibold text-blue-900 mb-3">Ringkasan Pengeluaran</h3>
                        <div className="mb-4">
                            <div className="mb-2">
                                <span className="font-semibold text-blue-900">Operasional:</span>
                                <span className="text-gray-700 ml-2">
                                    Digunakan untuk kebutuhan rutin seperti gaji petugas, pembayaran listrik dan air, perawatan fasilitas, serta pembelian perlengkapan operasional harian.
                                </span>
                            </div>
                            <div>
                                <span className="font-semibold text-blue-900">Mendadak:</span>
                                <span className="text-gray-700 ml-2">
                                    Digunakan untuk pengeluaran tak terduga atau kebutuhan mendadak, misalnya perbaikan darurat fasilitas, penanganan insiden, atau kebutuhan penting yang harus segera dipenuhi.
                                </span>
                            </div>
                        </div>
                        {/* Rincian Pengeluaran per Kategori */}
                        <div className="mt-6">
                            <h4 className="font-semibold text-gray-800 mb-2">Rincian Pengeluaran per Kategori:</h4>
                            {pengeluaran.length === 0 ? (
                                <div className="text-gray-400 text-sm">Data pengeluaran belum tersedia.</div>
                            ) : (
                                <div className="max-h-48 overflow-y-auto">
                                    <table className="w-full text-sm border">
                                        <thead>
                                            <tr className="bg-blue-50">
                                                <th className="py-2 px-3 border-b text-left">Kategori</th>
                                                <th className="py-2 px-3 border-b text-left">Deskripsi</th>
                                                <th className="py-2 px-3 border-b text-left">Loket</th>
                                                <th className="py-2 px-3 border-b text-right">Total Pengeluaran</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {pengeluaran.map((item, idx) => (
                                                <tr key={idx}>
                                                    <td className="py-2 px-3 border-b">{item.expanse_category?.name || "Lain-lain"}</td>
                                                    <td className="py-2 px-3 border-b">{item.expanse_operasional?.description || item.expanse_mendadak?.description || "-"}</td>
                                                    <td className="py-2 px-3 border-b">{item.user?.name || "-"}</td>
                                                    <td className="py-2 px-3 border-b text-right text-blue-900 font-semibold">
                                                        Rp {(parseInt(item.amount, 10) || 0).toLocaleString("id-ID")}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            )}
                        </div>
                        <div className="text-xs text-gray-500 mt-2">
                            <em>
                                <span>
                                    Untuk <b>gaji karyawan</b> masuk ke dalam kategori operasional, namun tidak ditampilkan secara rinci demi menjaga etika dan privasi rezeki seseorang.
                                </span>
                            </em>
                        </div>
                        <div className="mt-6 text-right text-base font-semibold text-blue-900">
                            Total Pengeluaran: Rp {
                                pengeluaran
                                    .reduce(
                                        (total, item) =>
                                            total + (parseInt(item.amount, 10) || 0),
                                        0
                                    )
                                    .toLocaleString("id-ID")
                            }
                        </div>
                    </div>
                </div>
                <div className="max-w-4xl mx-auto mt-8">
                    <div className="bg-blue-50 border-l-4 border-blue-900 p-4  text-blue-900 text-sm">
                        <strong>Catatan:</strong> Data pemasukan  dan pengeluaran diupdate secara otomatis setiap ada transaksi masuk dan transaksi keluar di rekap setiap 1 bulan sekali dan dapat diakses oleh publik sebagai bentuk akuntabilitas pengelolaan dana Wisata Kalimas.
                    </div>
                </div>
            </section>

            {/* Fasilitas & Harga Tiket Section */}
            <section id="fasilitas" className="py-10 px-4 md:px-16 bg-white border-b border-gray-200">
                <h2 className="text-2xl font-semibold text-gray-800 mb-8 text-center tracking-wide">
                    Fasilitas Unggulan & Harga Tiket
                </h2>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-10 items-start">
                    {/* Fasilitas */}
                    <div>
                        <div className="flex flex-wrap justify-center gap-6">
                            {fasilitas.map((f, idx) => (
                                <div
                                    key={idx}
                                    className="bg-[#f7f8fa] p-5 flex flex-col items-center border border-gray-200  w-56 shadow-sm"
                                >
                                    <span className="text-3xl mb-2">{f.icon}</span>
                                    <h3 className="text-base font-semibold text-blue-900 mb-1">
                                        {f.title}
                                    </h3>
                                    <p className="text-gray-600 text-center text-sm">
                                        {f.desc}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </div>
                    {/* Harga Tiket & Parkir */}
                    <div>
                        <div className="max-w-md mx-auto bg-[#f7f8fa] border border-gray-200  p-6 shadow-sm">
                            <div className="mb-4">
                                <div className="font-semibold text-gray-700 mb-1">Tiket Masuk:</div>
                                <div className="text-blue-900 font-bold text-xl">Rp 10.000</div>
                            </div>
                            <div>
                                <div className="font-semibold text-gray-700 mb-1">Harga Parkir:</div>
                                <table className="w-full text-sm text-left">
                                    <tbody>
                                        <tr>
                                            <td className="py-1 text-gray-700">Motor</td>
                                            <td className="py-1 text-blue-900 font-semibold">Rp 2.000</td>
                                        </tr>
                                        <tr>
                                            <td className="py-1 text-gray-700">Mobil</td>
                                            <td className="py-1 text-blue-900 font-semibold">Rp 5.000</td>
                                        </tr>
                                        <tr>
                                            <td className="py-1 text-gray-700">Elf</td>
                                            <td className="py-1 text-blue-900 font-semibold">Rp 10.000</td>
                                        </tr>
                                        <tr>
                                            <td className="py-1 text-gray-700">Bus Medium</td>
                                            <td className="py-1 text-blue-900 font-semibold">Rp 20.000</td>
                                        </tr>
                                        <tr>
                                            <td className="py-1 text-gray-700">Bus Besar</td>
                                            <td className="py-1 text-blue-900 font-semibold">Rp 30.000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Testimoni Section */}
            <section id="testimoni" className="py-10 px-4 md:px-16 bg-[#f7f8fa]">
                <h2 className="text-2xl font-semibold text-gray-800 mb-8 text-center tracking-wide">
                    Testimoni Pengunjung
                </h2>
                <div className="flex justify-center mb-4">
                    {!showTestimoni && (
                        <button
                            onClick={handleShowTestimoni}
                            className="bg-blue-900 text-white px-6 py-2  font-semibold text-base hover:bg-blue-800 transition"
                        >
                            Tampilkan Testimoni
                        </button>
                    )}
                </div>
                {loadingTestimoni && (
                    <div className="text-center text-blue-900 font-medium text-base">
                        Memuat testimoni...
                    </div>
                )}
                {showTestimoni && !loadingTestimoni && (
                    <div className="flex flex-wrap justify-center gap-6">
                        {testimoni.length === 0 && (
                            <div className="text-gray-400 text-center text-base">
                                Belum ada testimoni tersedia.
                            </div>
                        )}
                        {testimoni.map((rev, idx) => (
                            <div
                                key={idx}
                                className="bg-white p-5 border border-gray-200  w-80 flex flex-col gap-2 shadow-sm"
                            >
                                <div className="flex items-center gap-3 mb-2">
                                    <img
                                        src={
                                            rev.thumbnail ||
                                            "https://ui-avatars.com/api/?name=U"
                                        }
                                        alt={rev.author_name}
                                        className="w-9 h-9 -full border border-gray-200"
                                    />
                                    <div>
                                        <div className="font-semibold text-blue-900 text-base">
                                            {rev.author_name}
                                        </div>
                                        <div className="text-xs text-gray-400">
                                            {rev.relative_time_description}
                                        </div>
                                    </div>
                                </div>
                                <div className="flex items-center gap-0.5 mb-1">
                                    {[...Array(rev.rating || 0)].map((_, i) => (
                                        <span
                                            key={i}
                                            className="text-yellow-400 text-base"
                                        >
                                            ★
                                        </span>
                                    ))}
                                </div>
                                <div className="text-gray-700 text-sm">
                                    {rev.snippet_id || rev.snippet}
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </section>

            {/* Footer */}
            <footer className="bg-white border-t border-gray-200 mt-8 pt-8">
                <div className="max-w-5xl mx-auto px-4 flex flex-col md:flex-row gap-8 justify-between">
                    {/* Kontak Info */}
                    <div className="flex-1 mb-6 md:mb-0">
                        <div className="font-semibold text-gray-700 mb-2">Kontak Wisata Kalimas</div>
                        <div className="text-gray-700 text-base mb-1">
                            <span className="font-medium">Alamat:</span> Jl. Kalimas Timur No.1, Surabaya
                        </div>
                        <div className="text-gray-700 text-base mb-1">
                            <span className="font-medium">Email:</span> <a href="mailto:info@wisatakalimas.com" className="text-blue-900 hover:underline">info@wisatakalimas.com</a>
                        </div>
                        <div className="text-gray-700 text-base mb-1">
                            <span className="font-medium">WhatsApp:</span> <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer" className="text-blue-900 hover:underline">+62 812-3456-7890</a>
                        </div>
                    </div>
                    {/* Box Email */}
                    <div className="flex-1">
                        <div className="font-semibold text-gray-700 mb-2">Kirim Pesan ke Kami</div>
                        <form
                            className="flex flex-col gap-2"
                            onSubmit={e => {
                                e.preventDefault();
                                // Implement kirim email sesuai kebutuhan backend
                                alert('Pesan Anda telah dikirim!');
                                e.target.reset();
                            }}
                        >
                            <input
                                type="email"
                                name="email"
                                required
                                placeholder="Email Anda"
                                className="border border-gray-200  px-3 py-2 text-base focus:outline-none focus:ring-2 focus:ring-blue-200"
                            />
                            <textarea
                                name="pesan"
                                required
                                placeholder="Tulis pesan Anda..."
                                rows={3}
                                className="border border-gray-200  px-3 py-2 text-base focus:outline-none focus:ring-2 focus:ring-blue-200"
                            />
                            <button
                                type="submit"
                                className="bg-blue-900 text-white  px-4 py-2 text-base font-semibold hover:bg-blue-800 transition"
                            >
                                Kirim Pesan
                            </button>
                        </form>
                    </div>
                </div>
                <div className="text-center py-4 text-gray-400 text-sm mt-8 border-t border-gray-200">
                    &copy; {new Date().getFullYear()} Wisata Kalimas. Transparansi untuk semua.
                </div>
            </footer>
        </div>
    );
}

export default Dashboardguest;
