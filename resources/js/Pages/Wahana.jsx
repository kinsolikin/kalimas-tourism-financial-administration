import React, { useEffect, useState, useRef } from "react";
import { useForm, usePage } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import Swal from "sweetalert2";
import dayjs from "dayjs";
import axios from "axios";
import { router } from "@inertiajs/react";

const Wahana = ({ auth }) => {
    const { wahanaoption } = usePage().props;

    console.log("Wahana Options:", wahanaoption);
    const [transactions, setTransactions] = useState([]);
    const [fromDate, setFromDate] = useState("");
    const [toDate, setToDate] = useState("");
    const [showShiftSummary, setShowShiftSummary] = useState(false);
    const [closingShift, setClosingShift] = useState(false);
    const [todaySummary, setTodaySummary] = useState({
        wahana: [],
        totalWahana: 0,
    });
    const shiftSummaryRef = useRef(null);

    const { data, setData, post, processing, errors, reset } = useForm({
        wahana_id: "",
        harga: 0,
        jumlah: 0,
        total: 0,
        nama_wahana: "",
    });

    // Ambil data transaksi saat mount & setelah submit/hapus
    const fetchTransactions = async () => {
        try {
            const res = await axios.get("/dashboard/wahana/transactions");
            setTransactions(res.data);
        } catch (error) {
            Swal.fire("Gagal", "Gagal mengambil data transaksi", "error");
        }
    };

    useEffect(() => {
        fetchTransactions();
    }, []);

    useEffect(() => {
        // Update harga, total, dan nama_wahana jika wahana_id atau jumlah berubah
        const selected = Object.values(wahanaoption || {}).find(
            (w) => String(w.id) === String(data.wahana_id)
        );
        const harga = selected ? Number(selected.price) : 0;
        const nama_wahana = selected ? selected.jeniswahana : "";
        setData((prev) => ({
            ...prev,
            harga,
            total: harga * (Number(prev.jumlah) || 0),
            nama_wahana,
        }));
    }, [data.wahana_id, data.jumlah, wahanaoption]);

    const handleSubmit = (e) => {
        e.preventDefault();
        post("/dashboard/wahana/store", {
            onSuccess: () => {
                reset();
                Swal.fire(
                    "Berhasil",
                    "Data Wahana berhasil disimpan",
                    "success"
                );
                fetchTransactions();
            },
            onError: () => {
                Swal.fire({
                    title: "Gagal",
                    text: "Data wahana gagal disimpan",
                    icon: "error",
                    confirmButtonColor: "#d33",
                });
            },
        });
    };

    const fetchFilteredTransactions = async () => {
        try {
            const res = await axios.get(
                "/dashboard/wahana/transactions/filter",
                {
                    params: {
                        from: fromDate,
                        to: toDate,
                    },
                }
            );
            setTransactions(res.data);
        } catch (err) {
            Swal.fire("Gagal", "Gagal ambil data", "error");
        }
    };

    const deleteAllTransactions = async () => {
        const result = await Swal.fire({
            title: "Yakin ingin menghapus Semua transaksi ini?",
            text: "Tindakan ini tidak bisa dibatalkan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal",
        });
        if (result.isConfirmed) {
            Swal.fire({
                title: "Menghapus...",
                text: "Silakan tunggu...",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            try {
                await axios.delete("/dashboard/wahana/transactions/delete-all");
                setTransactions([]);
                Swal.fire("Berhasil!", "Transaksi telah dihapus.", "success");
            } catch (error) {
                Swal.fire("Gagal!", "Gagal menghapus transaksi.", "error");
            }
        }
    };

    const deleteTransaction = async (id) => {
        const result = await Swal.fire({
            title: "Yakin ingin menghapus transaksi ini?",
            text: "Tindakan ini tidak bisa dibatalkan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal",
        });
        if (result.isConfirmed) {
            try {
                await axios.delete(
                    `/dashboard/wahana/transactions/delete/${id}`
                );
                setTransactions((prev) =>
                    prev.filter((item) => item.id !== id)
                );
                Swal.fire("Berhasil!", "Transaksi telah dihapus.", "success");
            } catch (error) {
                Swal.fire("Gagal!", "Gagal menghapus transaksi.", "error");
            }
        }
    };

    // Helper: Get today's wahana transactions and total
    const getTodayTransactions = () => {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const tomorrow = new Date(today);
        tomorrow.setDate(today.getDate() + 1);

        const wahana = (transactions || []).filter((t) => {
            const d = new Date(t.created_at);
            return d >= today && d < tomorrow;
        });
        const totalWahana = wahana.reduce(
            (sum, t) => sum + Number(t.harga || 0) * Number(t.jumlah || 0),
            0
        );
        return {
            wahana,
            totalWahana,
        };
    };

    // Handler: Akhiri Shift
    const handleEndShift = () => {
        const summary = getTodayTransactions();
        setTodaySummary(summary);
        setShowShiftSummary(true);
    };

    // Handler: Simpan Shift & Logout
    const handleSaveShiftAndLogout = async () => {
        setClosingShift(true);
        try {
            const result = await Swal.fire({
                title: "Yakin ingin akhiri shift ?",
                text: "Tindakan ini tidak bisa dibatalkan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, akhiri!",
                cancelButtonText: "Batal",
            });
            if (result.isConfirmed) {
                const rresult = await Swal.fire({
                    title: "Menyimpan shift...",
                    text: "Silakan tunggu...",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();

                        // Tutup swal otomatis setelah 5 detik (5000 ms)
                        setTimeout(() => {
                            Swal.close();
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: "Shift berhasil disimpan.",
                                timer: 2000,
                                showConfirmButton: false,
                            });
                        }, 3000);
                    },
                });

                // Logout user
                router.post("/logout");
            }
        } catch (error) {
            Swal.fire({
                icon: "error",
                title: "Gagal",
                text: "Gagal menyimpan shift atau logout.",
            });
        } finally {
            setClosingShift(false);
        }
    };

    // Scroll ke shift summary saat dibuka
    const handleShowShiftSummary = () => {
        setShowShiftSummary((prev) => {
            const next = !prev;
            if (!prev) handleEndShift();
            setTimeout(() => {
                if (!prev && shiftSummaryRef.current) {
                    shiftSummaryRef.current.scrollIntoView({
                        behavior: "smooth",
                    });
                }
            }, 100);
            if (prev) setShowShiftSummary(false);
            return !prev;
        });
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <div className="max-w-5xl mx-auto px-4 py-6">
                <h1 className="text-2xl font-bold mb-6 text-center">
                    Form Pemasukan Wahana
                </h1>

                <div className="flex flex-col md:grid md:grid-cols-2 gap-6">
                    {/* Form Input */}
                    <form
                        onSubmit={handleSubmit}
                        className="space-y-6 bg-white p-4 md:p-6   shadow-md border"
                    >
                        <div>
                            <label
                                htmlFor="harga"
                                className="block text-sm font-semibold mb-1"
                            >
                                Harga Satuan (Rp)
                            </label>
                            <input
                                type="text"
                                id="harga"
                                value={(
                                    Number(data.harga) || 0
                                ).toLocaleString()}
                                disabled
                                className="w-full bg-gray-100 border border-gray-300   px-4 py-2 text-gray-600"
                            />
                        </div>

                        <div>
                            <label
                                htmlFor="jumlah"
                                className="block text-sm font-semibold mb-1"
                            >
                                Jumlah Tiket
                            </label>
                            <input
                                type="number"
                                id="jumlah"
                                min="1"
                                value={data.jumlah}
                                onChange={(e) => {
                                    const val = e.target.value;
                                    setData(
                                        "jumlah",
                                        val === "" ? "" : parseInt(val)
                                    );
                                }}
                                className="w-full border border-gray-300   px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required
                            />
                            {errors.jumlah && (
                                <p className="text-red-500 text-sm mt-1">
                                    {errors.jumlah}
                                </p>
                            )}
                        </div>

                        <div>
                            <label
                                htmlFor="wahana_id"
                                className="block text-sm font-semibold mb-1"
                            >
                                Jenis Wahana
                            </label>
                            <select
                                id="wahana_id"
                                value={data.wahana_id}
                                onChange={(e) =>
                                    setData({
                                        ...data,
                                        wahana_id: e.target.value,
                                    })
                                }
                                className="w-full border border-gray-300   px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required
                            >
                                <option value="">
                                    -- Pilih Jenis Wahana --
                                </option>
                                {Object.values(wahanaoption || {}).map(
                                    (option) => (
                                        <option
                                            key={option.id}
                                            value={option.id}
                                        >
                                            {option.jeniswahana}
                                        </option>
                                    )
                                )}
                            </select>
                        </div>

                        <div>
                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2   transition"
                            >
                                {processing ? "Menyimpan..." : "Simpan"}
                            </button>
                        </div>
                    </form>

                    {/* Detail Perhitungan */}
                    <div className="bg-gray-50 p-4 md:p-6   shadow-md border">
                        <h2 className="text-xl font-bold mb-4 text-blue-700">
                            Detail Perhitungan
                        </h2>
                        <div className="space-y-2 text-gray-700">
                            <p>
                                <span className="font-semibold">
                                    Nama Wahana:
                                </span>{" "}
                                {data.nama_wahana || "-"}
                            </p>
                            <p>
                                <span className="font-semibold">
                                    Harga per Unit:
                                </span>{" "}
                                Rp {data.harga.toLocaleString()}
                            </p>
                            <p>
                                <span className="font-semibold">Jumlah:</span>{" "}
                                {data.jumlah}
                            </p>
                            <hr className="my-2" />
                            <p className="text-lg font-bold text-blue-600">
                                Total: Rp{" "}
                                {(Number(data.total) || 0).toLocaleString()}
                            </p>
                        </div>
                        {/* Tombol Akhiri Shift */}
                        <div className="mt-8 flex flex-col items-center">
                            <button
                                onClick={handleShowShiftSummary}
                                className="w-full px-6 py-3 bg-green-600 text-white font-bold hover:bg-green-700 focus:ring-4 focus:ring-green-300"
                            >
                                {showShiftSummary
                                    ? "Tutup Rincian Shift"
                                    : "Akhiri Shift"}
                            </button>
                        </div>
                    </div>
                </div>
                {/* Rincian Shift Hari Ini */}
                {showShiftSummary && (
                    <div
                        ref={shiftSummaryRef}
                        className="mt-8 bg-gray-50 border border-gray-200   p-6 shadow-lg w-full  mx-auto"
                    >
                        <h4 className="text-lg font-bold mb-4 text-center text-green-700">
                            Rincian Transaksi Shift Hari Ini
                        </h4>
                        <div className="mb-4">
                            <strong>Transaksi Wahana:</strong>
                            <ul className="list-disc ml-6">
                                {todaySummary.wahana.length > 0 ? (
                                    todaySummary.wahana.map((t, i) => (
                                        <li key={i}>
                                            {t.jenis_wahana.jeniswahana || "-"} | Jumlah:{" "}
                                            {t.jumlah} | Rp {t.harga} | Total:
                                            Rp{" "}
                                            {(
                                                Number(t.harga) *
                                                Number(t.jumlah)
                                            ).toLocaleString()}{" "}
                                            |{" "}
                                            {new Date(
                                                t.created_at
                                            ).toLocaleTimeString("id-ID")}
                                        </li>
                                    ))
                                ) : (
                                    <li>
                                        Tidak ada transaksi wahana hari ini.
                                    </li>
                                )}
                            </ul>
                            <div className="mt-2 text-right font-semibold text-blue-700">
                                Total Wahana: Rp{" "}
                                {todaySummary.totalWahana.toLocaleString()}
                            </div>
                        </div>
                        <div className="flex justify-center">
                            <button
                                onClick={handleSaveShiftAndLogout}
                                disabled={closingShift}
                                className="px-6 py-3 bg-blue-700 text-white font-bold hover:bg-blue-800 focus:ring-4 focus:ring-blue-300"
                            >
                                {closingShift
                                    ? "Menyimpan & Logout..."
                                    : "Simpan Shift & Logout"}
                            </button>
                        </div>
                    </div>
                )}
                {/* Riwayat Transaksi */}
                <div className="mt-8 bg-white shadow   p-6">
                    <h3 className="text-xl font-bold mb-4">
                        Riwayat Transaksi Wahana
                    </h3>
                    <div className="flex gap-4 mb-4">
                        <div>
                            <label className="block text-sm">
                                Dari Tanggal
                            </label>
                            <input
                                type="date"
                                value={fromDate}
                                onChange={(e) => setFromDate(e.target.value)}
                                className="border p-2  "
                            />
                        </div>
                        <div>
                            <label className="block text-sm">
                                Sampai Tanggal
                            </label>
                            <input
                                type="date"
                                value={toDate}
                                onChange={(e) => setToDate(e.target.value)}
                                className="border p-2  "
                            />
                        </div>
                        <div className="flex items-end">
                            <button
                                onClick={fetchFilteredTransactions}
                                className="bg-green-600 text-white px-4 py-2   hover:bg-green-700"
                            >
                                Filter
                            </button>
                        </div>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr className="bg-gray-100">
                                    <th className="px-2 py-1 text-left">
                                        Tanggal
                                    </th>
                                    <th className="px-2 py-1 text-left">
                                        Nama Wahana
                                    </th>
                                    <th className="px-2 py-1 text-right">
                                        Jumlah
                                    </th>
                                    <th className="px-2 py-1 text-right">
                                        Harga
                                    </th>
                                    <th className="px-2 py-1 text-right">
                                        Total
                                    </th>
                                    <th className="px-2 py-1 text-center">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {transactions.length === 0 ? (
                                    <tr>
                                        <td
                                            colSpan="6"
                                            className="text-center py-2 text-gray-400"
                                        >
                                            Tidak ada transaksi.
                                        </td>
                                    </tr>
                                ) : (
                                    transactions.map((t) => (
                                        <tr key={t.id} className="border-t">
                                            <td className="px-2 py-1">
                                                {dayjs(t.created_at).format(
                                                    "DD/MM/YYYY"
                                                )}
                                            </td>
                                            <td className="px-2 py-1">
                                                {t.jenis_wahana.jeniswahana}
                                            </td>
                                            <td className="px-2 py-1 text-right">
                                                {t.jumlah}
                                            </td>
                                            <td className="px-2 py-1 text-right">
                                                Rp {t.harga.toLocaleString()}
                                            </td>
                                            <td className="px-2 py-1 text-right">
                                                Rp{" "}
                                                {(
                                                    t.harga * t.jumlah
                                                ).toLocaleString()}
                                            </td>
                                            <td className="px-2 py-1 text-center">
                                                <button
                                                    onClick={() =>
                                                        deleteTransaction(t.id)
                                                    }
                                                    className="text-red-600 hover:underline"
                                                >
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                    {transactions.length > 0 && (
                        <div className="flex justify-between mt-4">
                            <button
                                onClick={deleteAllTransactions}
                                className="text-red-600 hover:underline"
                            >
                                🗑 Hapus Semua Transaksi
                            </button>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
};

export default Wahana;
