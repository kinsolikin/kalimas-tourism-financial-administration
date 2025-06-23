import React, { useEffect, useState, useRef } from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router } from "@inertiajs/react";
import Swal from "sweetalert2";
import dayjs from "dayjs";

export default function Resto({ auth }) {
    const [showHistory, setShowHistory] = useState(false);
    const [transactions, setTransactions] = useState([]);
    const [fromDate, setFromDate] = useState("");
    const [toDate, setToDate] = useState("");
    const [form, setForm] = useState({
        nama: "",
        makanan: "",
        minuman: "",
        qty_makanan: "",
        qty_minuman: "",
        harga_satuan_makanan: 0,
        harga_satuan_minuman: 0,
        total_makanan: 0,
        total_minuman: 0,
        total: 0,
    });
    const [showShiftSummary, setShowShiftSummary] = useState(false);
    const [closingShift, setClosingShift] = useState(false);
    const [todaySummary, setTodaySummary] = useState({
        resto: [],
        totalResto: 0,
    });
    const shiftSummaryRef = useRef(null);
    const historyRef = useRef(null);

    useEffect(() => {
        // Jika harga satuan diisi manual, gunakan itu, jika tidak, fallback ke menu
        const hargaMakanan =
            form.harga_satuan_makanan !== 0
                ? Number(form.harga_satuan_makanan)
                : menuMakanan[form.makanan] || 0;
        const hargaMinuman =
            form.harga_satuan_minuman !== 0
                ? Number(form.harga_satuan_minuman)
                : menuMinuman[form.minuman] || 0;
        const totalMakanan = hargaMakanan * form.qty_makanan;
        const totalMinuman = hargaMinuman * form.qty_minuman;
        const total = totalMakanan + totalMinuman;

        setForm((prev) => ({
            ...prev,
            harga_satuan_makanan: hargaMakanan,
            harga_satuan_minuman: hargaMinuman,
            total_makanan: totalMakanan,
            total_minuman: totalMinuman,
            total,
        }));
    }, [
        form.makanan,
        form.minuman,
        form.qty_makanan,
        form.qty_minuman,
        form.harga_satuan_makanan,
        form.harga_satuan_minuman,
    ]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setForm((prev) => ({
            ...prev,
            [name]:
                name.includes("qty") || name.includes("harga_satuan")
                    ? Math.max(0, parseInt(value) || 0)
                    : value,
        }));
    };

    const menuMakanan = {
        "Ayam Geprek Nasi": 10000,
        "Lele Bakar Nasi": 13000,
        "Mie Rebus Telor": 7000,
        "Mie Goreng Telor": 7000,
    };

    const menuMinuman = {
        "Es Teh": 3000,
        "Es Jeruk": 4000,
        "Es Kopi": 6000,
    };

    // Ubah showHistorySection agar bisa menerima filter tanggal
    const showHistorySection = async (from = fromDate, to = toDate) => {
        try {
            let url = "/dashboard/resto/transactions";
            let params = {};
            if (from || to) {
                url = "/dashboard/resto/transactions/filter";
                params = { params: { from, to } };
            }
            const res = await axios.get(url, params);
            setTransactions(res.data);
            setShowHistory(true);
        } catch (error) {
            console.error("Gagal mengambil data transaksi", error);
        }
    };

    // Otomatis refresh riwayat saat filter tanggal berubah & saat showHistory true
    useEffect(() => {
        if (showHistory) {
            showHistorySection();
        }
        // eslint-disable-next-line
    }, [fromDate, toDate, showHistory]);

    const handleSubmit = (e) => {
        e.preventDefault();

        router.post("/resto/store", form, {
            onSuccess: () => {
                setForm({
                    nama: "",
                    makanan: "",
                    minuman: "",
                    qty_makanan: "",
                    qty_minuman: "",
                    harga_satuan_makanan: 0,
                    harga_satuan_minuman: 0,
                    total_makanan: 0,
                    total_minuman: 0,
                    total: 0,
                }),
                    Swal.fire({
                        icon: "success",
                        title: "Transaksi Berhasil",
                        text: "Transaksi berhasil disimpan",
                        confirmButtonColor: "#3085d6",
                    });
                if (showHistory) showHistorySection();
            },
            onError: () => {
                Swal.fire({
                    icon: "error",
                    title: "Transaksi Gagal",
                    text: "Terjadi kesalahan saat menyimpan transaksi",
                    confirmButtonColor: "#d33",
                });
            },
        });
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
        }

        try {
            await axios.delete("/dashboard/resto/transactions/delete-all");
            setTransactions([]); // Clear data setelah hapus
            Swal.fire("Berhasil!", "Transaksi telah dihapus.", "success");
            if (showHistory) showHistorySection();
        } catch (error) {
            console.error("Gagal menghapus transaksi", error);
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
                    `/dashboarad/resto/transactions/delete/${id}`
                );
                setTransactions((prev) =>
                    prev.filter((item) => item.id !== id)
                ); // update state

                Swal.fire("Berhasil!", "Transaksi telah dihapus.", "success");
                if (showHistory) showHistorySection();
            } catch (error) {
                Swal.fire("Gagal!", "Gagal menghapus transaksi.", "error");
            }
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

    // Scroll ke riwayat transaksi saat dibuka
    const handleShowHistory = () => {
        setShowHistory((prev) => {
            const next = !prev;
            setTimeout(() => {
                if (!prev && historyRef.current) {
                    historyRef.current.scrollIntoView({ behavior: "smooth" });
                }
            }, 100);
            return next;
        });
    };

    // Helper: Get today's resto transactions and total
    const getTodayTransactions = () => {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const tomorrow = new Date(today);
        tomorrow.setDate(today.getDate() + 1);

        const resto = (transactions || []).filter((t) => {
            const d = new Date(t.created_at);
            return d >= today && d < tomorrow;
        });
        const totalResto = resto.reduce((sum, t) => sum + Number(t.total || 0), 0);
        return {
            resto,
            totalResto,
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
                  // Jika perlu, simpan data shift di sini sebelum logout
                  Swal.fire({
                      title: "Berhasil",
                      text: "Shift berhasil disimpan. Anda akan logout.",
                      icon: "success",
                      timer: 1500,
                      showConfirmButton: false,
                  });
                  setTimeout(() => {
                      router.post('/logout');
                  }, 1500);
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
  

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Kasir" />
            <div className="max-w-6xl mx-auto p-4 grid md:grid-cols-2 gap-6">
                {/* Form Section */}
                <div>
                    <form
                        onSubmit={handleSubmit}
                        className="bg-white p-8 shadow-2xl space-y-6 border max-w-2xl mx-auto"
                    >
                        <h2 className="text-3xl font-bold text-center text-blue-700 mb-6">
                            🍽️ Form Kasir - Loket Resto
                        </h2>

                        {/* Nama Pembeli */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-1">
                                Nama Pembeli
                            </label>
                            <input
                                type="text"
                                name="nama"
                                value={form.nama}
                                onChange={handleChange}
                                required
                                placeholder="Masukkan nama pembeli"
                                className="w-full px-4 py-3 border border-gray-300   shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                            />
                        </div>

                        {/* Pilih Makanan (ubah jadi input) */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-1">
                                Makanan
                            </label>
                            <input
                                type="text"
                                name="makanan"
                                value={form.makanan}
                                onChange={handleChange}
                                placeholder="Masukkan nama makanan"
                                className="w-full px-4 py-3 border border-gray-300   shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                            />
                        </div>

                        {/* Harga Satuan Makanan */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-1">
                                Harga Satuan Makanan
                            </label>
                            <input
                                type="number"
                                name="harga_satuan_makanan"
                                value={form.harga_satuan_makanan}
                                onChange={handleChange}
                                min={0}
                                placeholder="0"
                                className="w-full px-4 py-3 border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                            />
                        </div>

                        {/* Jumlah Makanan */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-1">
                                Jumlah Makanan
                            </label>
                            <input
                                type="number"
                                name="qty_makanan"
                                value={form.qty_makanan}
                                onChange={handleChange}
                                min={0}
                                placeholder="0"
                                className="w-full px-4 py-3 border border-gray-300   shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                            />
                        </div>

                        {/* Pilih Minuman (ubah jadi input) */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-1">
                                Minuman
                            </label>
                            <input
                                type="text"
                                name="minuman"
                                value={form.minuman}
                                onChange={handleChange}
                                placeholder="Masukkan nama minuman"
                                className="w-full px-4 py-3 border border-gray-300   shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                            />
                        </div>

                        {/* Harga Satuan Minuman */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-1">
                                Harga Satuan Minuman
                            </label>
                            <input
                                type="number"
                                name="harga_satuan_minuman"
                                value={form.harga_satuan_minuman}
                                onChange={handleChange}
                                min={0}
                                placeholder="0"
                                className="w-full px-4 py-3 border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                            />
                        </div>

                        {/* Jumlah Minuman */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-1">
                                Jumlah Minuman
                            </label>
                            <input
                                type="number"
                                name="qty_minuman"
                                value={form.qty_minuman}
                                onChange={handleChange}
                                min={0}
                                placeholder="0"
                                className="w-full px-4 py-3 border border-gray-300   shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                            />
                        </div>

                        {/* Submit Button */}
                        <button
                            type="submit"
                            className="w-full bg-blue-600 text-white font-semibold py-3 shadow-md hover:bg-blue-700 transition duration-200 ease-in-out focus:ring-4 focus:ring-blue-300"
                        >
                            💾 Simpan Transaksi
                        </button>
                    </form>
                </div>
                {/* Right Section: Ringkasan, Tombol, dan Output */}
                <div className="p-6 flex flex-col items-center">
                    <div className="bg-gray-50 p-6 shadow-lg border border-gray-200 space-y-4 max-w-2xl w-full mx-auto">
                        <h3 className="text-2xl font-bold text-blue-700 text-center mb-6">
                            🧾 Ringkasan Transaksi
                        </h3>

                        {/* Makanan */}
                        <div className="grid grid-cols-2 gap-2 text-sm text-gray-700">
                            <div>Harga Satuan Makanan:</div>
                            <div className="text-right font-medium text-gray-900">
                                Rp {form.harga_satuan_makanan.toLocaleString()}
                            </div>

                            <div>Jumlah Makanan:</div>
                            <div className="text-right">{form.qty_makanan}</div>

                            <div>Total Makanan:</div>
                            <div className="text-right font-semibold text-green-700">
                                Rp {form.total_makanan.toLocaleString()}
                            </div>
                        </div>

                        <hr className="my-3" />

                        {/* Minuman */}
                        <div className="grid grid-cols-2 gap-2 text-sm text-gray-700">
                            <div>Harga Satuan Minuman:</div>
                            <div className="text-right font-medium text-gray-900">
                                Rp {form.harga_satuan_minuman.toLocaleString()}
                            </div>

                            <div>Jumlah Minuman:</div>
                            <div className="text-right">{form.qty_minuman}</div>

                            <div>Total Minuman:</div>
                            <div className="text-right font-semibold text-green-700">
                                Rp {form.total_minuman.toLocaleString()}
                            </div>
                        </div>

                        <hr className="my-4" />

                        {/* Total */}
                        <div className="text-xl font-bold text-gray-800 text-center">
                            💰 Total Pembayaran:{" "}
                            <span className="text-blue-700">
                                Rp {form.total.toLocaleString()}
                            </span>
                        </div>
                        {/* Tombol Riwayat */}
                        <button
                            className="w-full mt-6 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 shadow-md transition duration-200 ease-in-out focus:outline-none focus:ring-4 focus:ring-green-300"
                            onClick={e => {
                                e.preventDefault();
                                handleShowHistory();
                            }}
                        >
                            {showHistory ? "Tutup Riwayat" : "📋 Lihat Riwayat Transaksi"}
                        </button>
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
                    {/* Rincian Shift Hari Ini */}
                    {showShiftSummary && (
                        <div
                            ref={shiftSummaryRef}
                            className="mt-8 bg-gray-50 border border-gray-200 rounded-xl p-6 shadow-lg w-full max-w-2xl"
                        >
                            <h4 className="text-lg font-bold mb-4 text-center text-green-700">
                                Rincian Transaksi Shift Hari Ini
                            </h4>
                            <div className="mb-4">
                                <strong>Transaksi Resto:</strong>
                                <ul className="list-disc ml-6">
                                    {todaySummary.resto.length > 0 ? (
                                        todaySummary.resto.map((t, i) => (
                                            <li key={i}>
                                                {t.name_customer || "-"} | Makanan: {t.makanan || "-"} | Minuman: {t.minuman || "-"} | Rp {t.total} |{" "}
                                                {new Date(t.created_at).toLocaleTimeString("id-ID")}
                                            </li>
                                        ))
                                    ) : (
                                        <li>Tidak ada transaksi resto hari ini.</li>
                                    )}
                                </ul>
                                <div className="mt-2 text-right font-semibold text-blue-700">
                                    Total Resto: Rp {todaySummary.totalResto}
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

                    {/* Riwayat Transaksi tampil di bawah, bukan modal */}
                    {showHistory && (
                        <div
                            ref={historyRef}
                            className="mt-8 bg-white p-6 w-full max-w-2xl shadow-lg border mx-auto"
                        >
                            <h2 className="text-xl font-semibold mb-4">
                                Riwayat Transaksi
                            </h2>
                            <div className="flex gap-4 mb-4">
                                <div>
                                    <label className="block text-sm mb-1">
                                        Dari Tanggal
                                    </label>
                                    <input
                                        type="date"
                                        value={fromDate}
                                        onChange={(e) =>
                                            setFromDate(e.target.value)
                                        }
                                        className="border p-2  "
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm mb-1">
                                        Sampai Tanggal
                                    </label>
                                    <input
                                        type="date"
                                        value={toDate}
                                        onChange={(e) =>
                                            setToDate(e.target.value)
                                        }
                                        className="border p-2  "
                                    />
                                </div>
                            </div>
                            {/* Daftar Transaksi */}
                            <ul className="space-y-2 max-h-64 overflow-y-auto">
                                {transactions.map((t, i) => (
                                    <li
                                        key={i}
                                        className="border p-2   shadow-sm"
                                    >
                                        <div className="font-medium">
                                            Nama Cust : {t.name_customer}
                                        </div>
                                        <div className="text-sm text-gray-500">
                                            Makanan : {t.makanan}
                                        </div>
                                        <div className="text-sm text-gray-500">
                                            Minuman : {t.minuman}
                                        </div>
                                        <div className="text-sm text-gray-500">
                                            Jumlah Makanan : {t.qty_makanan}
                                        </div>
                                        <div className="text-sm text-gray-500">
                                            Jumlah Minuman : {t.qty_minuman}
                                        </div>
                                        <div className="text-sm text-gray-500">
                                            Harga Satuan makanan :{" "}
                                            {t.harga_satuan_makanan}
                                        </div>
                                        <div className="text-sm text-gray-500">
                                            Harga Satuan minuman :
                                            {t.harga_satuan_minuman}
                                        </div>
                                        <div className="font-medium">
                                            Total Pembayaran :{t.total}
                                        </div>
                                        <div className="text-sm text-gray-500">
                                            Tanggal Transaksi :
                                            {dayjs(t.created_at).format(
                                                "DD MMMM YYYY"
                                            )}
                                        </div>
                                        <br />
                                        <button
                                            onClick={() =>
                                                deleteTransaction(t.id)
                                            }
                                            className="bg-red-500 text-white px-2 py-1   hover:bg-red-600 text-sm"
                                        >
                                            Hapus
                                        </button>
                                    </li>
                                ))}
                                {transactions.length === 0 && (
                                    <p className="text-gray-500">
                                        Tidak ada transaksi.
                                    </p>
                                )}
                            </ul>
                            {/* Tombol Hapus Semua */}
                            {transactions.length > 0 && (
                                <button
                                    onClick={deleteAllTransactions}
                                    className="mt-4 bg-red-600 text-white px-4 py-2   hover:bg-red-700"
                                >
                                    Hapus Semua Transaksi
                                </button>
                            )}
                            {/* Tombol Tutup Riwayat */}
                            <button
                                className="absolute top-2 right-2 text-gray-500 hover:text-black"
                                onClick={handleShowHistory}
                                style={{ position: "absolute", top: 10, right: 20 }}
                            >
                                ✕
                            </button>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
