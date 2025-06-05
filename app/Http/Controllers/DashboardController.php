<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use App\Models\NetIncome;
use Carbon\Carbon;
use App\Models\Ticket_income_details;
use Illuminate\Support\Facades\Http;
use    App\Models\Review;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function ambilReview()
    {

        $apiKey = config('services.serpapi.api_key');
        $dataId = '0x2e798ac99f2dcab9:0xc6aae83f59e2dc26';

        $response = Http::get('https://serpapi.com/search.json', [
            'engine' => 'google_maps_reviews',
            'data_id' => $dataId,
            'api_key' => $apiKey,
        ]);

        $reviews = $response->json()['reviews'] ?? [];

        // Translate each review snippet to Indonesian
        foreach ($reviews as &$review) {
            if (!empty($review['snippet'])) {
                $translateRes = Http::get('https://translate.googleapis.com/translate_a/single', [
                    'client' => 'gtx',
                    'sl' => 'auto',
                    'tl' => 'id',
                    'dt' => 't',
                    'q' => $review['snippet'],
                ]);
                $translated = $translateRes->json()[0][0][0] ?? $review['snippet'];
                $review['snippet_id'] = $translated;

              
            }
        }
       

        return response()->json([
            'reviews' => $reviews,
        ]);
    }
    public function index()
    {
        return Inertia::render('Dashboardguest');
    }

    public function fetchdata()
    {
        $today = Carbon::today();


        $netincomeharian = NetIncome::whereDate('created_at', now())->value('net_income') ?? 0;

        $netincomebulanan = NetIncome::whereYear('created_at', $today->year)
            ->whereMonth('created_at', $today->month)
            ->sum('net_income');

        $pengunjungbulanan = Ticket_income_details::whereYear('created_at', $today->year)
            ->whereMonth('created_at', $today->month)
            ->sum('jumlah_orang');

        return response()->json([
            'Harian' => $netincomeharian,
            'Bulanan' => $netincomebulanan,
            'PengunjungBulanan' => $pengunjungbulanan,
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'shift' => 'required|string',
            'vehicle_type' => 'required|string',
            'price' => 'required|numeric',
            'jumlah_tiket' => 'required|integer|min:0',
            'harga_tiket' => 'required|numeric',
            'jam_masuk' => 'required|string',
            'jam_keluar' => 'required|string',
        ]);

        // Log the data for debugging purposes
        Log::info('Form data received:', $validatedData);

        // Process the data (e.g., save to database or perform other actions)
        // Example: Model::create($validatedData);

        return redirect()->back()->with([
            'status' => 'success',
            'message' => 'Data saved successfully!',
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
}
