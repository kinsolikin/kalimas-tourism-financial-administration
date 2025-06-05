<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Income;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Resto_income_details;

class DashboardLoketResto extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
       return Inertia::render('Resto');
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
            'nama' => 'required|string',
            'makanan' => 'required|string',
            'minuman' => 'required|string',
            'qty_makanan' => 'required|integer|min:0',
            'qty_minuman' => 'required|integer|min:0',
            'harga_satuan_makanan' => 'required|numeric',
            'harga_satuan_minuman' => 'required|numeric',
        ]);


        $user = auth()->user();

        $restoincome = Income::where('income_categori_id',$user->id)
        ->where('user_id',$user->id)
        ->whereDate('created_at',Carbon::today())
        ->first();

        if(!$restoincome){
            $restoincome=Income::create([
                'income_categori_id'=> $user->id,
                'user_id'=>$user->id,
                'amount'=>0,
            ]);

        }

        if($validatedata['qty_makanan']>0 && $validatedata['qty_minuman']>0){
            Resto_income_details::create([
                'user_id'=>$user->id,
                'income_id'=>$restoincome->id,
                'name_customer'=>$validatedata['nama'],
                'makanan'=>$validatedata['makanan'],
                'minuman'=>$validatedata['minuman'],
                'qty_makanan'=>$validatedata['qty_makanan'],
                'qty_minuman'=>$validatedata['qty_minuman'],
                'harga_satuan_makanan'=>$validatedata['harga_satuan_makanan'],
                'harga_satuan_minuman'=>$validatedata['harga_satuan_minuman'],
                'total'=> ($validatedata['qty_makanan']*$validatedata['harga_satuan_makanan'])+($validatedata['qty_minuman']*$validatedata['harga_satuan_minuman'])
                
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
