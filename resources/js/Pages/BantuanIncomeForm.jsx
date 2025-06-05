import React, { useEffect, useState } from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { useForm } from "@inertiajs/react";
import Swal from "sweetalert2";
import dayjs from "dayjs";

const BantuanIncomeForm = ({ auth, incomes }) => {
    const [showHistory, setShowHistory] = useState(false);
    const [transactions, setTransactions] = useState([]);
    const [fromDate, setFromDate] = useState("");
    const [toDate, setToDate] = useState("");

    const { data, setData, post, processing, errors, reset } = useForm({
        sumber_bantuan: "",
        keterangan: "",
        total: 0,
    });

    const handleSubmit = (e) => {
        e.preventDefault();

        post("/dashboard/bantuan-income/store", {
            onSuccess: () => {
                Swal.fire("Berhasil", "Data bantuan berhasil disimpan", "success");
                reset();
                if (showHistory) fetchTransactions();
            },
            onError: () => {
                Swal.fire("Gagal", "Gagal menyimpan data bantuan", "error");
            },
        });
    };

    // Ambil data transaksi, otomatis filter jika ada tanggal
    const fetchTransactions = async (from = fromDate, to = toDate) => {
        try {
            let url = "/dashboard/bantuan/transactions";
            let params = {};
            if (from || to) {
                url = "/dashboard/bantuan/transactions/filter";
                params = { params: { from, to } };
            }
            const res = await axios.get(url, params);
            setTransactions(res.data);
        } catch (err) {
            console.error("Gagal mengambil data transaksi", err);
        }
    };

    // Otomatis refresh riwayat saat filter tanggal berubah & saat showHistory true
    useEffect(() => {
        if (showHistory) {
            fetchTransactions();
        }
        // eslint-disable-next-line
    }, [fromDate, toDate, showHistory]);

    // Setelah hapus data, refresh riwayat jika sedang tampil
    const deleteAllTransactions = async () => {
        const result = await Swal.fire({
            title: "Hapus semua transaksi?",
            text: "Tindakan ini tidak bisa dibatalkan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal",
        });

        if (result.isConfirmed) {
            try {
                await axios.delete("/dashboard/bantuan/transactions/delete-all");
                setTransactions([]);
                Swal.fire("Berhasil!", "Semua transaksi telah dihapus.", "success");
                if (showHistory) fetchTransactions();
            } catch (err) {
                console.error("Gagal hapus transaksi", err);
            }
        }
    };

    const deleteTransaction = async (id) => {
        const result = await Swal.fire({
            title: "Hapus transaksi ini?",
            text: "Tindakan ini tidak bisa dibatalkan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal",
        });

        if (result.isConfirmed) {
            try {
                await axios.delete(`/dashboard/bantuan/transactions/delete/${id}`);
                setTransactions((prev) => prev.filter((t) => t.id !== id));
                Swal.fire("Berhasil!", "Transaksi telah dihapus.", "success");
                if (showHistory) fetchTransactions();
            } catch (err) {
                Swal.fire("Gagal!", "Gagal menghapus transaksi.", "error");
            }
        }
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <div className="max-w-lg mx-auto py-8 px-4">
                <div className="bg-white  shadow-lg p-8">
                    <h1 className="text-2xl font-bold text-blue-700 mb-6 text-center">
                        Input Bantuan Pemasukan
                    </h1>
                    <form onSubmit={handleSubmit} className="space-y-5">
                        {/* Sumber Bantuan */}
                        <div>
                            <label className="block mb-1 text-sm font-semibold text-gray-700">
                                Sumber Bantuan
                            </label>
                            <input
                                type="text"
                                value={data.sumber_bantuan}
                                onChange={(e) => setData("sumber_bantuan", e.target.value)}
                                className="w-full border border-gray-300 px-4 py-2  focus:ring-2 focus:ring-blue-200 focus:outline-none"
                                required
                                placeholder="Masukkan nama pemberi bantuan"
                            />
                            {errors.sumber_bantuan && (
                                <p className="text-red-500 text-xs mt-1">{errors.sumber_bantuan}</p>
                            )}
                        </div>
                        {/* Keterangan */}
                        <div>
                            <label className="block mb-1 text-sm font-semibold text-gray-700">
                                Keterangan
                            </label>
                            <textarea
                                value={data.keterangan}
                                onChange={(e) => setData("keterangan", e.target.value)}
                                className="w-full border border-gray-300 px-4 py-2  focus:ring-2 focus:ring-blue-200 focus:outline-none"
                                rows={3}
                                placeholder="Keterangan tambahan (opsional)"
                            />
                            {errors.keterangan && (
                                <p className="text-red-500 text-xs mt-1">{errors.keterangan}</p>
                            )}
                        </div>
                        {/* Total Bantuan */}
                        <div>
                            <label className="block mb-1 text-sm font-semibold text-gray-700">
                                Total Bantuan (Rp)
                            </label>
                            <input
                                type="number"
                                min="0"
                                step="0.01"
                                value={data.total}
                                onChange={(e) => setData("total", e.target.value)}
                                className="w-full border border-gray-300 px-4 py-2  focus:ring-2 focus:ring-blue-200 focus:outline-none"
                                required
                                placeholder="Masukkan nominal bantuan"
                            />
                            {errors.total && (
                                <p className="text-red-500 text-xs mt-1">{errors.total}</p>
                            )}
                        </div>
                        {/* Submit Button */}
                        <div>
                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2  transition"
                            >
                                {processing ? "Menyimpan..." : "Simpan Bantuan"}
                            </button>
                        </div>
                    </form>
                    <div className="mt-6">
                        <button
                            onClick={() => setShowHistory(true)}
                            className="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 "
                        >
                            📋 Lihat Riwayat Transaksi
                        </button>
                    </div>
                    {/* Riwayat Transaksi tampil di bawah, bukan modal */}
                    {showHistory && (
                        <div className="mt-8 bg-white rounded-lg p-6 w-full shadow-lg border mx-auto">
                            <h2 className="text-xl font-bold mb-4">
                                Riwayat Transaksi Bantuan
                            </h2>
                            <div className="flex gap-4 mb-4">
                                <div>
                                    <label className="block text-sm">Dari</label>
                                    <input
                                        type="date"
                                        value={fromDate}
                                        onChange={(e) => setFromDate(e.target.value)}
                                        className="border p-2 "
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm">Sampai</label>
                                    <input
                                        type="date"
                                        value={toDate}
                                        onChange={(e) => setToDate(e.target.value)}
                                        className="border p-2 "
                                    />
                                </div>
                            </div>
                            <table className="w-full text-left border">
                                <thead>
                                    <tr className="bg-gray-100">
                                        <th className="p-2 border">Tanggal</th>
                                        <th className="p-2 border">Jumlah</th>
                                        <th className="p-2 border">Total</th>
                                        <th className="p-2 border">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {transactions.length === 0 ? (
                                        <tr>
                                            <td colSpan="4" className="p-4 text-center">
                                                Belum ada transaksi
                                            </td>
                                        </tr>
                                    ) : (
                                        transactions.map((trx) => (
                                            <tr key={trx.id}>
                                                <td className="p-2 border">
                                                    {dayjs(trx.created_at).format("DD/MM/YYYY")}
                                                </td>
                                                <td className="p-2 border">{trx.jumlah_pengguna}</td>
                                                <td className="p-2 border">
                                                    Rp {trx.total.toLocaleString()}
                                                </td>
                                                <td className="p-2 border">
                                                    <button
                                                        onClick={() => deleteTransaction(trx.id)}
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
                            <div className="flex justify-between mt-4">
                                {transactions.length > 0 && (
                                    <button
                                        onClick={deleteAllTransactions}
                                        className="text-red-600 hover:underline"
                                    >
                                        🗑 Hapus Semua
                                    </button>
                                )}
                                <button
                                    onClick={() => setShowHistory(false)}
                                    className="text-gray-600 hover:underline"
                                >
                                    ❌ Tutup
                                </button>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
};

export default BantuanIncomeForm;
