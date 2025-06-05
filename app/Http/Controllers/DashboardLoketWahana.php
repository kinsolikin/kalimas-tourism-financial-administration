<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Income;
use App\Models\Wahana_income_details;
class DashboardLoketWahana extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Wahana');
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
            'nama_wahana'=> 'required|string',
            'harga'=> 'required|integer|min:0',
            'jumlah'=> 'required|integer|min:0',
            'total'=> 'required|numeric',
        ]);

        $user = auth()->user();

         $wahanaincome = Income::where('income_categori_id',$user->id)
        ->where('user_id',$user->id)
        ->whereDate('created_at',Carbon::today())
        ->first();

        if(!$wahanaincome){
            $wahanaincome=Income::create([
                'income_categori_id'=> $user->id,
                'user_id'=>$user->id,
                'amount'=>0,
            ]);
        }
            
        if($validatedata['jumlah'] > 0){
            Wahana_income_details::create([
                'user_id'=>$user->id,
                'income_id'=>$wahanaincome->id,
                'nama_wahana'=>$validatedata['nama_wahana'],
                'harga'=>$validatedata['harga'],
                'jumlah'=>$validatedata['jumlah'],
                'total'=>$validatedata['total'],
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
