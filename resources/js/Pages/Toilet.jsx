import React, { useEffect, useState } from "react";
import { useForm } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import Swal from "sweetalert2";
import dayjs from "dayjs";
import axios from "axios";

const Toilet = ({ auth }) => {
    const [showModal, setShowModal] = useState(false);
    const [transactions, setTransactions] = useState([]);
    const [fromDate, setFromDate] = useState("");
    const [toDate, setToDate] = useState("");

    const fixedHarga = 2000;

    const { data, setData, post, processing, errors } = useForm({
       
        jumlah: 0,
        harga_perorang: 2000,
        total: 0,
    });

    useEffect(() => {
        setData((prev) => ({
            ...prev,
            total: fixedHarga * prev.jumlah,
        }));
    }, [data.jumlah]);

    const handleSubmit = (e) => {
        e.preventDefault();

        post("/dashboard/toilet/store", {
            onSuccess: () => {
                setData({
                    jumlah: 0,
                    harga_perorang: 2000,
                    total: 0,
                });
                Swal.fire("Berhasil", "Data Toilet berhasil disimpan", "success");
            },
            onError: () => {
                Swal.fire("Gagal", "Data Toilet gagal disimpan", "error");
            },
        });
    };

    const openModal = async () => {
        try {
            const res = await axios.get("/dashboard/toilet/transactions");
            setTransactions(res.data);
            setShowModal(true);
        } catch (err) {
            console.error("Gagal mengambil data transaksi", err);
        }
    };

    const fetchTransactions = async () => {
        try {
            const res = await axios.get("/dashboard/toilet/transactions/filter", {
                params: { from: fromDate, to: toDate },
            });
            setTransactions(res.data);
        } catch (err) {
            console.error("Gagal filter data", err);
        }
    };

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
                await axios.delete("/dashboard/toilet/transactions/delete-all");
                setTransactions([]);
                Swal.fire("Berhasil!", "Semua transaksi telah dihapus.", "success");
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
                await axios.delete(`/dashboard/toilet/transactions/delete/${id}`);
                setTransactions((prev) => prev.filter((t) => t.id !== id));
                Swal.fire("Berhasil!", "Transaksi telah dihapus.", "success");
            } catch (err) {
                Swal.fire("Gagal!", "Gagal menghapus transaksi.", "error");
            }
        }
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <div className="max-w-3xl mx-auto px-4 py-6">
                <h1 className="text-2xl font-bold text-center mb-4">Form Pemasukan Toilet</h1>

                <div className="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded-md">
                    <p><strong>Informasi:</strong> Setiap 1 orang pengguna toilet dikenakan biaya <strong>Rp 2.000</strong>.</p>
                </div>

                <form
                    onSubmit={handleSubmit}
                    className="bg-white p-6 rounded-xl shadow-md space-y-6"
                >
                    <div>
                        <label htmlFor="jumlah" className="block mb-1 font-medium">
                            Jumlah Pengguna Toilet
                        </label>
                        <input
                            type="number"
                            id="jumlah"
                            min="1"
                            value={data.jumlah}
                            onChange={(e) => setData("jumlah", parseInt(e.target.value))}
                            className="w-full border px-4 py-2 rounded-lg"
                            required
                        />
                        {errors.jumlah && (
                            <p className="text-red-500 text-sm">{errors.jumlah}</p>
                        )}
                    </div>

                    <div className="text-lg font-semibold">
                        Total Bayar: Rp {data.total.toLocaleString()}
                    </div>

                    <button
                        type="submit"
                        disabled={processing}
                        className="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg"
                    >
                        {processing ? "Menyimpan..." : "Simpan"}
                    </button>
                </form>

                <div className="mt-6">
                    <button
                        onClick={openModal}
                        className="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl"
                    >
                        📋 Lihat Riwayat Transaksi
                    </button>
                </div>

                {showModal && (
                    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                        <div className="bg-white w-full max-w-2xl p-6 rounded-xl relative">
                            <h2 className="text-xl font-bold mb-4">
                                Riwayat Transaksi Toilet
                            </h2>

                            <div className="flex gap-4 mb-4">
                                <div>
                                    <label className="block text-sm">Dari</label>
                                    <input
                                        type="date"
                                        value={fromDate}
                                        onChange={(e) => setFromDate(e.target.value)}
                                        className="border p-2 rounded"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm">Sampai</label>
                                    <input
                                        type="date"
                                        value={toDate}
                                        onChange={(e) => setToDate(e.target.value)}
                                        className="border p-2 rounded"
                                    />
                                </div>
                                <div className="flex items-end">
                                    <button
                                        onClick={fetchTransactions}
                                        className="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                                    >
                                        Filter
                                    </button>
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
                                <button
                                    onClick={deleteAllTransactions}
                                    className="text-red-600 hover:underline"
                                >
                                    🗑 Hapus Semua
                                </button>
                                <button
                                    onClick={() => setShowModal(false)}
                                    className="text-gray-600 hover:underline"
                                >
                                    ❌ Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
};

export default Toilet;
