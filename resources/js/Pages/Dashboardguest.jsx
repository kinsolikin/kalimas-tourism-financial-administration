import React, { useEffect, useState, useRef } from "react";
import { usePage } from "@inertiajs/react";
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
import useFadeInOnScroll from "../hooks/useFadeInOnScroll";

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
        Expanse: 0,
        PengunjungBulanan: 0,
        Incomes: [],
        PengunjungHarian: [],
        // Tambahkan jika belum ada di backend: ExpanseLineHarian: [],
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

    const total = Object.values(data.Incomes).reduce(
        (sum, val) => sum + parseInt(val, 10),
        0
    );

    const { priceTicket, jeniskendaraan, jeniswahana } = usePage().props;

    console.log(jeniswahana);

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
                data: [data.Expanse],
                backgroundColor: "rgba(59, 130, 246, 0.7)",
                borderColor: "rgba(59, 130, 246, 1)",
                borderWidth: 2,
            },
        ],
    };
    // Untuk chart pemasukan bulanan fluktuasi
    const bulananArray = Array.isArray(data.Bulanan) ? data.Bulanan : [];
    const chartBulanan = {
        labels: bulananArray.map((item) => item.tanggal ?? item.bulan ?? ""), // gunakan tanggal/bulan
        datasets: [
            {
                label: "Pemasukan Bulanan",
                data: bulananArray.map(
                    (item) => item.total ?? item.jumlah ?? 0
                ),
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
    const pengunjungHarianTerakhir = Array.isArray(data.PengunjungBulanan)
        ? data.PengunjungBulanan.slice(-14)
        : [];
    const chartPengunjung = {
        labels: pengunjungHarianTerakhir.map((item) => item.tanggal),
        datasets: [
            {
                label: "Jumlah Pengunjung",
                data: pengunjungHarianTerakhir.map(
                    (item) => item.total ?? item.jumlah
                ),
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

    // Chart garis fluktuasi pengeluaran harian
    const expanseLineHarianArray = Array.isArray(data.Expanse)
        ? data.Expanse
        : [];
    const chartExpanseLineHarian = {
        labels: expanseLineHarianArray.map((item) => item.tanggal),
        datasets: [
            {
                label: "Pengeluaran Harian",
                data: expanseLineHarianArray.map((item) => item.total ?? 0),
                backgroundColor: "rgba(239, 68, 68, 0.15)",
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

    // Tambahkan hook animasi untuk setiap section
    const [heroRef, heroVisible] = useFadeInOnScroll();
    const [dataRef, dataVisible] = useFadeInOnScroll();
    const [keterbukaanRef, keterbukaanVisible] = useFadeInOnScroll();
    const [fasilitasRef, fasilitasVisible] = useFadeInOnScroll();
    const [testimoniRef, testimoniVisible] = useFadeInOnScroll();

    const [showLoginDropdown, setShowLoginDropdown] = useState(false);
    const loginDropdownRef = useRef(null);

    // Tutup dropdown jika klik di luar
    useEffect(() => {
        function handleClickOutside(event) {
            if (
                loginDropdownRef.current &&
                !loginDropdownRef.current.contains(event.target)
            ) {
                setShowLoginDropdown(false);
            }
        }
        if (showLoginDropdown) {
            document.addEventListener("mousedown", handleClickOutside);
        } else {
            document.removeEventListener("mousedown", handleClickOutside);
        }
        return () => {
            document.removeEventListener("mousedown", handleClickOutside);
        };
    }, [showLoginDropdown]);

    return (
        <div className="min-h-screen bg-[#f7f8fa] font-sans">
            {/* Header */}
            <header
                className="w-full py-4 px-4 flex flex-col md:flex-row items-center justify-between border-b border-gray-200 bg-white fixed top-0 left-0 z-50 shadow"
                style={{ backdropFilter: "blur(2px)" }}
            >
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
                <nav className="mt-2 md:mt-0 flex gap-6 text-gray-700 text-base font-medium items-center">
                    <a href="#data" className="hover:text-blue-900 transition">
                        Data
                    </a>
                    <a
                        href="#fasilitas"
                        className="hover:text-blue-900 transition"
                    >
                        Fasilitas
                    </a>
                    <a
                        href="#testimoni"
                        className="hover:text-blue-900 transition"
                    >
                        Testimoni
                    </a>
                    {/* Dropdown Login (on click, not hover) */}
                    <div className="relative ml-4" ref={loginDropdownRef}>
                        <button
                            className="bg-blue-900 text-white px-4 py-1 rounded font-semibold hover:bg-blue-800 transition flex items-center gap-2"
                            type="button"
                            onClick={() => setShowLoginDropdown((v) => !v)}
                        >
                            Login
                            <svg
                                className="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </button>
                        {showLoginDropdown && (
                            <div className="absolute left-0 mt-2 min-w-[120px] bg-white border border-gray-200 rounded shadow-lg z-50">
                                <a
                                    href="/login"
                                    className="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-900"
                                    onClick={() => setShowLoginDropdown(false)}
                                >
                                    User
                                </a>
                                <a
                                    href="/admin/login"
                                    className="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-900"
                                    onClick={() => setShowLoginDropdown(false)}
                                >
                                    Admin
                                </a>
                            </div>
                        )}
                    </div>
                </nav>
            </header>

            {/* Spacer for fixed navbar */}
            <div className="h-24"></div>

            {/* Hero Section */}
            <section
                ref={heroRef}
                className={`flex flex-col md:flex-row items-center justify-between px-4 md:px-16 py-12 gap-8 bg-white border-b border-gray-200 transition-all duration-700 ease-out ${
                    heroVisible
                        ? "opacity-100 translate-y-0"
                        : "opacity-0 translate-y-8"
                }`}
            >
                <div className="flex-1">
                    <h1 className="text-3xl font-bold text-gray-900 mb-4 leading-tight">
                        Selamat Datang di{" "}
                        <span className="text-blue-900">Wisata Kalimas</span>
                    </h1>
                    <p className="text-base text-gray-700 mb-4">
                        Nikmati keindahan dan keseruan wisata sungai Kalimas,
                        destinasi wisata keluarga yang memadukan pesona alam,
                        budaya, dan modernitas di jantung kota Surabaya. Kami
                        menyajikan pengalaman menyenangkan untuk semua kalangan:
                        mulai dari susur sungai yang tenang, aneka kuliner khas
                        yang menggugah selera, hingga hiburan menarik untuk
                        seluruh keluarga.
                    </p>
                    <p className="text-base text-gray-700 mb-4">
                        Di <strong>Wisata Kalimas</strong>, kami juga
                        mengedepankan prinsip <strong>transparansi</strong>{" "}
                        dalam pengelolaan. Untuk itu, kami menyediakan data
                        terbuka yang mencakup: jumlah pengunjung harian dan
                        bulanan, pemasukan dari tiket dan kegiatan wisata, serta
                        aktivitas yang sedang dan akan berlangsung.
                    </p>
                    <p className="text-base text-gray-700 mb-6">
                        Jelajahi Kalimas dan lihat bagaimana setiap kunjungan
                        Anda ikut berkontribusi untuk kemajuan wisata lokal.
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
            <section
                ref={dataRef}
                className={`py-10 px-4 md:px-16 bg-[#f7f8fa] border-b border-gray-200 transition-all duration-700 ease-out ${
                    dataVisible
                        ? "opacity-100 translate-y-0"
                        : "opacity-0 translate-y-8"
                }`}
            >
                <h2 className="text-2xl font-semibold text-gray-800 mb-8 text-center tracking-wide">
                    Statistik Terkini
                </h2>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {/* Chart garis pengeluaran harian */}
                    <div className="bg-white p-6 flex flex-col items-center border border-gray-200 shadow-sm">
                        <h3 className="font-medium text-blue-900 mb-2 text-base">
                            Pengeluaran Harian (1 Bulan Terakhir)
                        </h3>
                        <div className="w-full overflow-x-auto">
                            <div style={{ minWidth: 600 }}>
                                <Line
                                    data={chartExpanseLineHarian}
                                    options={optionsRupiah}
                                />
                            </div>
                        </div>
                        <div className="mt-2 text-gray-400 text-xs italic text-justify">
                            Grafik ini memperlihatkan fluktuasi pengeluaran
                            harian Kalimas, memudahkan publik memantau
                            transparansi dan pola pengeluaran setiap hari secara
                            real-time.
                        </div>
                    </div>
                    <div className="bg-white p-6 flex flex-col items-center border border-gray-200  shadow-sm">
                        <h3 className="font-medium text-blue-900 mb-2 text-base">
                            Pemasukan Bulanan
                        </h3>
                        <div className="w-full overflow-x-auto">
                            <div style={{ minWidth: 600 }}>
                                <Line
                                    data={chartBulanan}
                                    options={optionsRupiah}
                                />
                            </div>
                        </div>
                        <div className="mt-2 text-gray-400 text-xs italic text-justify">
                            Grafik ini menampilkan tren pemasukan bulanan dari
                            berbagai sumber, membantu masyarakat melihat
                            perkembangan keuangan wisata Kalimas tiap bulan.
                        </div>
                    </div>
                    <div className="bg-white p-6 flex flex-col items-center border border-gray-200  shadow-sm">
                        <h3 className="font-medium text-blue-900 mb-2 text-base">
                            Jumlah Pengunjung
                        </h3>
                        <div className="w-full overflow-x-auto">
                            <div style={{ minWidth: 600 }}>
                                <Line
                                    data={chartPengunjung}
                                    options={optionsPengunjung}
                                />
                            </div>
                        </div>
                        <div className="mt-2 text-gray-400 text-xs italic text-justify">
                            Grafik ini menunjukkan jumlah pengunjung harian,
                            memudahkan pemantauan tren kunjungan dan evaluasi
                            efektivitas event atau promosi wisata.
                        </div>
                    </div>
                </div>
            </section>

            {/* Keterbukaan Informasi Publik */}
            <section
                ref={keterbukaanRef}
                id="keterbukaan"
                className={`py-10 px-4 md:px-16 bg-white border-b border-gray-200 transition-all duration-700 ease-out ${
                    keterbukaanVisible
                        ? "opacity-100 translate-y-0"
                        : "opacity-0 translate-y-8"
                }`}
            >
                <h2 className="text-2xl font-semibold text-gray-800 mb-6 text-center tracking-wide">
                    Keterbukaan Informasi Publik
                </h2>
                <div className="max-w-4xl mx-auto mb-8">
                    <p className="text-gray-700 text-base text-center mb-4">
                        Sebagai bentuk transparansi, berikut adalah ringkasan
                        pemasukan dan pengeluaran Wisata Kalimas. Dana yang
                        terkumpul digunakan untuk mendukung operasional,
                        perawatan fasilitas, promosi, dan berbagai kegiatan demi
                        kenyamanan pengunjung.
                    </p>
                </div>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                    {/* Ringkasan Pemasukan */}
                    <div className="bg-[#f7f8fa] border border-gray-200  p-6 shadow-sm self-start">
                        <h3 className="text-lg font-semibold text-blue-900 mb-3">
                            Ringkasan Pemasukan
                        </h3>
                        <table className="w-full text-base text-left border">
                            <tbody>
                                <tr>
                                    <td className="py-2 px-3 border-b font-medium">
                                        Tiket Masuk
                                    </td>
                                    <td className="py-2 px-3 border-b text-blue-900 font-bold">
                                        Rp{" "}
                                        {data.Incomes.ticket_total?.toLocaleString(
                                            "id-ID"
                                        )}
                                    </td>
                                </tr>
                                <tr>
                                    <td className="py-2 px-3 border-b font-medium">
                                        Total Parkir
                                    </td>
                                    <td className="py-2 px-3 border-b text-blue-900 font-bold">
                                        Rp{" "}
                                        {data.Incomes.parking_total?.toLocaleString(
                                            "id-ID"
                                        ) || 0}
                                    </td>
                                </tr>
                                <tr>
                                    <td className="py-2 px-3 border-b font-medium">
                                        Total Bantuan
                                    </td>
                                    <td className="py-2 px-3 border-b text-blue-900 font-bold">
                                        Rp{" "}
                                        {data.Incomes.bantuan_total?.toLocaleString(
                                            "id-ID"
                                        ) || 0}
                                    </td>
                                </tr>
                                <tr>
                                    <td className="py-2 px-3 border-b font-medium">
                                        Total Resto
                                    </td>
                                    <td className="py-2 px-3 border-b text-blue-900 font-bold">
                                        Rp{" "}
                                        {data.Incomes.resto_total?.toLocaleString(
                                            "id-ID"
                                        ) || 0}
                                    </td>
                                </tr>
                                <tr>
                                    <td className="py-2 px-3 border-b font-medium">
                                        Total Toilet
                                    </td>
                                    <td className="py-2 px-3 border-b text-blue-900 font-bold">
                                        Rp{" "}
                                        {data.Incomes.toilet_total?.toLocaleString(
                                            "id-ID"
                                        ) || 0}
                                    </td>
                                </tr>
                                <tr>
                                    <td className="py-2 px-3 border-b font-medium">
                                        Total Wahana
                                    </td>
                                    <td className="py-2 px-3 border-b text-blue-900 font-bold">
                                        Rp{" "}
                                        {data.Incomes.wahana_total?.toLocaleString(
                                            "id-ID"
                                        ) || 0}
                                    </td>
                                </tr>
                                <tr>
                                    <td className="py-2 px-3 border-b font-medium">
                                        Total Pemasukan
                                    </td>
                                    <td className="py-2 px-3 border-b text-blue-900 font-bold">
                                        <span>{""}</span>
                                        Rp{" "}
                                        {data.Incomes.total_income?.toLocaleString(
                                            "id-ID"
                                        )}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div className="text-xs text-gray-500 mt-2">
                            <em>
                                <span>
                                    Seluruh angka pada tabel di atas merupakan
                                    total pemasukan setelah dikurangi
                                    pengeluaran.
                                    <br />
                                    <b>Catatan:</b> Nilai pemasukan sudah
                                    dikurangi oleh pengeluaran operasional
                                    maupun mendadak.
                                </span>
                            </em>
                        </div>
                    </div>
                    {/* Ringkasan Pengeluaran */}
                    <div className="bg-[#f7f8fa] border border-gray-200  p-6 shadow-sm">
                        <h3 className="text-lg font-semibold text-blue-900 mb-3">
                            Ringkasan Pengeluaran
                        </h3>
                        <div className="mb-4">
                            <div className="mb-2">
                                <span className="font-semibold text-blue-900">
                                    Operasional:
                                </span>
                                <span className="text-gray-700 ml-2">
                                    Digunakan untuk kebutuhan rutin seperti gaji
                                    petugas, pembayaran listrik dan air,
                                    perawatan fasilitas, serta pembelian
                                    perlengkapan operasional harian.
                                </span>
                            </div>
                            <div>
                                <span className="font-semibold text-blue-900">
                                    Mendadak:
                                </span>
                                <span className="text-gray-700 ml-2">
                                    Digunakan untuk pengeluaran tak terduga atau
                                    kebutuhan mendadak, misalnya perbaikan
                                    darurat fasilitas, penanganan insiden, atau
                                    kebutuhan penting yang harus segera
                                    dipenuhi.
                                </span>
                            </div>
                        </div>
                        {/* Rincian Pengeluaran per Kategori */}
                        <div className="mt-6">
                            <h4 className="font-semibold text-gray-800 mb-2">
                                Rincian Pengeluaran per Kategori:
                            </h4>
                            {pengeluaran.length === 0 ? (
                                <div className="text-gray-400 text-sm">
                                    Data pengeluaran belum tersedia.
                                </div>
                            ) : (
                                <div className="max-h-48 overflow-y-auto">
                                    <table className="w-full text-sm border">
                                        <thead>
                                            <tr className="bg-blue-50">
                                                <th className="py-2 px-3 border-b text-left">
                                                    Kategori
                                                </th>
                                                <th className="py-2 px-3 border-b text-left">
                                                    Deskripsi
                                                </th>
                                                <th className="py-2 px-3 border-b text-left">
                                                    Loket
                                                </th>
                                                <th className="py-2 px-3 border-b text-right">
                                                    Total Pengeluaran
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {pengeluaran.map((item, idx) => (
                                                <tr key={idx}>
                                                    <td className="py-2 px-3 border-b">
                                                        {item.expanse_category
                                                            ?.name ||
                                                            "Lain-lain"}
                                                    </td>
                                                    <td className="py-2 px-3 border-b">
                                                        {item
                                                            .expanse_operasional
                                                            ?.description ||
                                                            item
                                                                .expanse_mendadak
                                                                ?.description ||
                                                            "-"}
                                                    </td>
                                                    <td className="py-2 px-3 border-b">
                                                        {item.user?.name || "-"}
                                                    </td>
                                                    <td className="py-2 px-3 border-b text-right text-blue-900 font-semibold">
                                                        Rp{" "}
                                                        {(
                                                            parseInt(
                                                                item.amount,
                                                                10
                                                            ) || 0
                                                        ).toLocaleString(
                                                            "id-ID"
                                                        )}
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
                                    Untuk <b>gaji karyawan</b> masuk ke dalam
                                    kategori operasional, namun tidak
                                    ditampilkan secara rinci demi menjaga etika
                                    dan privasi rezeki seseorang.
                                </span>
                            </em>
                        </div>
                        <div className="mt-6 text-right text-base font-semibold text-blue-900">
                            Total Pengeluaran: Rp{" "}
                            {pengeluaran
                                .reduce(
                                    (total, item) =>
                                        total +
                                        (parseInt(item.amount, 10) || 0),
                                    0
                                )
                                .toLocaleString("id-ID")}
                        </div>
                    </div>
                </div>
                <div className="max-w-4xl mx-auto mt-8">
                    <div className="bg-blue-50 border-l-4 border-blue-900 p-4  text-blue-900 text-sm">
                        <strong>Catatan:</strong> Data pemasukan dan pengeluaran
                        diupdate secara otomatis setiap ada transaksi masuk dan
                        transaksi keluar di rekap setiap 1 bulan sekali dan
                        dapat diakses oleh publik sebagai bentuk akuntabilitas
                        pengelolaan dana Wisata Kalimas.
                    </div>
                </div>
            </section>

            {/* Fasilitas & Harga Tiket Section */}
            <section
                ref={fasilitasRef}
                id="fasilitas"
                className={`py-10 px-4 md:px-16 bg-white border-b border-gray-200 transition-all duration-700 ease-out ${
                    fasilitasVisible
                        ? "opacity-100 translate-y-0"
                        : "opacity-0 translate-y-8"
                }`}
            >
                <h2 className="text-2xl font-semibold text-gray-800 mb-8 text-center tracking-wide">
                    Fasilitas Unggulan & Harga Tiket
                </h2>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-start md:items-start">
                    {/* Fasilitas */}
                    <div className="flex flex-col h-full justify-start">
                        <div className="flex flex-wrap justify-center gap-6">
                            {fasilitas.map((f, idx) => (
                                <div
                                    key={idx}
                                    className="bg-[#f7f8fa] p-5 flex flex-col items-center border border-gray-200 w-56 shadow-sm rounded-lg mb-4"
                                >
                                    <span className="text-3xl mb-2">
                                        {f.icon}
                                    </span>
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
                    {/* Harga Tiket & Parkir & Wahana */}
                    <div className="flex flex-col h-full justify-start">
                        <div className="max-w-md w-full mx-auto bg-[#f7f8fa] border border-gray-200 p-6 shadow-sm rounded-lg">
                            {/* Tiket Masuk */}
                            <div className="mb-6">
                                <div className="font-semibold text-gray-700 mb-1 text-lg flex items-center gap-2">
                                    <span className="inline-block bg-blue-900 text-white rounded px-2 py-1 text-sm">
                                        Tiket Masuk
                                    </span>
                                </div>
                                <div className="text-blue-900 font-bold text-2xl mt-1 mb-2">
                                    Rp{" "}
                                    {priceTicket && priceTicket.price ? (
                                        Number(
                                            priceTicket.price
                                        ).toLocaleString("id-ID")
                                    ) : (
                                        <span className="text-gray-500">
                                            Tidak tersedia
                                        </span>
                                    )}
                                </div>
                                <div className="text-gray-500 text-xs">
                                    Harga tiket berlaku untuk semua pengunjung,
                                    update setiap waktu karena terkadang ada
                                    perubahan harga tiket cek langsung diwebsite
                                    resmi kami
                                </div>
                            </div>
                            {/* Harga Parkir & Wahana Side by Side */}
                            <div className="flex flex-col md:flex-row gap-6 w-full">
                                {/* Harga Parkir */}
                                <div className="flex-1">
                                    <div className="font-semibold text-gray-700 mb-1 text-lg flex items-center gap-2">
                                        <span className="inline-block bg-blue-900 text-white rounded px-2 py-1 text-sm">
                                            Harga Parkir
                                        </span>
                                    </div>
                                    <table className="w-full text-sm text-left border mt-2">
                                        <thead>
                                            <tr className="bg-blue-50">
                                                <th className="py-2 px-3 border-b text-gray-700 font-semibold">
                                                    Jenis
                                                </th>
                                                <th className="py-2 px-3 border-b text-gray-700 font-semibold">
                                                    Harga
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {jeniskendaraan &&
                                            jeniskendaraan.length > 0 ? (
                                                jeniskendaraan.map(
                                                    (item, index) => (
                                                        <tr key={index}>
                                                            <td className="py-2 px-3 border-b text-gray-700">
                                                                {
                                                                    item.namakendaraan
                                                                }
                                                            </td>
                                                            <td className="py-2 px-3 border-b text-blue-900 font-semibold">
                                                                Rp{" "}
                                                                {Number(
                                                                    item.price
                                                                ).toLocaleString(
                                                                    "id-ID"
                                                                )}
                                                            </td>
                                                        </tr>
                                                    )
                                                )
                                            ) : (
                                                <tr>
                                                    <td
                                                        colSpan="2"
                                                        className="text-center text-gray-500 py-2"
                                                    >
                                                        Tidak ada data
                                                        kendaraan.
                                                    </td>
                                                </tr>
                                            )}
                                        </tbody>
                                    </table>
                                    <div className="text-gray-500 text-xs mt-2">
                                        Harga parkir sesuai jenis kendaraan
                                        update sewaktu waktu jadi pastikan
                                        pantau diwebsite resmi kami
                                    </div>
                                </div>
                                {/* Harga Wahana */}
                                <div className="flex-1">
                                    <div className="font-semibold text-gray-700 mb-1 text-lg flex items-center gap-2">
                                        <span className="inline-block bg-blue-900 text-white rounded px-2 py-1 text-sm">
                                            Harga Wahana
                                        </span>
                                    </div>
                                    <table className="w-full text-sm text-left border mt-2">
                                        <thead>
                                            <tr className="bg-blue-50">
                                                <th className="py-2 px-3 border-b text-gray-700 font-semibold">
                                                    Wahana
                                                </th>
                                                <th className="py-2 px-3 border-b text-gray-700 font-semibold">
                                                    Harga
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {/* Contoh data, ganti dengan data asli jika sudah ada di props */}

                                            {jeniswahana &&
                                            jeniswahana.length > 0 ? (
                                                jeniswahana.map(
                                                    (item, index) => (
                                                        <tr key={index}>
                                                            <td className="py-2 px-3 border-b text-gray-700">
                                                                {
                                                                    item.jeniswahana
                                                                }
                                                            </td>
                                                            <td className="py-2 px-3 border-b text-blue-900 font-semibold">
                                                                Rp{" "}
                                                                {Number(
                                                                    item.price
                                                                ).toLocaleString(
                                                                    "id-ID"
                                                                )}
                                                            </td>
                                                        </tr>
                                                    )
                                                )
                                            ) : (
                                                <tr>
                                                    <td
                                                        colSpan="2"
                                                        className="text-center text-gray-500 py-2"
                                                    >
                                                        Tidak ada data Wahana.
                                                    </td>
                                                </tr>
                                            )}
                                        </tbody>
                                    </table>
                                    <div className="text-gray-500 text-xs mt-2">
                                        Harga wahana dapat berubah sewaktu-waktu
                                        sesuai kebijakan pengelola.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Testimoni Section */}
            <section
                ref={testimoniRef}
                id="testimoni"
                className={`py-10 px-4 md:px-16 bg-[#f7f8fa] transition-all duration-700 ease-out ${
                    testimoniVisible
                        ? "opacity-100 translate-y-0"
                        : "opacity-0 translate-y-8"
                }`}
            >
                <div className="mb-8 text-center tracking-wide">
                    <h2 className="text-2xl font-semibold text-gray-800 ">
                        Testimoni Pengunjung
                    </h2>
                    <div className="text-gray-500 text-xs">
                        Ulasan ini di peroleh dari review di GoogleMaps, data
                        update berkala jika ada ulasan terbaru.
                    </div>
                </div>
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
                        <div className="font-semibold text-gray-700 mb-2">
                            Kontak Wisata Kalimas
                        </div>
                        <div className="text-gray-700 text-base mb-1">
                            <span className="font-medium">Alamat:</span> Jl.
                            Kalimas Timur No.1, Surabaya
                        </div>
                        <div className="text-gray-700 text-base mb-1">
                            <span className="font-medium">Email:</span>{" "}
                            <a
                                href="mailto:info@wisatakalimas.com"
                                className="text-blue-900 hover:underline"
                            >
                                info@wisatakalimas.com
                            </a>
                        </div>
                        <div className="text-gray-700 text-base mb-1">
                            <span className="font-medium">WhatsApp:</span>{" "}
                            <a
                                href="https://wa.me/6281234567890"
                                target="_blank"
                                rel="noopener noreferrer"
                                className="text-blue-900 hover:underline"
                            >
                                +62 812-3456-7890
                            </a>
                        </div>
                    </div>
                    {/* Box Email */}
                    <div className="flex-1">
                        <div className="font-semibold text-gray-700 mb-2">
                            Kirim Pesan ke Kami
                        </div>
                        <form
                            className="flex flex-col gap-2"
                            onSubmit={(e) => {
                                e.preventDefault();
                                // Implement kirim email sesuai kebutuhan backend
                                alert("Pesan Anda telah dikirim!");
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
                    &copy; {new Date().getFullYear()} Wisata Kalimas.
                    Transparansi untuk semua.
                </div>
            </footer>
        </div>
    );
}

export default Dashboardguest;
