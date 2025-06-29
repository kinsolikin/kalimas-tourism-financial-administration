<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Ticket_income_details;
use App\Models\Parking_income_details;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\JenisKendaraan;
use App\Models\SetingTicket;
use App\Models\ListShift;
use Illuminate\Support\Facades\Auth;
use App\Models\Shift;


class ControllerTicketParking extends Controller
{
    /**
     * Display a listing of the resource.
     */





    public function shift()
    {

        $shifts = ListShift::with('employe')->get();

        return Inertia::render('Auth/Shift', [
            'shifts' => $shifts
        ]);
    }


    public function index(Request $request)


    {

      
        $user = Auth::user();

        if ($user->id == 1) {
            // Buatkan entri shift untuk user id 1 dan 2
            $targetUserIds = [1, 2];
        } else {
            // Hanya untuk user yang sedang login
            $targetUserIds = [$user->id];
        }

        foreach ($targetUserIds as $userId) {
            Shift::firstOrCreate(
                [
                    'user_id' => $userId,
                    'end_time' => null,
                    'created_at' => now()->startOfDay(),
                ],
                [
                    'list_shift_id' => $request->shift,
                    'employe_id' => $request->employe,
                    'start_time' => now(),
                    'total_pendapatan' => 0,
                    'total_pengeluaran' => 0,
                ]
            );
        }




        $jeniskendaraan = JenisKendaraan::all();
        $priceticket = SetingTicket::pluck('price')->first();
        return Inertia::render('Dashboard', [
            'jenisKendaraan' => $jeniskendaraan,
            'priceticket' => $priceticket,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Cek income hari ini


        $validatedData = $request->validate([

            "shift" => "required|string",
            "operator_name" => "required|string",
            "vehicle_type" => "required|integer|exists:jenis_kendaraans,id",
            "price" => "required|numeric",
            "jumlah_tiket" => "required|integer|min:0",
            "harga_tiket" => "required|numeric",
            "jam_masuk" => "required|string",
            "jam_keluar" => "required|string"


        ]);


        $user = auth()->user();


        $incometicket = Income::where('income_categori_id', $user->id)
            ->where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->first();

        if (!$incometicket) {
            $incometicket = Income::create([
                'income_categori_id' => $user->id,
                'user_id' => 1,
                'amount' => 0,
            ]);
        }

        $incomeparking = Income::where('income_categori_id', 2)
            ->where('user_id', 2)
            ->whereDate('created_at', Carbon::today())
            ->first();

        if (!$incomeparking) {
            $incomeparking = Income::create([
                'income_categori_id' => 2,
                'user_id' => 2,
                'amount' => 0,
            ]);
        }



        // Simpan data tiket ke tabel Ticket_income_details jika field terkait tiket diisi

        $detailticket = Ticket_income_details::create([
            'user_id' => 1,
            'income_id' => $incometicket->id,
            'jumlah_orang' => $validatedData['jumlah_tiket'],
            'harga_satuan' => 5000,
            'total' => $validatedData['jumlah_tiket'] * 5000,
        ]);


        // Simpan data parkir ke tabel Parking_income_details jika field terkait parkir diisi

        $detailparking = Parking_income_details::create([
            'user_id' => 1,
            'income_id' => $incomeparking->id,
            'jenis_kendaraan_id' => $validatedData['vehicle_type'],
            'jumlah_kendaraan' => 1,
            'harga_satuan' => $validatedData['price'],
            'total' => 1 * $validatedData['price'],
        ]);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function fetchJenisKendaraan()
    {


        $jeniskendaraan = JenisKendaraan::all();
        return response()->json($jeniskendaraan);
    }
}
