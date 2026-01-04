<?php

namespace App\Providers;

use App\Models\Revenue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AdminViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('partials.sidebar-admin', function ($view) {
            if (Auth::check() && Auth::user()->isAdmin()) {
                $pendingRevenuesCount = Revenue::where('status', 'pending')->count();
                $view->with('pendingRevenuesCount', $pendingRevenuesCount);
            } else {
                $view->with('pendingRevenuesCount', 0);
            }
        });
    }
}
