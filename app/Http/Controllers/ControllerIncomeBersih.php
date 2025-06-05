<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\NetIncome;
use App\Models\TotalIncome;
use App\Models\TotalExpanse;
use Illuminate\Http\Request;

class ControllerIncomeBersih extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalIncome = TotalIncome::where('created_at', Carbon::today())->sum('total_amount');
        $totalExpense = TotalExpanse::where('created_at', Carbon::today())->sum('total_amount');
    
        NetIncome::updateOrCreate(
            ['created_at' => Carbon::today()],
            [
                'user_id' => 1,
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
            ]
        );

        return response()->json([
            'message' => 'Total pemasukan dan pengeluaran pada hari ini berhasil disimpan.',
            'total_income_today' => $totalIncome,
            'total_expense_today' => $totalExpense,
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
