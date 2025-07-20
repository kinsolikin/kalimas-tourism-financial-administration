import { useEffect } from "react";
import Checkbox from "@/Components/Checkbox";
import GuestLayout from "@/Layouts/GuestLayout";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import { Head, Link, useForm } from "@inertiajs/react";

export default function Login({ status, canResetPassword, shifts }) {


    console.log("shifts", shifts);
    
    const { data, setData, post, processing, errors, reset } = useForm({
        shift:"",
        employe:"",
        email: "",
        password: "",
        remember: false,
    });

    useEffect(() => {
        return () => {
            reset("password");
        };
    }, []);

    const submit = (e) => {
        e.preventDefault();
        post(route("login"));
    };

    return (
        <GuestLayout>
            <Head title="Login" />

            <p className=" text-gray-500 font-bold text-left mb-6">
                Login Dengan Akun Karyawan kamu
            </p>

            {status && (
                <div className="mb-4 text-sm font-medium text-green-600 text-center">
                    {status}
                </div>
            )}

            <form onSubmit={submit}>
                {/* <div className="mb-4">
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
                    <InputError message={errors.shift} className="mt-2" />
                </div>
                 {selectedShift && selectedShift.employe.length > 0 && (
                <div className="mb-4">
                    <label htmlFor="employe" className="block font-medium text-sm text-gray-700">
                        Nama Employe
                    </label>
                    <select
                        id="employe"
                        name="employe"
                        value={data.employe}
                        onChange={(e) => setData({ ...data, employe: e.target.value })}
                        className="text-gray-500 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200"
                    >
                        <option value="">Pilih Nama</option>
                        {selectedShift.employe.map((employe) => (
                            <option key={employe.id} value={employe.id}>
                                {employe.name}
                            </option>
                        ))}
                    </select>
                </div>
            )} */}
                <div className="mb-4">
                    <InputLabel htmlFor="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className="mt-1 block w-full"
                        autoComplete="username"
                        isFocused={true}
                        onChange={(e) => setData("email", e.target.value)}
                    />
                    <InputError message={errors.email} className="mt-2" />
                </div>

                <div className="mb-4">
                    <InputLabel htmlFor="password" value="Password" />
                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        value={data.password}
                        className="mt-1 block w-full"
                        autoComplete="current-password"
                        onChange={(e) => setData("password", e.target.value)}
                    />
                    <InputError message={errors.password} className="mt-2" />
                </div>

                <div className="flex items-center justify-between mb-6">
                    <label className="flex items-center text-sm text-gray-600">
                        <Checkbox
                            name="remember"
                            checked={data.remember}
                            onChange={(e) =>
                                setData("remember", e.target.checked)
                            }
                        />
                        <span className="ml-2">Remember me</span>
                    </label>

                    {canResetPassword && (
                        <Link
                            href={route("password.request")}
                            className="text-sm text-indigo-600 hover:underline"
                        >
                            Forgot password?
                        </Link>
                    )}
                </div>

                <PrimaryButton
                    className="w-full justify-center"
                    disabled={processing}
                >
                    Log in
                </PrimaryButton>
            </form>

            <div className="mt-6 text-center text-sm text-gray-600">
                Don't have an account?{" "}
                <Link
                    href={route("register")}
                    className="text-indigo-600 hover:underline font-medium"
                >
                    Register
                </Link>
            </div>
        </GuestLayout>
    );
}
