import React, { useEffect, useState } from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router } from "@inertiajs/react";
import Swal from "sweetalert2";
import dayjs from "dayjs";
import axios from "axios"; // pastikan axios diimport

function Expanse({ expanses = [], users = [], auth }) {
    const [form, setForm] = useState({
        expanse_id: "",
        amount: "",
        description: "",
    });
    const [transactions, setTransactions] = useState([]);

    // Ambil data transaksi saat mount & setelah submit/hapus
    const fetchTransactions = async () => {
        try {
            const res = await axios.get("/dashboard/expanse/transactions");
            setTransactions(res.data.expanses || []);
        } catch (err) {
            Swal.fire({
                title: "Gagal",
                text: "Gagal mengambil data transaksi",
                icon: "error",
                confirmButtonText: "OK",
            });
        }
    };

    useEffect(() => {
        fetchTransactions();
    }, []);

    const handleChange = (e) => {
        setForm({ ...form, [e.target.name]: e.target.value });
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        router.post("/dashboard/expanses/store", form, {
            onSuccess: () => {
                setForm({ expanse_id: "", amount: "", description: "" });
                Swal.fire({
                    title: "Berhasil",
                    text: "Transaksi pengeluaran berhasil disimpan",
                    icon: "success",
                    confirmButtonText: "OK",
                });
                fetchTransactions();
            },
            onError: () => {
                Swal.fire({
                    title: "Gagal",
                    text: "Terjadi kesalahan saat menyimpan transaksi",
                    icon: "error",
                    confirmButtonText: "OK",
                });
            },
        });
    };

    const handleDelete = async (id) => {
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
                await axios.delete(`/dashboard/expanse/transactions/delete/${id}`);
                Swal.fire("Berhasil!", "Transaksi telah dihapus.", "success");
                fetchTransactions();
            } catch (err) {
                Swal.fire("Gagal!", "Gagal menghapus transaksi.", "error");
            }
        }
    };

    // Temukan expanse_id untuk mendadak dan operasional
    const mendadak = expanses.find((e) => e.name?.toLowerCase() === "mendadak");
    const operasional = expanses.find(
        (e) => e.name?.toLowerCase() === "operasional"
    );

    return (
        <AuthenticatedLayout user={auth.user}>
            <div className="max-w-xl mx-auto py-8">
                <form
                    onSubmit={handleSubmit}
                    className="bg-white shadow rounded-lg p-6 space-y-4"
                >
                    <h2 className="text-2xl font-bold mb-4">
                        Tambah Transaksi Pengeluaran
                    </h2>
                    {/* Jenis Pengeluaran Pilihan */}
                    <div>
                        <label className="block text-sm font-medium mb-1">
                            Jenis Pengeluaran
                        </label>
                        <div className="flex gap-4 mt-2">
                            <label className="flex items-center cursor-pointer">
                                <input
                                    type="radio"
                                    name="expanse_id"
                                    value="1"
                                    checked={form.expanse_id === "1"}
                                    onChange={handleChange}
                                    className="form-radio text-blue-600"
                                    required
                                />
                                <span className="ml-2">Operasional</span>
                            </label>

                            <label className="flex items-center cursor-pointer">
                                <input
                                    type="radio"
                                    name="expanse_id"
                                    value="2"
                                    checked={form.expanse_id === "2"}
                                    onChange={handleChange}
                                    className="form-radio text-blue-600"
                                    required
                                />
                                <span className="ml-2">Mendadak</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label className="block text-sm font-medium mb-1">
                            Jumlah
                        </label>
                        <input
                            type="number"
                            name="amount"
                            value={form.amount}
                            onChange={handleChange}
                            className="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="0.00"
                            min="0"
                            step="0.01"
                            required
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium mb-1">
                            Deskripsi
                        </label>
                        <input
                            type="text"
                            name="description"
                            value={form.description}
                            onChange={handleChange}
                            className="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="Opsional"
                        />
                    </div>
                    <button
                        type="submit"
                        className="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition font-semibold"
                    >
                        Simpan
                    </button>
                </form>

                {/* Riwayat transaksi selalu tampil di bawah form */}
                <div className="mt-6 bg-white shadow rounded-lg p-6">
                    <h3 className="text-xl font-bold mb-4">
                        Riwayat Transaksi
                    </h3>
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr>
                                    <th className="px-2 py-1 text-left">
                                        Jenis Pengeluaran
                                    </th>
                                    <th className="px-2 py-1 text-right">
                                        Jumlah
                                    </th>
                                    <th className="px-2 py-1 text-left">
                                        Deskripsi
                                    </th>
                                    <th className="px-2 py-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                {transactions.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={4}
                                            className="text-center py-2 text-gray-400"
                                        >
                                            Belum ada transaksi
                                        </td>
                                    </tr>
                                )}
                                {transactions.map((trx) => (
                                    <tr key={trx.id} className="border-t">
                                        <td className="px-2 py-1">
                                            {trx.expanse_category &&
                                            trx.expanse_category.name
                                                ? trx.expanse_category.name
                                                : ""}
                                        </td>
                                        <td className="px-2 py-1 text-right">
                                            {Number(
                                                trx.amount
                                            ).toLocaleString("id-ID", {
                                                style: "currency",
                                                currency: "IDR",
                                            })}
                                        </td>
                                        <td className="px-2 py-1">
                                            {trx.expanse_mendadak
                                                ? trx.expanse_mendadak.description
                                                : trx.expanse_operasional
                                                ? trx.expanse_operasional.description
                                                : null}
                                        </td>
                                        <td className="px-2 py-1">
                                            <button
                                                onClick={() =>
                                                    handleDelete(trx.id)
                                                }
                                                className="text-red-500 hover:underline"
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

export default Expanse;
