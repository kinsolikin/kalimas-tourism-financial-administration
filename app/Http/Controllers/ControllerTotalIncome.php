<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Income;
use App\Models\TotalIncome;
use Illuminate\Http\Request;

class ControllerTotalIncome extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalIncomeToday = Income::whereIn('income_categori_id', [1, 2])
        ->whereDate('created_at', Carbon::today())
        ->sum('amount');

     // Menghitung total pemasukan hari ini berdasarkan kategori
     TotalIncome::updateOrCreate(
        [
            'created_at' => Carbon::today()
        ], // Kondisi untuk update berdasarkan tanggal
        [
            'user_id' => 1,
            'total_amount' => $totalIncomeToday,
        ] // Data yang akan disimpan
    );

    return response()->json([
        'message' => 'Total pemasukan pada hari ini berhasil disimpan.',
        'total_expense_today' => $totalIncomeToday,
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
