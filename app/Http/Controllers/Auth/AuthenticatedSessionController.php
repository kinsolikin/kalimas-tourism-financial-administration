<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use App\Models\Shift;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        $user = Auth::user();

        if ($user->id == 1) {
            // Buatkan entri shift untuk user id 1 dan 2
            $targetUserIds = [1, 2];
        } else {
            // Hanya untuk user yang sedang login
            $targetUserIds = [$user->id];
        }

        foreach ($targetUserIds as $userId) {
            Shift::firstOrCreate(
                [
                    'user_id' => $userId,
                    'end_time' => null,
                    'created_at' => now()->startOfDay(),
                ],
                [
                    'start_time' => now(),
                    'total_pendapatan' => 0,
                ]
            );
        }


        return redirect()->intended(match ($user->role) {
            'lokettiketparkir' => route('dashboard.tiketparkir'),
            'loketresto' => route('dashboard.resto'),
            'loketwahana' => route('dashboard.wahana'),
            'lokettoilet' => route('dashboard.toilet'),
            'bantuan' => route('dashboard.bantuan'),
            // default => RouteServiceProvider::HOME,
        });
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {


        $user = Auth::user();

        if ($user->id == 1) {
            // Akhiri shift untuk user id 1 dan 2
            $targetUserIds = [1, 2];
        } else {
            // Akhiri shift hanya untuk user yang login
            $targetUserIds = [$user->id];
        }

        foreach ($targetUserIds as $userId) {
            // Ambil shift yang belum berakhir untuk masing-masing user
            $shift = Shift::where('user_id', $userId)
                ->whereNull('end_time')
                ->whereDate('created_at', now()->toDateString())
                ->first();

            // Ambil total pendapatan untuk user tersebut
            $userTarget = User::find($userId);
            $totalpendapatan = $userTarget->income()->value('amount') ?? 0;

            if ($shift) {
                $shift->update([
                    'end_time' => now(),
                    'total_pendapatan' => $totalpendapatan
                ]);
            }
        }


        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
