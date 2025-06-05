import React, { useEffect, useState } from "react";
import { Bar } from "react-chartjs-2";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
} from "chart.js";

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
);

function Dashboardguest() {
    const [data, setData] = useState({
        Harian: 0,
        Bulanan: 0,
        PengunjungBulanan: 0,
    });

    useEffect(() => {
        const fetchData = () => {
            fetch("/api/dashboard-data")
                .then((res) => res.json())
                .then((json) => setData(json));
        };

        fetchData();
        const interval = setInterval(fetchData, 5000);

        return () => clearInterval(interval);
    }, []);

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
                borderRadius: 8,
            },
        ],
    };
    const chartBulanan = {
        labels: ["Bulan Ini"],
        datasets: [
            {
                label: "Pemasukan Bulanan",
                data: [data.Bulanan],
                backgroundColor: "rgba(96, 165, 250, 0.7)",
                borderColor: "rgba(96, 165, 250, 1)",
                borderWidth: 2,
                borderRadius: 8,
            },
        ],
    };
    const chartPengunjung = {
        labels: ["Bulan Ini"],
        datasets: [
            {
                label: "Jumlah Pengunjung",
                data: [data.PengunjungBulanan],
                backgroundColor: "rgba(34, 197, 94, 0.7)",
                borderColor: "rgba(34, 197, 94, 1)",
                borderWidth: 2,
                borderRadius: 8,
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

    return (
        <div className="min-h-screen bg-white">
            {/* Header */}
            <header className="w-full py-4 px-4 flex flex-col md:flex-row items-center justify-between border-b border-gray-100 bg-white fixed top-0 left-0 z-50 transition-shadow duration-300 shadow-sm"
                style={{ backdropFilter: "blur(2px)" }}>
                <div className="flex items-center gap-2">
                    <img
                        src="/assets/images/logo.jpeg"
                        alt="Kalimas Logo"
                        className="w-10 h-10 "
                    />
                    <span className="text-xl font-semibold text-gray-800">
                        Wisata Kalimas
                    </span>
                </div>
                <nav className="mt-2 md:mt-0 flex gap-4 text-gray-600 text-sm font-medium">
                    <a href="#data" className="hover:text-blue-500 transition">Data</a>
                    <a href="#fasilitas" className="hover:text-blue-500 transition">Fasilitas</a>
                    <a href="#testimoni" className="hover:text-blue-500 transition">Testimoni</a>
                </nav>
            </header>

            {/* Spacer for fixed navbar */}
            <div className="h-20 md:h-16"></div>

            {/* Hero Section */}
            <section className="flex flex-col md:flex-row items-center justify-between px-4 md:px-12 py-10 gap-6 bg-white">
                <div className="flex-1">
                    <h1 className="text-2xl md:text-3xl font-bold text-gray-900 mb-3">
                        Selamat Datang di{" "}
                        <span className="text-blue-500">Wisata Kalimas</span>
                    </h1>
                    <p className="text-sm text-gray-600 mb-5">
                        Nikmati keindahan dan keseruan wisata sungai
                        Kalimas destinasi wisata keluarga yang memadukan pesona
                        alam, budaya, dan modernitas di jantung kota Surabaya.
                        Kami menyajikan pengalaman menyenangkan untuk semua
                        kalangan: mulai dari susur sungai yang tenang, aneka
                        kuliner khas yang menggugah selera, hingga hiburan
                        menarik untuk seluruh keluarga.
                    </p>
                    <p className="text-sm text-gray-600 mb-5">
                        Di <strong>Wisata Kalimas</strong>, kami juga
                        mengedepankan prinsip <strong>transparansi</strong>{" "}
                        dalam pengelolaan. Untuk itu, kami menyediakan data
                        terbuka yang mencakup: jumlah pengunjung harian dan
                        bulanan, pemasukan dari tiket dan kegiatan wisata, serta
                        aktivitas yang sedang dan akan berlangsung.
                    </p>
                    <p className="text-sm text-gray-600 mb-5">
                        Ayo jelajahi Kalimas dan lihat bagaimana setiap
                        kunjungan Anda ikut berkontribusi untuk kemajuan wisata
                        lokal.
                    </p>
                    <a
                        href="#data"
                        className="inline-block bg-blue-500 text-white px-5 py-2  transition font-medium text-sm hover:bg-blue-600 hover:scale-105 duration-200"
                    >
                        Lihat Data
                    </a>
                </div>

                <div className="flex-1 flex justify-center">
                    <img
                        src="/assets/images/kalimas.jpg"
                        alt="Wisata Kalimas"
                        className="w-full max-w-md min-h-[220px] object-cover  transition-transform duration-700 hover:scale-105"
                    />
                </div>
            </section>

            {/* Data Section */}
            <section className="py-8 px-4 md:px-12 bg-white">
                <h2 className="text-xl font-semibold text-gray-800 mb-6 text-center">
                    Statistik Terkini
                </h2>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div className="bg-white  p-4 flex flex-col items-center border border-gray-100 transition-transform duration-500 hover:scale-105">
                        <h3 className="font-medium text-blue-500 mb-1 text-sm">
                            Pemasukan Harian
                        </h3>
                        <div className="w-full">
                            <Bar data={chartHarian} options={optionsRupiah} />
                        </div>
                    </div>
                    <div className="bg-white  p-4 flex flex-col items-center border border-gray-100 transition-transform duration-500 hover:scale-105">
                        <h3 className="font-medium text-blue-500 mb-1 text-sm">
                            Pemasukan Bulanan
                        </h3>
                        <div className="w-full">
                            <Bar data={chartBulanan} options={optionsRupiah} />
                        </div>
                    </div>
                    <div className="bg-white  p-4 flex flex-col items-center border border-gray-100 transition-transform duration-500 hover:scale-105">
                        <h3 className="font-medium text-blue-500 mb-1 text-sm">
                            Jumlah Pengunjung
                        </h3>
                        <div className="w-full">
                            <Bar
                                data={chartPengunjung}
                                options={optionsPengunjung}
                            />
                        </div>
                    </div>
                </div>
            </section>

            {/* Fasilitas & Harga Tiket Section */}
            <section id="fasilitas" className="py-8 px-4 md:px-12 bg-white">
                <h2 className="text-xl font-semibold text-gray-800 mb-6 text-center">
                    Fasilitas Unggulan & Harga Tiket
                </h2>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                    {/* Fasilitas */}
                    <div>
                        <div className="flex flex-wrap justify-center gap-4">
                            {fasilitas.map((f, idx) => (
                                <div
                                    key={idx}
                                    className="bg-white p-4 flex flex-col items-center border border-gray-100 w-48 transition-transform duration-500 hover:scale-105"
                                    style={{ transitionDelay: `${idx * 80}ms` }}
                                >
                                    <span className="text-3xl mb-1">{f.icon}</span>
                                    <h3 className="text-base font-medium text-blue-500 mb-0.5">
                                        {f.title}
                                    </h3>
                                    <p className="text-gray-500 text-center text-xs">
                                        {f.desc}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </div>
                    {/* Harga Tiket & Parkir */}
                    <div>
                        <div className="max-w-md mx-auto bg-white border border-gray-100 rounded p-6">
                            <div className="mb-4">
                                <div className="font-medium text-gray-700 mb-1">Tiket Masuk:</div>
                                <div className="text-blue-600 font-bold text-lg">Rp 5.000</div>
                            </div>
                            <div>
                                <div className="font-medium text-gray-700 mb-1">Harga Parkir:</div>
                                <table className="w-full text-sm text-left">
                                    <tbody>
                                        <tr>
                                            <td className="py-1 text-gray-600">Motor</td>
                                            <td className="py-1 text-blue-600 font-semibold">Rp 2.000</td>
                                        </tr>
                                        <tr>
                                            <td className="py-1 text-gray-600">Mobil</td>
                                            <td className="py-1 text-blue-600 font-semibold">Rp 5.000</td>
                                        </tr>
                                        <tr>
                                            <td className="py-1 text-gray-600">Elf</td>
                                            <td className="py-1 text-blue-600 font-semibold">Rp 10.000</td>
                                        </tr>
                                        <tr>
                                            <td className="py-1 text-gray-600">Bus Medium</td>
                                            <td className="py-1 text-blue-600 font-semibold">Rp 20.000</td>
                                        </tr>
                                        <tr>
                                            <td className="py-1 text-gray-600">Bus Besar</td>
                                            <td className="py-1 text-blue-600 font-semibold">Rp 30.000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Testimoni Section */}
            <section id="testimoni" className="py-8 px-4 md:px-12 bg-white">
                <h2 className="text-xl font-semibold text-gray-800 mb-6 text-center">
                    Testimoni Pengunjung
                </h2>
                <div className="flex justify-center mb-4">
                    {!showTestimoni && (
                        <button
                            onClick={handleShowTestimoni}
                            className="bg-blue-500 text-white px-5 py-2  transition font-medium text-sm hover:bg-blue-600 hover:scale-105 duration-200"
                        >
                            Tampilkan Testimoni
                        </button>
                    )}
                </div>
                {loadingTestimoni && (
                    <div className="text-center text-blue-500 font-medium text-sm">
                        Memuat testimoni...
                    </div>
                )}
                {showTestimoni && !loadingTestimoni && (
                    <div className="flex flex-wrap justify-center gap-4">
                        {testimoni.length === 0 && (
                            <div className="text-gray-400 text-center text-sm">
                                Belum ada testimoni tersedia.
                            </div>
                        )}
                        {testimoni.map((rev, idx) => (
                            <div
                                key={idx}
                                className="bg-white  p-4 border border-gray-100 w-72 flex flex-col gap-1 transition-transform duration-500 hover:scale-105"
                                style={{ transitionDelay: `${idx * 60}ms` }}
                            >
                                <div className="flex items-center gap-2 mb-1">
                                    <img
                                        src={
                                            rev.thumbnail ||
                                            "https://ui-avatars.com/api/?name=U"
                                        }
                                        alt={rev.author_name}
                                        className="w-8 h-8 "
                                    />
                                    <div>
                                        <div className="font-medium text-blue-500 text-sm">
                                            {rev.author_name}
                                        </div>
                                        <div className="text-xs text-gray-400">
                                            {rev.relative_time_description}
                                        </div>
                                    </div>
                                </div>
                                <div className="flex items-center gap-0.5 mb-0.5">
                                    {[...Array(rev.rating || 0)].map((_, i) => (
                                        <span
                                            key={i}
                                            className="text-yellow-400 text-xs"
                                        >
                                            ★
                                        </span>
                                    ))}
                                </div>
                                <div className="text-gray-600 text-xs">
                                    {rev.snippet_id || rev.snippet}
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </section>

            {/* Footer */}
            <footer className="bg-white border-t border-gray-100 mt-8 pt-8">
                <div className="max-w-5xl mx-auto px-4 flex flex-col md:flex-row gap-8 justify-between">
                    {/* Kontak Info */}
                    <div className="flex-1 mb-6 md:mb-0">
                        <div className="font-semibold text-gray-700 mb-2">Kontak Wisata Kalimas</div>
                        <div className="text-gray-600 text-sm mb-1">
                            <span className="font-medium">Alamat:</span> Jl. Kalimas Timur No.1, Surabaya
                        </div>
                        <div className="text-gray-600 text-sm mb-1">
                            <span className="font-medium">Email:</span> <a href="mailto:info@wisatakalimas.com" className="text-blue-600 hover:underline">info@wisatakalimas.com</a>
                        </div>
                        <div className="text-gray-600 text-sm mb-1">
                            <span className="font-medium">WhatsApp:</span> <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer" className="text-blue-600 hover:underline">+62 812-3456-7890</a>
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
                                className="border border-gray-200 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                            />
                            <textarea
                                name="pesan"
                                required
                                placeholder="Tulis pesan Anda..."
                                rows={3}
                                className="border border-gray-200 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                            />
                            <button
                                type="submit"
                                className="bg-blue-500 text-white rounded px-4 py-2 text-sm font-medium hover:bg-blue-600 transition"
                            >
                                Kirim Pesan
                            </button>
                        </form>
                    </div>
                </div>
                <div className="text-center py-4 text-gray-400 text-xs mt-8 border-t border-gray-100">
                    &copy; {new Date().getFullYear()} Wisata Kalimas. Transparansi untuk semua.
                </div>
            </footer>
        </div>
    );
}

export default Dashboardguest;
