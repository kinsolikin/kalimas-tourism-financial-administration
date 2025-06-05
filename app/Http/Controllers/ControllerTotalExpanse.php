<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Expanse;
use App\Models\TotalExpanse;
use Illuminate\Http\Request;

class ControllerTotalExpanse extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalexpansetoday = Expanse::whereIn('expanse_category_id', [1, 2])
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        // Menghitung total pengeluaran hari ini berdasarkan kategori
        TotalExpanse::updateOrCreate(
            [
                'created_at' => Carbon::today()
            ], // Kondisi untuk update berdasarkan tanggal
            [
                'total_amount' => $totalexpansetoday,
                'user_id' => 1,
            ] // Data yang akan disimpan
        );

        return response()->json([
            'message' => 'Total pengeluaran pada hari ini berhasil disimpan.',
            'total_expense_today' => $totalexpansetoday,
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
        //
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
