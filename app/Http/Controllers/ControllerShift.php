<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\ListShift;
use App\Models\Shift;

class ControllerShift extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public  function closeShift(Request $request) {}

    public function index()
    {


        $userId = Auth::id(); // ID dari user yang sedang login

        $shifts = ListShift::with(['employe' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])->get();


        return Inertia::render('Auth/Shift', [
            'shifts' => $shifts,
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


        $request->validate([
            'shift' => 'required|exists:list_shifts,id',
            'employe' => 'required|exists:employes,id',
        ], [
            'shift.required' => 'Silakan pilih shift terlebih dahulu.',
            'shift.exists' => 'Shift yang dipilih tidak valid.',
            'employe.required' => 'Silakan pilih pegawai terlebih dahulu.',
            'employe.exists' => 'Pegawai yang dipilih tidak valid.',
        ]);

        session([
            'shift' => $request->shift,
            'employe' => $request->employe,
        ]);


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
                    'employe_id' => 1,
                    'start_time' => now(),
                    'total_pendapatan' => 0,
                    'total_pengeluaran' => 0,
                ]
            );
        }


        return redirect()->intended(match ($user->role) {
            'lokettiketparkirparkir' => route('dashboard.tiketparkir.masuk'),
            'loketresto' => route('dashboard.resto'),
            'loketwahana' => route('dashboard.wahana'),
            'lokettoilet' => route('dashboard.toilet'),
            'bantuan' => route('dashboard.bantuan'),
            // default => RouteServiceProvider::HOME,
        });
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
}
