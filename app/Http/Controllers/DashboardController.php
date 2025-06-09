<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use    App\Models\Review;
use App\Models\NetIncome;
use App\Models\TotalIncome;
use App\Models\TotalExpanse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Ticket_income_details;


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




          $expanse = TotalExpanse::select(
            DB::raw('DATE(created_at) as tanggal'),
            DB::raw('SUM(total_amount) as total')
        )
            ->whereYear('created_at', $today->year)
            ->whereMonth('created_at', $today->month)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('tanggal')
            ->get();



        $netincomebulanan = NetIncome::whereYear('created_at', $today->year)
            ->whereMonth('created_at', $today->month)
            ->sum('net_income');

        $pengunjungHarian = Ticket_income_details::select(
            DB::raw('DATE(created_at) as tanggal'),
            DB::raw('SUM(jumlah_orang) as total')
        )
            ->whereYear('created_at', $today->year)
            ->whereMonth('created_at', $today->month)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('tanggal')
            ->get();


        $netincomebulanan = NetIncome::select(
            DB::raw('DATE(created_at) as tanggal'),
            DB::raw('SUM(net_income) as total')
        )
            ->whereYear('created_at', $today->year)
            ->whereMonth('created_at', $today->month)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('tanggal')
            ->get();

           

        $rekapBulanan = [
            'ticket_total' => TotalIncome::whereYear('created_at', $today->year)
                ->whereMonth('created_at', $today->month)
                ->sum('total_ticket_details'),

            'parking_total' => TotalIncome::whereYear('created_at', $today->year)
                ->whereMonth('created_at', $today->month)
                ->sum('total_parking_details'),

            'bantuan_total' => TotalIncome::whereYear('created_at', $today->year)
                ->whereMonth('created_at', $today->month)
                ->sum('total_bantuan_details'),

            'resto_total' => TotalIncome::whereYear('created_at', $today->year)
                ->whereMonth('created_at', $today->month)
                ->sum('total_resto_details'),

            'toilet_total' => TotalIncome::whereYear('created_at', $today->year)
                ->whereMonth('created_at', $today->month)
                ->sum('total_toilet_details'),

            'wahana_total' => TotalIncome::whereYear('created_at', $today->year)
                ->whereMonth('created_at', $today->month)
                ->sum('total_wahana_details'),

            'total_income' => NetIncome::whereYear('created_at', $today->year)
                ->whereMonth('created_at', $today->month)
                ->sum('net_income'),
        ];


        return response()->json([
            'Expanse' => $expanse,
            'Bulanan' => $netincomebulanan,
            'PengunjungBulanan' => $pengunjungHarian,
            'Incomes' => $rekapBulanan,
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
