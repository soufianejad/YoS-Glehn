<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SchoolViewComposerServiceProvider extends ServiceProvider
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
        View::composer('layouts.school', function ($view) {
            if (auth()->check() && auth()->user()->isSchool()) {
                $school = auth()->user()->managedSchool()->first();
                $view->with('school', $school);
            }
        });
    }
}
