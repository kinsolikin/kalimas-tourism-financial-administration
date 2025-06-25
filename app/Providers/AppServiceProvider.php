<?php

namespace App\Providers;

use App\Models\Income;
use App\Models\Expanse;
use App\Models\TotalIncome;
use App\Models\TotalExpanse;
use Illuminate\Http\Request;
use App\Models\Expanse_Mendadak;
use App\Observers\IncomeObserver;
use App\Observers\ExpanseObserver;
use App\Models\Expanse_Operasional;
use Illuminate\Support\Facades\URL;
use App\Models\Resto_income_details;
use App\Models\Ticket_income_details;
use App\Models\Toilet_income_details;
use App\Models\Wahana_income_details;
use App\Models\Bantuan_income_details;
use App\Models\Parking_income_details;
use App\Observers\TotalIncomeObserver;
use App\Observers\IncomeDetailObserver;
use App\Observers\TotalExpanseObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use App\Observers\Expanse_MendadakObserver;
use Illuminate\Support\Facades\RateLimiter;
use App\Observers\RestoIncomeDetailObserver;
use App\Observers\TicketIncomeDetailObserver;
use App\Observers\ToiletIncomeDetailObserver;
use App\Observers\WahanaincomeDetailObserver;
use App\Observers\BantuanIncomeDetailObserver;
use App\Observers\Expanse_OperasionalObserver;
use App\Observers\ParkingIncomeDetailObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
   public function boot(): void
{
    if (
        request()->header('x-forwarded-proto') === 'https' ||
        app()->environment('production')
    ) {
        URL::forceScheme('https');
    }

    // Observer registrations tetap seperti sebelumnya
    TotalExpanse::observe(TotalExpanseObserver::class);
    TotalIncome::observe(TotalIncomeObserver::class);
    Expanse_Operasional::observe(Expanse_OperasionalObserver::class);
    Expanse_Mendadak::observe(Expanse_MendadakObserver::class);
    Income::observe(IncomeObserver::class);
    Ticket_income_details::observe(TicketIncomeDetailObserver::class);
    Parking_income_details::observe(ParkingIncomeDetailObserver::class);
    Resto_income_details::observe(RestoIncomeDetailObserver::class);
    Wahana_income_details::observe(WahanaincomeDetailObserver::class);
    Toilet_income_details::observe(ToiletIncomeDetailObserver::class);
    Bantuan_income_details::observe(BantuanIncomeDetailObserver::class);
    Expanse::observe(ExpanseObserver::class);
}

}