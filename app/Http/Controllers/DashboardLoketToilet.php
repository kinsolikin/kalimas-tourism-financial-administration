<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\Income;
use App\Models\Toilet_income_details;
class DashboardLoketToilet extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Toilet');
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

        $validatedata = $request->validate([
            'jumlah' => 'required|integer',
            'harga_perorang' => 'required|numeric',
            'total' => 'required|numeric'
        ]);

        $user = auth()->user();

        $toiletincome = Income::where('income_categori_id',$user->id)
        ->where('user_id',$user->id)
        ->whereDate('created_at',Carbon::today())
        ->first();

        if(!$toiletincome){
            $toiletincome = Income::create([
                'income_categori_id'=> $user->id,
                'user_id' => $user->id,
                'amount' => 0,
            ]);
        }

        if($validatedata['jumlah']>0){
            Toilet_income_details::create([
                'user_id'=> $user->id,
                'income_id' => $toiletincome->id,
                'jumlah_pengguna' => $validatedata['jumlah'],
                'harga_per_orang' => $validatedata['harga_perorang'],
                'total' => $validatedata['total'],
            ]);
        };


        
        



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
