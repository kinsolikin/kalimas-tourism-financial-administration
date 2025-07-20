<?php

use App\Http\Controllers\ControllerTicketParking;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardLoketResto;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardLoketWahana;
use App\Http\Controllers\DashboardLoketToilet;
use App\Http\Controllers\DashboardBantuan;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\ControllerShift;
use App\Http\Controllers\ControllerExpanse;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/dashboard/expanse/transactions/guest', [TransactionController::class, 'guestexpanses']);



Route::middleware(['auth', 'verified'])->group(function () {



    Route::get('/dashboard/pengeluaran', [ControllerExpanse::class, 'index'])->name('dashboard.pengeluaran');

    Route::post('dashboard/expanses/store', [ControllerExpanse::class, 'store']);

    Route::get('/dashboard/expanse/transactions', [TransactionController::class, 'indexExpanse']);


    Route::delete('/dashboard/expanse/transactions/delete/{id}', [TransactionController::class, 'deletefindexpanse']);


    Route::post('/dashboard/close-shift', [ControllerShift::class, 'closeShift']);


    // shift
    Route::get('/dashboard-shift', [ControllerShift::class, 'index'])->name('dashboard.shift');

    Route::post('/dashboard-shift-store', [ControllerShift::class, 'store'])->name('dashboard.tiketparkir.parkir');


    // route untuk role lokettiketparkir
    Route::middleware(['role:lokettiketparkirparkir'])->group(function () {





        Route::get('/dashboard-tiketparkir', [ControllerTicketParking::class, 'index'])->name('dashboard.tiketparkir.masuk');

        Route::get('/dashboard/transaction-history', [TransactionController::class, 'index']);




        Route::post('/dashboard/store', [ControllerTicketParking::class, 'store']);

        Route::delete('/dashboard/parking/{id}', [TransactionController::class, 'deleteParking']);
        Route::delete('/dashboard/ticket/{id}', [TransactionController::class, 'deleteTicket']);
        Route::delete('/dashboard/transactions/delete-all', [TransactionController::class, 'deleteAllTransactions']);
    });




    // route untuk role loketresto
    Route::middleware(['role:loketresto'])->group(function () {

        Route::get('/dashboard-loketresto', [DashboardLoketResto::class, 'index'])->name('dashboard.resto');
        Route::get('/dashboard/resto/transactions', [TransactionController::class, 'indexResto']);
        Route::get('/dashboard/resto/transactions/filter', [TransactionController::class, 'restotransactionfilter']);

        Route::post('/resto/store', [DashboardLoketResto::class, 'store']);


        Route::delete('/dashboarad/resto/transactions/delete/{id}', [TransactionController::class, 'deletefindResto']);
        Route::delete('/dashboard/resto/transactions/delete-all', [TransactionController::class, 'deleteAllRestoTransactions']);
    });

    // route untuk role loket wahana
    Route::middleware(('role:loketwahana'))->group(function () {
        Route::post('/dashboard/wahana/store', [DashboardLoketWahana::class, 'store']);

        Route::get('/dashboard-loketwahana', [DashboardLoketWahana::class, 'index'])->name('dashboard.wahana');
        Route::get('/dashboard/wahana/transactions', [TransactionController::class, 'indexWahana']);
        Route::get('/dashboard/wahana/transactions/filter', [TransactionController::class, 'wahanatransactionfilter']);

        Route::delete('/dashboard/wahana/transactions/delete/{id}', [TransactionController::class, 'deletefindWahana']);
        Route::delete('/dashboard/wahana/transactions/delete-all', [TransactionController::class, 'deleteAllWahanaTransactions']);
    });

    // route untuk role lokettoilet
    Route::middleware(['role:lokettoilet'])->group(function () {

        Route::post('/dashboard/toilet/store', [DashboardLoketToilet::class, 'store']);

        Route::get('/dashboard-lokettoilet', [DashboardLoketToilet::class, 'index'])->name('dashboard.toilet');
        Route::get('/dashboard/toilet/transactions', [TransactionController::class, 'indexToilet']);
        Route::get('/dashboard/toilet/transactions/filter', [TransactionController::class, 'toilettransactionfilter']);

        Route::delete('/dashboard/toilet/transactions/delete/{id}', [TransactionController::class, 'deletefindToilet']);
        Route::delete('/dashboard/toilet/transactions/delete-all', [TransactionController::class, 'deleteAllToiletTransactions']);
    });


    // route untuk role bantuan

    Route::middleware(['role:bantuan'])->group(function () {
        Route::get('/dashboard-bantuan', [DashboardBantuan::class, 'index'])->name('dashboard.bantuan');
        Route::get('dashboard/bantuan/transactions', [TransactionController::class, 'indexbantuan']);
        Route::get('/dashboard/bantuan/transactions/filter', [TransactionController::class, 'bantuantransactionfilter']);


        Route::post('/dashboard/bantuan-income/store', [DashboardBantuan::class, 'store']);

        Route::delete('dashboard/bantuan/transactions/delete/{id}', [TransactionController::class, 'deletefindbantuan']);
        Route::delete('dashboard/bantuan/transactions/delete-all', [TransactionController::class, 'deleteAllBantuanTransactions']);
    });
});









Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




require __DIR__ . '/auth.php';
