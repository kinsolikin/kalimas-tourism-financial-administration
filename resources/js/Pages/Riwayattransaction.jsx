import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm , usePage} from '@inertiajs/react';
import { useEffect } from 'react';
import Swal from 'sweetalert2';



export default function Dashboard({ auth, transactions }) {


    const { props } = usePage();
    const {status , message} = props;

    const { data, setData, post, processing, errors } = useForm({
        shift: '1',
        operator_name: '',
        vehicle_type: '',
        price: 0,
        jumlah_tiket: 0,
        harga_tiket: 0,
        jam_masuk: new Date().toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' }), // Default to current time
        jam_keluar: '16:00',
    });

    const prices = {
        motor: 2000,
        mobil: 5000,
        eleve: 10000,
        bis_medium: 20000,
        bus_besar: 30000,
    };

    useEffect(() => {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        setData('jam_masuk', `${hours}:${minutes}`);
    }, []);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setData(name, value);

        if (name === 'vehicle_type') {
            setData('price', prices[value] || 0);
        }

        if (name === 'jumlah_tiket') {
            setData('harga_tiket', value * 5000);
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/dashboard/store', {
            onSuccess: () => {
                setData({
                    ...data, // Pertahankan nilai shift yang lama
                    operator_name: '',
                    vehicle_type: '',
                    price: 0,
                    jumlah_tiket: 0,
                    harga_tiket: 0,
                    jam_masuk: '',
                    jam_keluar: '16:00',
                });
            },
        });
    };

    useEffect(() => {
    console.log("Status:", status); // Debugging log
    console.log("Message:", message); // Debugging log

    if (status && message) {
      Swal.fire({
        icon: status === "success" ? "success" : "error",
        title: status === "success" ? "Berhasil" : "Gagal",
        text: message,
      });
    }
  }, [status, message]);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Riwayat Transaksi</h2>}
        >
            <Head title="Riwayat Transaksi" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <table className="table-auto w-full border-collapse border border-gray-300">
                                <thead>
                                    <tr>
                                        <th className="border border-gray-300 px-4 py-2">No</th>
                                        <th className="border border-gray-300 px-4 py-2">Operator</th>
                                        <th className="border border-gray-300 px-4 py-2">Jenis Kendaraan</th>
                                        <th className="border border-gray-300 px-4 py-2">Harga</th>
                                        <th className="border border-gray-300 px-4 py-2">Jumlah Tiket</th>
                                        <th className="border border-gray-300 px-4 py-2">Jam Masuk</th>
                                        <th className="border border-gray-300 px-4 py-2">Jam Keluar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {transactions.map((transaction, index) => (
                                        <tr key={transaction.id}>
                                            <td className="border border-gray-300 px-4 py-2">{index + 1}</td>
                                            <td className="border border-gray-300 px-4 py-2">{transaction.operator_name}</td>
                                            <td className="border border-gray-300 px-4 py-2">{transaction.vehicle_type}</td>
                                            <td className="border border-gray-300 px-4 py-2">{transaction.price}</td>
                                            <td className="border border-gray-300 px-4 py-2">{transaction.jumlah_tiket}</td>
                                            <td className="border border-gray-300 px-4 py-2">{transaction.jam_masuk}</td>
                                            <td className="border border-gray-300 px-4 py-2">{transaction.jam_keluar}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
