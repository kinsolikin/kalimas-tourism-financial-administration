<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::guard('admin')->user();

        if ($user?->role === 'super_admin') {
            return redirect()->route('filament.admin.pages.dashboard');
        }

        if ($user?->role === 'admin') {
            return redirect()->route('filament.admin.resources.total-incomes.index');
        }

        return redirect('/'); // fallback
    }
}
