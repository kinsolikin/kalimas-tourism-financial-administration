import React from "react";
import { useEffect } from "react";
import Checkbox from "@/Components/Checkbox";
import GuestLayout from "@/Layouts/GuestLayout";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import { Head, Link, useForm, usePage } from "@inertiajs/react";

function Shift({ shifts }) {
    const { flash } = usePage().props;

    const { data, setData, post, processing, errors, reset } = useForm({
        shift: "",
        employe: "",
        remember: false,
    });

    const selectedShift = shifts.find((s) => s.id === parseInt(data.shift));

    const submit = (e) => {
        e.preventDefault();
        post(route("dashboard.shift.store"));
    };

    console.log("shifts", shifts);

    return (
        <GuestLayout>
            <Head title="Login" />
            {flash.error && (
                <div className="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
                    {flash.error}
                </div>
            )}
            <p className=" text-gray-500 font-bold text-left mb-6">
                Pilih Shift Kamu
            </p>
            <form onSubmit={submit}>
                <div className="mb-4">
                    <InputLabel htmlFor="shift" value="Shift" />
                    <select
                        id="shift"
                        name="shift"
                        value={data.shift}
                        onChange={(e) => setData("shift", e.target.value)}
                        className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 text-gray-500 "
                    >
                        <option value="">pilih shift</option>
                        {shifts.map((shift) => (
                            <option key={shift.id} value={shift.id}>
                                {shift.shift_name}
                            </option>
                        ))}
                    </select>
                    <InputError message={errors.shift} className="text-red-500 text-sm mt-2" />
                </div>
                {selectedShift && selectedShift.employe.length > 0 && (
                    <div className="mb-4">
                        <label
                            htmlFor="employe"
                            className="block font-medium text-sm text-gray-700"
                        >
                            Nama Employe
                        </label>
                        <select
                            id="employe"
                            name="employe"
                            value={data.employe}
                            onChange={(e) =>
                                setData({ ...data, employe: e.target.value })
                            }
                            className="text-gray-500 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200"
                        >
                            <option value="">Pilih Nama</option>
                            {selectedShift.employe.map((employe) => (
                                <option key={employe.id} value={employe.id}>
                                    {employe.name}
                                </option>
                            ))}
                        </select>
                    <InputError message={errors.employe} className="text-red-500 text-sm mt-2" />

                    </div>
                )}
                <PrimaryButton
                    className="w-full justify-center"
                    disabled={processing}
                >
                    Masuk
                </PrimaryButton>
            </form>
        </GuestLayout>
    );
}

export default Shift;
