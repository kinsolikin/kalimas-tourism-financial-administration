<?php

use App\Http\Controllers\ControllerExpanse;
use App\Http\Controllers\ControllerIncomeBersih;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControllerTotalIncome;
use App\Http\Controllers\ControllerTicketParking;
use App\Http\Controllers\ControllerTotalExpanse;
use App\Http\Controllers\DashboardController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});




Route::get('dashboard-data',[DashboardController::class, 'fetchdata']);
Route::get('dashboard/ambilReview',[DashboardController::class, 'ambilReview']);


    Route::post('ticket', [ControllerTicketParking::class, 'store']);


    Route::get('totalincome', [ControllerTotalIncome::class, 'index']);
    Route::get('totalexpanse', [ControllerTotalExpanse::class, 'index']);
    Route::get('netincome', [ControllerIncomeBersih::class, 'index']);

