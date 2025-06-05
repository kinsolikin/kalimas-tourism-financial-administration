<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Expanse;
use App\Models\Expanse_Mendadak;
use App\Models\Expanse_Operasional;
use App\Models\TotalExpanse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ControllerExpanse extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Expanse');
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

        $user = auth()->user();

        if ($request->input('expanse_id') == 1) {
            $expanse = Expanse::where('expanse_category_id', 1)
                ->where('user_id', $request->input('user_id'))
                ->whereDate('created_at', Carbon::today())
                ->first();
        } else {
            $expanse = Expanse::where('expanse_category_id', 2)
                ->where('user_id', $request->input('user_id'))
                ->whereDate('created_at', Carbon::today())
                ->first();
        }



        if (!$expanse) {
            $expanse = Expanse::create([
                'expanse_category_id' => $request->input('expanse_id'),
                'user_id' => $user->id,
                'amount' => 0,
            ]);
        }


        if (($request->input('expanse_id')==1) && $request->filled('amount') && $request->filled('description')) {
            $expansemount = Expanse_Operasional::create([
                'expanse_id' => $expanse->id,
                'user_id' => $user->id,
                'amount' => $request->amount,
                'description' => $request->description,
            ]);
        }else
        {
            $expansemount = Expanse_Mendadak::create([
                'expanse_id' => $expanse->id,
                'user_id' => $user->id,
                'amount' => $request->amount,
                'description' => $request->description,
            ]);
        }

        $expanse->increment('amount', $expansemount->amount);

        
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
