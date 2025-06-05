<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

use App\Models\Income;
use App\Models\Bantuan_income_details;

class DashboardBantuan extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('BantuanIncomeForm');
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
        // Validate the request data
        $validatedata = $request->validate([
            'sumber_bantuan' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255',
            'total' => 'required|numeric',
        ]);

        $user = auth()->user();

        $totalincome = Income::where('income_categori_id',$user->id)
        ->where('user_id',$user->id)
        ->whereDate('created_at',Carbon::today())
        ->first();

        if(!$totalincome)
        {
            $totalincome = Income::create([
                
                'income_categori_id' => $user->id,
                'user_id'=> $user->id,
                'amount' => 0
            ]);
        }

        if(isset($validatedata['total'])){
            Bantuan_income_details::create([
                'user_id' => $user->id,
                'income_id' => $totalincome->id,
                'sumber_bantuan' => $validatedata['sumber_bantuan'],
                'keterangan' => $validatedata['keterangan'],
                'total' => $validatedata['total']
            ]);
        }


        

       


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
