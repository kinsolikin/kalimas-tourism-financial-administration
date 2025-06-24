import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm, usePage } from "@inertiajs/react";
import { useEffect, useState, useRef } from "react";
import Swal from "sweetalert2";
import axios from "axios";
import { router } from "@inertiajs/react";

export default function Dashboard({ auth }) {
    const { props } = usePage();
    const { jenisKendaraan,priceticket } = usePage().props;

    console.log("priceticket:", priceticket);
    const [transactionHistory, setTransactionHistory] = useState({
        parkingTransactions: [],
        ticketTransactions: [],
    });
    const [filterCategory, setFilterCategory] = useState("all"); // State for filtering category
    const [startDate, setStartDate] = useState("");
    const [endDate, setEndDate] = useState("");
    const [showShiftSummary, setShowShiftSummary] = useState(false);
    const [closingShift, setClosingShift] = useState(false);
    const [todaySummary, setTodaySummary] = useState({
        parking: [],
        ticket: [],
        totalParking: 0,
        totalTicket: 0,
        totalAll: 0,
    });
    const [showTransactionHistory, setShowTransactionHistory] = useState(false);
    // Tambahkan ref untuk scroll
    const transactionHistoryRef = useRef(null);
    const shiftSummaryRef = useRef(null);

    const { data, setData, post, processing, errors } = useForm({
        shift: "1",
        operator_name: "",
        vehicle_type: "",
        price: 0,
        jumlah_tiket: 0,
        harga_tiket: 0,
        jam_masuk: new Date().toLocaleTimeString("en-GB", {
            hour: "2-digit",
            minute: "2-digit",
        }), // Default to current time
        jam_keluar: "16:00",
    });


 
    useEffect(() => {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, "0");
        const minutes = String(now.getMinutes()).padStart(2, "0");
        setData("jam_masuk", `${hours}:${minutes}`);
    }, []);

    const handleChange = (e) => {
        const { name, value } = e.target;
        if (name === "vehicle_type") {
            // Cari kendaraan berdasarkan id
            const selectedVehicle = jenisKendaraan.find((item) => item.id == value);
            setData({
                ...data,
                vehicle_type: value, // id kendaraan
                price: selectedVehicle ? selectedVehicle.price : 0,
            });
        } else if (name === "jumlah_tiket") {
            // Gunakan harga tiket dari props priceticket
            const hargaTiket = Number(priceticket);
            setData({
                ...data,
                jumlah_tiket: value,
                harga_tiket: value * hargaTiket,
            });
        } else {
            setData({
                ...data,
                [name]: value,
            });
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post("/dashboard/store", {
            onSuccess: () => {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: "Data berhasil disimpan.",
                    confirmButtonColor: "#3085d6",
                }),
                    // Reset form fields
                    setData({
                        shift: data.shift,
                        operator_name: "",
                        vehicle_type: "",
                        price: 0,
                        jumlah_tiket: 0,
                        harga_tiket: 0,
                        jam_masuk: new Date().toLocaleTimeString("en-GB", {
                            hour: "2-digit",
                            minute: "2-digit",
                        }),
                        jam_keluar: "16:00",
                    });
            },
            onError: () => {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: "Terjadi kesalahan saat menyimpan data.",
                    confirmButtonColor: "#3085d6",
                });
            },
        });
    };

    const fetchTransactionHistory = async () => {
        try {
            const response = await axios.get("/dashboard/transaction-history");
            const parkingTransactions = response.data.parkingTransactions || [];
            const ticketTransactions = response.data.ticketTransactions || [];
            setTransactionHistory({ parkingTransactions, ticketTransactions });

            console.log(response.data);
        } catch (error) {
            console.error("Error fetching transaction history:", error);
        }
    };


    const filteredTransactions = () => {
        const transactionsByDate = filterTransactionsByDate();

        return {
            parkingTransactions:
                filterCategory === "all" || filterCategory === "parking"
                    ? transactionsByDate.parkingTransactions || []
                    : [],
            ticketTransactions:
                filterCategory === "all" || filterCategory === "ticket"
                    ? transactionsByDate.ticketTransactions || []
                    : [],
        };
    };

    const filterTransactionsByDate = () => {
        if (!startDate || !endDate) return transactionHistory;

        const start = new Date(startDate);
        const end = new Date(endDate);
        end.setHours(23, 59, 59, 999); // Include the entire end date

        const filteredParking = transactionHistory.parkingTransactions.filter(
            (transaction) => {
                const transactionDate = new Date(transaction.created_at);
                return transactionDate >= start && transactionDate <= end;
            }
        );

        const filteredTickets = transactionHistory.ticketTransactions.filter(
            (transaction) => {
                const transactionDate = new Date(transaction.created_at);
                return transactionDate >= start && transactionDate <= end;
            }
        );

        return {
            parkingTransactions: filteredParking,
            ticketTransactions: filteredTickets,
        };
    };

    const deleteTransaction = async (type, id) => {
        try {
            const url =
                type === "Parking"
                    ? `/dashboard/parking/${id}`
                    : `/dashboard/ticket/${id}`;
            await axios.delete(url);
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: "Data berhasil dihapus.",
            });
            fetchTransactionHistory(); // Refresh the transaction history
        } catch (error) {
            console.error("Error deleting transaction:", error);
            Swal.fire({
                icon: "error",
                title: "Gagal",
                text: "Terjadi kesalahan saat menghapus data.",
            });
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
                await axios.delete("/dashboard/transactions/delete-all");
                Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: "Semua transaksi berhasil dihapus.",
                });
                fetchTransactionHistory(); // Refresh the transaction history
            } catch (error) {
                console.error("Error deleting all transactions:", error);
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: "Terjadi kesalahan saat menghapus semua transaksi.",
                });
            }
        }
    };

    // Fetch transaction history on mount and after changes
    useEffect(() => {
        fetchTransactionHistory();
    }, []);

    // Tambahkan effect agar data diperbarui setelah submit
    useEffect(() => {
        if (!processing) {
            fetchTransactionHistory();
        }
        // eslint-disable-next-line
    }, [processing]);

    // Helper: Filter transaksi hari ini
    const getTodayTransactions = () => {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const tomorrow = new Date(today);
        tomorrow.setDate(today.getDate() + 1);

        const parking = (transactionHistory.parkingTransactions || []).filter(
            (t) => {
                const d = new Date(t.created_at);
                return d >= today && d < tomorrow;
            }
        );
        const ticket = (transactionHistory.ticketTransactions || []).filter(
            (t) => {
                const d = new Date(t.created_at);
                return d >= today && d < tomorrow;
            }
        );
        // Pastikan penjumlahan dengan Number()
        const totalParking = parking.reduce(
            (sum, t) => sum + Number(t.total || 0),
            0
        );
        const totalTicket = ticket.reduce(
            (sum, t) => sum + Number(t.total || 0),
            0
        );
        return {
            parking,
            ticket,
            totalParking,
            totalTicket,
            totalAll: totalParking + totalTicket,
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
                    router.post("/logout");
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

    // Scroll ke riwayat transaksi saat dibuka
    const handleShowTransactionHistory = () => {
        setShowTransactionHistory((prev) => {
            const next = !prev;
            setTimeout(() => {
                if (!prev && transactionHistoryRef.current) {
                    transactionHistoryRef.current.scrollIntoView({
                        behavior: "smooth",
                    });
                }
            }, 100); // beri delay agar render dulu
            return next;
        });
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
            <Head title="Kasir" />
            <div className="py-2">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-none shadow-none   -nonw p-6 md:p-8 mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        {/* Left Column: Form */}
                        <div>
                            <h2 className="text-2xl font-bold text-center text-blue-700 mb-6">
                                Form Kasir Parkir
                            </h2>
                            <form
                                onSubmit={handleSubmit}
                                className="space-y-6 bg-white p-8   -2xl shadow-2xl border border-gray-200 max-w-xl mx-auto"
                            >
                                {/* Shift Kerja */}
                                <div>
                                    <label
                                        htmlFor="shift"
                                        className="block text-sm font-semibold text-gray-700 mb-2"
                                    >
                                        Shift Kerja
                                    </label>
                                    <select
                                        id="shift"
                                        name="shift"
                                        value={data.shift}
                                        onChange={handleChange}
                                        className="w-full px-4 py-2 text-gray-800 bg-gray-50 border border-gray-300   shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="1">Shift 1</option>
                                        <option value="2">Shift 2</option>
                                    </select>
                                    <small className="text-gray-500">
                                        Pilih shift kerja Anda.
                                    </small>
                                </div>

                                {/* Operator */}
                                <div>
                                    <label
                                        htmlFor="operator_name"
                                        className="block text-sm font-semibold text-gray-700 mb-2"
                                    >
                                        Nama Operator Kasir
                                    </label>
                                    <input
                                        type="text"
                                        id="operator_name"
                                        name="operator_name"
                                        value={data.operator_name}
                                        onChange={handleChange}
                                        placeholder="Masukkan nama operator"
                                        className="w-full px-4 py-2 text-gray-800 bg-gray-50 border border-gray-300   shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                {/* Jenis Kendaraan */}
                                <div>
                                    <label
                                        htmlFor="vehicle_type"
                                        className="block text-sm font-semibold text-gray-700 mb-2"
                                    >
                                        Jenis Kendaraan Parkir
                                    </label>
                                    <select
                                        id="vehicle_type"
                                        name="vehicle_type"
                                        value={data.vehicle_type}
                                        onChange={handleChange}
                                        className="w-full px-4 py-2 text-gray-800 bg-gray-50 border border-gray-300   shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="">
                                            Pilih Kendaraan
                                        </option>
                                        {jenisKendaraan.map((item) => (
                                            <option
                                                key={item.id}
                                                value={item.id}
                                            >
                                                {item.namakendaraan} - Rp {item.price.toLocaleString("id-ID")}
                                            </option>
                                        ))}
                                    </select>
                                    <small className="text-gray-500">
                                        Pilih jenis kendaraan yang diparkir.
                                    </small>
                                </div>

                                {/* Jumlah Tiket */}
                                <div>
                                    <label
                                        htmlFor="jumlah_tiket"
                                        className="block text-sm font-semibold text-gray-700 mb-2"
                                    >
                                        Jumlah Tiket Wisata
                                    </label>
                                    <input
                                        type="number"
                                        id="jumlah_tiket"
                                        name="jumlah_tiket"
                                        value={data.jumlah_tiket}
                                        onChange={handleChange}
                                        min="0"
                                        placeholder="Masukkan jumlah tiket"
                                        className="w-full px-4 py-2 text-gray-800 bg-gray-50 border border-gray-300   shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                {/* Jam Masuk */}
                                <div>
                                    <label
                                        htmlFor="jam_masuk"
                                        className="block text-sm font-semibold text-gray-700 mb-2"
                                    >
                                        Waktu Masuk Kendaraan
                                    </label>
                                    <input
                                        type="time"
                                        id="jam_masuk"
                                        name="jam_masuk"
                                        value={
                                            data.jam_masuk ||
                                            new Date().toLocaleTimeString(
                                                "en-GB",
                                                {
                                                    hour: "2-digit",
                                                    minute: "2-digit",
                                                }
                                            )
                                        }
                                        onChange={handleChange}
                                        className="w-full px-4 py-2 text-gray-800 bg-gray-50 border border-gray-300   shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                {/* Jam Keluar */}
                                <div>
                                    <label
                                        htmlFor="jam_keluar"
                                        className="block text-sm font-semibold text-gray-700 mb-2"
                                    >
                                        Waktu Keluar Kendaraan
                                    </label>
                                    <input
                                        type="time"
                                        id="jam_keluar"
                                        name="jam_keluar"
                                        value={data.jam_keluar}
                                        onChange={handleChange}
                                        className="w-full px-4 py-2 text-gray-800 bg-gray-50 border border-gray-300   shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                {/* Submit Button */}
                                <div className="pt-4">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="w-full py-3 text-white text-base font-semibold bg-blue-600 hover:bg-blue-700   shadow-md transition duration-200 ease-in-out focus:ring-4 focus:ring-blue-300"
                                    >
                                        Simpan Data
                                    </button>
                                </div>
                            </form>
                        </div>
                        {/* Right Column: Total Results */}
                        <div className="mt-6 md:mt-0">
                            <h3 className="text-2xl font-bold mb-6 text-blue-700 text-center">
                                Ringkasan Pembayaran
                            </h3>
                            <div className="space-y-4 bg-gradient-to-br from-white via-gray-50 to-gray-100 p-6   -2xl shadow-xl border border-gray-200">
                                {/* Baris Biaya Parkir */}
                                <div className="flex justify-between items-center border-b border-dashed pb-4">
                                    <p className="text-base font-semibold text-gray-700">
                                        Biaya Parkir
                                    </p>
                                    <p className="text-xl font-bold text-blue-600">
                                        Rp {data.price ? data.price.toLocaleString("id-ID") : 0}
                                    </p>
                                </div>

                                {/* Baris Total Harga Tiket */}
                                <div className="flex justify-between items-center border-b border-dashed pb-4">
                                    <p className="text-base font-semibold text-gray-700">
                                        Total Harga Tiket
                                    </p>
                                    <p className="text-xl font-bold text-blue-600">
                                        Rp {data.harga_tiket}
                                    </p>
                                </div>

                                {/* Baris Jumlah Kendaraan */}
                                <div className="flex justify-between items-center border-b border-dashed pb-4">
                                    <p className="text-base font-semibold text-gray-700">
                                        Jumlah Kendaraan
                                    </p>
                                    <p className="text-xl font-bold text-blue-600">
                                        {data.vehicle_type ? 1 : 0}
                                    </p>
                                </div>

                                {/* Baris Jumlah Tiket */}
                                <div className="flex justify-between items-center border-b border-dashed pb-4">
                                    <p className="text-base font-semibold text-gray-700">
                                        Jumlah Tiket
                                    </p>
                                    <p className="text-xl font-bold text-blue-600">
                                        {data.jumlah_tiket}
                                    </p>
                                </div>

                                {/* Total Pembayaran */}
                                <div className="flex justify-between items-center pt-2">
                                    <p className="text-lg font-bold text-gray-900 uppercase">
                                        Total Pembayaran
                                    </p>
                                    <p className="text-2xl font-extrabold text-green-600">
                                        Rp {Number(data.price) + Number(data.harga_tiket)}
                                    </p>
                                </div>
                            </div>
                            {/* Tombol Riwayat Transaksi */}
                            <div className="mt-8 flex flex-col items-center">
                                <button
                                    onClick={handleShowTransactionHistory}
                                    className=" w-full px-6 py-3 bg-purple-600 text-white  font-bold hover:bg-purple-700 focus:ring-4 focus:ring-purple-300 mb-4"
                                >
                                    {showTransactionHistory
                                        ? "Tutup Riwayat"
                                        : "Riwayat Transaksi"}
                                </button>
                            </div>

                            {/* Tombol Akhiri Shift */}
                            <div className="mt-8 flex flex-col items-center">
                                <button
                                    onClick={handleShowShiftSummary}
                                    className="  w-full px-6 py-3 bg-green-600 text-white  font-bold hover:bg-green-700 focus:ring-4 focus:ring-green-300"
                                >
                                    {showShiftSummary
                                        ? "Tutup Rincian Shift"
                                        : "Akhiri Shift"}
                                </button>
                            </div>
                            {/* Rincian Shift Hari Ini */}
                        </div>
                    </div>

                    {showTransactionHistory && (
                        <div ref={transactionHistoryRef} className="mt-2">
                            <div className="max-w-4xl mx-auto bg-white p-6   shadow-lg">
                                <h3 className="text-lg font-bold mb-4 text-center md:text-left">
                                    Riwayat Transaksi
                                </h3>
                                {/* Date Filter Inputs */}
                                <div className="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
                                    <div className="w-full md:w-1/2 pr-2">
                                        <label
                                            htmlFor="start_date"
                                            className="block text-sm font-medium text-gray-700"
                                        >
                                            Tanggal Mulai:
                                        </label>
                                        <input
                                            type="date"
                                            id="start_date"
                                            value={startDate}
                                            onChange={(e) =>
                                                setStartDate(e.target.value)
                                            }
                                            className="mt-1 block w-full border-gray-300    shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        />
                                    </div>
                                    <div className="w-full md:w-1/2 pl-2">
                                        <label
                                            htmlFor="end_date"
                                            className="block text-sm font-medium text-gray-700"
                                        >
                                            Tanggal Akhir:
                                        </label>
                                        <input
                                            type="date"
                                            id="end_date"
                                            value={endDate}
                                            onChange={(e) =>
                                                setEndDate(e.target.value)
                                            }
                                            className="mt-1 block w-full border-gray-300    shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        />
                                    </div>
                                </div>
                                {/* Filter Buttons */}
                                <div className="flex flex-wrap justify-center space-x-2 mb-6">
                                    <button
                                        onClick={() => setFilterCategory("all")}
                                        className={`px-4 py-2   ${
                                            filterCategory === "all"
                                                ? "bg-blue-600 text-white"
                                                : "bg-gray-200 text-gray-700"
                                        }`}
                                    >
                                        Semua
                                    </button>
                                    <button
                                        onClick={() =>
                                            setFilterCategory("parking")
                                        }
                                        className={`px-4 py-2   ${
                                            filterCategory === "parking"
                                                ? "bg-blue-600 text-white"
                                                : "bg-gray-200 text-gray-700"
                                        }`}
                                    >
                                        Parkir
                                    </button>
                                    <button
                                        onClick={() =>
                                            setFilterCategory("ticket")
                                        }
                                        className={`px-4 py-2   ${
                                            filterCategory === "ticket"
                                                ? "bg-blue-600 text-white"
                                                : "bg-gray-200 text-gray-700"
                                        }`}
                                    >
                                        Tiket
                                    </button>
                                </div>
                                {/* Reset Filter Button */}
                                <div className="flex justify-end mb-6">
                                    <button
                                        onClick={() => {
                                            setStartDate("");
                                            setEndDate("");
                                            setFilterCategory("all");
                                        }}
                                        className="px-4 py-2 bg-red-600 text-white   hover:bg-red-700 focus:ring-4 focus:ring-red-300"
                                    >
                                        Reset Filter
                                    </button>
                                </div>
                                {/* Delete All Transactions Button */}
                                <div className="flex justify-end mb-6">
                                    <button
                                        onClick={deleteAllTransactions}
                                        className="px-4 py-2 bg-red-600 text-white   hover:bg-red-700 focus:ring-4 focus:ring-red-300"
                                    >
                                        Hapus Semua Transaksi
                                    </button>
                                </div>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6 max-h-[500px] overflow-y-auto">
                                    {/* Column for Parking Transactions */}
                                    <div>
                                        <h4 className="text-md font-semibold text-center md:text-left">
                                            Transaksi Parkir
                                        </h4>
                                        <hr />
                                        <br />
                                        {filteredTransactions()
                                            .parkingTransactions.length > 0 ? (
                                            filteredTransactions().parkingTransactions.map(
                                                (transaction, index) => (
                                                    <div
                                                        key={index}
                                                        className="border-b pb-2"
                                                    >
                                                        <p className="text-sm text-gray-700">
                                                            <strong>
                                                                Waktu Input:
                                                            </strong>{" "}
                                                            {new Date(
                                                                transaction.created_at
                                                            ).toLocaleString(
                                                                "id-ID",
                                                                {
                                                                    year: "numeric",
                                                                    month: "long",
                                                                    day: "numeric",
                                                                    hour: "2-digit",
                                                                    minute: "2-digit",
                                                                    second: "2-digit",
                                                                }
                                                            )}
                                                        </p>
                                                        <p className="text-sm text-gray-700">
                                                            <strong>
                                                                Jenis Kendaraan:
                                                            </strong>{" "}
                                                            {transaction.jenis_kendaraan?.namakendaraan || "-"}
                                                        </p>
                                                        <p className="text-sm text-gray-700">
                                                            <strong>
                                                                Harga Satuan:
                                                            </strong>{" "}
                                                            Rp {transaction.harga_satuan ? Number(transaction.harga_satuan).toLocaleString("id-ID") : "-"}
                                                        </p>
                                                        <p className="text-sm text-gray-700">
                                                            <strong>
                                                                Total Harga:
                                                            </strong>{" "}
                                                            Rp{" "}
                                                            {transaction.total ? Number(transaction.total).toLocaleString("id-ID") : 0}
                                                        </p>
                                                        <button
                                                            onClick={() =>
                                                                deleteTransaction(
                                                                    "Parking",
                                                                    transaction.id
                                                                )
                                                            }
                                                            className="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium   text-sm px-4 py-2 mt-2"
                                                        >
                                                            Hapus
                                                        </button>
                                                    </div>
                                                )
                                            )
                                        ) : (
                                            <p className="text-sm text-gray-500 text-center">
                                                Tidak ada data transaksi parkir.
                                            </p>
                                        )}
                                    </div>
                                    {/* Column for Ticket Transactions */}
                                    <div>
                                        <h4 className="text-md font-semibold text-center md:text-left">
                                            Transaksi Tiket
                                        </h4>
                                        <hr />
                                        <br />
                                        {filteredTransactions()
                                            .ticketTransactions.length > 0 ? (
                                            filteredTransactions().ticketTransactions.map(
                                                (transaction, index) => (
                                                    <div
                                                        key={index}
                                                        className="border-b pb-2"
                                                    >
                                                        <p className="text-sm text-gray-700">
                                                            <strong>
                                                                Waktu Input:
                                                            </strong>{" "}
                                                            {new Date(
                                                                transaction.created_at
                                                            ).toLocaleString(
                                                                "id-ID",
                                                                {
                                                                    year: "numeric",
                                                                    month: "long",
                                                                    day: "numeric",
                                                                    hour: "2-digit",
                                                                    minute: "2-digit",
                                                                    second: "2-digit",
                                                                }
                                                            )}
                                                        </p>
                                                        <p className="text-sm text-gray-700">
                                                            <strong>
                                                                Jumlah Tiket:
                                                            </strong>{" "}
                                                            {transaction.jumlah_orang || "-"}
                                                        </p>
                                                        <p className="text-sm text-gray-700">
                                                            <strong>
                                                                Harga Satuan:
                                                            </strong>{" "}
                                                            {transaction.harga_satuan ||
                                                                "-"}
                                                        </p>
                                                        <p className="text-sm text-gray-700">
                                                            <strong>
                                                                Total Harga:
                                                            </strong>{" "}
                                                            Rp{" "}
                                                            {transaction.total}
                                                        </p>
                                                        <button
                                                            onClick={() =>
                                                                deleteTransaction(
                                                                    "Ticket",
                                                                    transaction.id
                                                                )
                                                            }
                                                            className="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium   text-sm px-4 py-2 mt-2"
                                                        >
                                                            Hapus
                                                        </button>
                                                    </div>
                                                )
                                            )
                                        ) : (
                                            <p className="text-sm text-gray-500 text-center">
                                                Tidak ada data transaksi tiket.
                                            </p>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {showShiftSummary && (
                        <div
                            ref={shiftSummaryRef}
                            className="mt-8 bg-gray-50 border border-gray-200   -xl p-6 shadow-lg"
                        >
                            <h4 className="text-lg font-bold mb-4 text-center text-green-700">
                                Rincian Transaksi Shift Hari Ini
                            </h4>
                            <div className="mb-4">
                                <strong>Transaksi Parkir:</strong>
                                <ul className="list-disc ml-6">
                                    {todaySummary.parking.length > 0 ? (
                                        todaySummary.parking.map((t, i) => (
                                            <li key={i}>
                                                {t.jenis_kendaraan.namakendaraan || "-"} | Rp {t.harga_satuan ? Number(t.harga_satuan).toLocaleString("id-ID") : "-"} | Total: Rp {t.total ? Number(t.total).toLocaleString("id-ID") : 0} |{" "}
                                                {new Date(
                                                    t.created_at
                                                ).toLocaleTimeString("id-ID")}
                                            </li>
                                        ))
                                    ) : (
                                        <li>
                                            Tidak ada transaksi parkir hari ini.
                                        </li>
                                    )}
                                </ul>
                                <div className="mt-2 text-right font-semibold text-blue-700">
                                    Total Parkir: Rp {todaySummary.totalParking}
                                </div>
                            </div>
                            <div className="mb-4">
                                <strong>Transaksi Tiket:</strong>
                                <ul className="list-disc ml-6">
                                    {todaySummary.ticket.length > 0 ? (
                                        todaySummary.ticket.map((t, i) => (
                                            <li key={i}>
                                                {t.jumlah_orang || "-"} tiket |
                                                Rp {t.total} |{" "}
                                                {new Date(
                                                    t.created_at
                                                ).toLocaleTimeString("id-ID")}
                                            </li>
                                        ))
                                    ) : (
                                        <li>
                                            Tidak ada transaksi tiket hari ini.
                                        </li>
                                    )}
                                </ul>
                                <div className="mt-2 text-right font-semibold text-green-700">
                                    Total Tiket: Rp {todaySummary.totalTicket}
                                </div>
                            </div>
                            {/* Total Semua dihapus, hanya tampil total parkir dan tiket */}
                            <div className="flex justify-center">
                                <button
                                    onClick={handleSaveShiftAndLogout}
                                    disabled={closingShift}
                                    className="px-6 py-3 bg-blue-700 text-white   font-bold hover:bg-blue-800 focus:ring-4 focus:ring-blue-300"
                                >
                                    {closingShift
                                        ? "Menyimpan & Logout..."
                                        : "Simpan Shift & Logout"}
                                </button>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
