<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\TotalIncomeResource;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';

    public static function getSlug(): string
    {
        return 'dashboard';
    }

    public static function getNavigationLabel(): string
    {
        return 'Dashboard';
    }

    /**
     * Kontrol apakah page ini tampil di sidebar navigation
     */
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::guard('admin')->check()
            && Auth::guard('admin')->user()->role === 'super_admin';
    }

    /**
     * Kalau admin nekat akses lewat URL, redirect
     */
    public function mount(): void
    {
        $user = Auth::guard('admin')->user();

        if ($user?->role !== 'super_admin') {
           abort(403);
            // redirect()->to(TotalIncomeResource::getUrl());
        }
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverview::class,
            \App\Filament\Widgets\BlogPostsChart::class,
            \App\Filament\Widgets\Visitors::class,
        ];
    }
}
