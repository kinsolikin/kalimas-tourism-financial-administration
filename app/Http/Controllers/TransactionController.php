<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Parking_income_details;
use App\Models\Resto_income_details;
use App\Models\Ticket_income_details;
use App\Models\Wahana_income_details;
use App\Models\Toilet_income_details;
use App\Models\Bantuan_income_details;
use App\Models\Expanse;


use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $parkingTransactions = Parking_income_details::with(['user','jenisKendaraan'])->orderBy('created_at', 'desc')->get();
        $ticketTransactions = Ticket_income_details::with('user')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'parkingTransactions' => $parkingTransactions,
            'ticketTransactions' => $ticketTransactions,
        ]);
    }

      // menghapus transaksi parking dan ticket berdasarkan id
    public function deleteParking($id)
    {
        try {
            Parking_income_details::findOrFail($id)->delete();
            return response()->json(['message' => 'Parking transaction deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete parking transaction.'], 500);
        }
    }

    public function deleteTicket($id)
    {
        try {
            Ticket_income_details::findOrFail($id)->delete();
            return response()->json(['message' => 'Ticket transaction deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete ticket transaction.'], 500);
        }
    }

    public function deleteAllTransactions()
    {
        // Ambil semua transaksi parking dan hapus satu per satu
        $parkingTransactions = Parking_income_details::all();
        foreach ($parkingTransactions as $transaction) {
            $transaction->delete(); // Event `deleted` akan dipanggil
        }

        // Ambil semua transaksi ticket dan hapus satu per satu
        $ticketTransactions = Ticket_income_details::all();
        foreach ($ticketTransactions as $transaction) {
            $transaction->delete(); // Event `deleted` akan dipanggil
        }

        return response()->json(['message' => 'All transactions deleted successfully.']);
    }

    //menampilkan riwayat transaksi resto
    public function indexResto()
    {

        return response()->json(Resto_income_details::latest()->get());
    }

    // menghapus semua transaksi resto
    public function deleteAllRestoTransactions()
    {
        // Ambil semua transaksi resto dan hapus satu per satu
        $restoTransactions = Resto_income_details::all();
        foreach ($restoTransactions as $transaction) {
            $transaction->delete(); // Event `deleted` akan dipanggil
        }

        return response()->json(['message' => 'All transactions deleted successfully.']);
    }

    // menghapus transaksi resto berdasarkan id
    public function deletefindResto($id)
    {
        $transaction = Resto_income_details::findOrFail($id);
        $transaction->delete();

        return response()->json(['message' => 'Transaksi berhasil dihapus']);
    }

    public function restotransactionfilter(Request $request)
    {


        $query = Resto_income_details::query();

        if ($request->filled('from') && $request->filled('to')) {

            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();

            $query->whereBetween('created_at', [$from, $to]);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }


    // menampilkan riwayat transaksi wahana
    public function indexWahana()
    {

        
        // Ambil semua transaksi wahana
        return response()->json(Wahana_income_details::with('jenisWahana')->get());
    }



    public function deletefindWahana($id)
    {


        $transaction = Wahana_income_details::findOrFail($id);
        $transaction->delete();

        return response()->json(['message' => 'Transaksi berhasil dihapus']);
    }


    public function deleteAllWahanaTransactions()
    {
        // Ambil semua transaksi wahana dan hapus satu per satu
        $wahanaTransactions = Wahana_income_details::all();
        foreach ($wahanaTransactions as $transaction) {
            $transaction->delete(); // Event `deleted` akan dipanggil
        }

        return response()->json(['message' => 'All transactions deleted successfully.']);
    }

    public function wahanatransactionfilter(Request $request) {

        $query =Wahana_income_details::query();


        if($request->filled('from')&&$request->filled('to')){

            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();
        
            $query->whereBetween('created_at', [$from, $to]);


            
            return response()->json($query->orderBy('created_at', 'desc')->get());


        }
    }


  

    public function indexToilet()
    {
        // Ambil semua transaksi toilet
        return response()->json(Toilet_income_details::latest()->get());
    }

    public function deletefindToilet($id)
    {
        Toilet_income_details::findOrfail($id)->delete();
        return response()->json(['message'=>'Transaksi berhasil dihapus']); 
    }

    public function deleteAllToiletTransactions()
    {
        // Ambil semua transaksi toilet dan hapus satu per satu
        $toiletTransactions = Toilet_income_details::all();
        
        foreach($toiletTransactions as $transacion ){
            $transacion->delete(); // Event `deleted` akan dipanggil
        }
    }


    public function toilettransactionfilter(Request $request){

        $query = Toilet_income_details::query();

        
        if($request->filled('from') && $request->filled('to')){

            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();

            $query->whereBetween('created_at', [$from, $to]);

            return response()->json($query->orderBy('created_at', 'desc')->get());                              
        }
    }

    public function indexbantuan(){
        
        return response()->json(Bantuan_income_details::latest()->get());
    }

    public function deletefindbantuan($id)
    {
        Bantuan_income_details::findOrfail($id)->delete();
        return response()->json(['message'=>'Transaksi berhasil dihapus']); 
        
    }

      public function deleteAllBantuanTransactions()
    {
        // Ambil semua transaksi toilet dan hapus satu per satu
        $BantuanTransactions = Bantuan_income_details::all();
        
        foreach($BantuanTransactions as $transacion ){
            $transacion->delete(); // Event `deleted` akan dipanggil
        }
    }

       public function bantuantransactionfilter(Request $request){

        $query = Toilet_income_details::query();

        
        if($request->filled('from') && $request->filled('to')){

            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();

            $query->whereBetween('created_at', [$from, $to]);

            return response()->json($query->orderBy('created_at', 'desc')->get());                              
        }
    }



    public function indexExpanse()
    {
        // Ambil semua transaksi expanse

        $user = auth()->user();


      $expanses = $user->expanse()->with(['expanse_category','expanse_operasional','expanse_mendadak'])->get();

      return response()->json([
          'expanses' => $expanses,
      ]);
    }

     public function deletefindexpanse($id)
    {
      
        $expanse = Expanse::findOrFail($id);
        $expanse->delete();

        return response()->json(['message' => 'Transaksi expanse berhasil dihapus']);

        
        
    }


    // untuk guest dashboard
    public function guestexpanses()
    {
        // ambil semua pengeluaran

        $expanses = Expanse::with(['expanse_category','expanse_operasional','expanse_mendadak','user'])->get();

        return response()->json([
            'expanses'=>$expanses
        ]);
    }
    }


