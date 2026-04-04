<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Setting;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('layouts.app', function ($view) {
            $currenciesSetting = Setting::where('key', 'platform.available_currencies')->first();
            $availableCurrencies = $currenciesSetting ? json_decode($currenciesSetting->value, true) : [];
            $view->with('availableCurrencies', $availableCurrencies);
        });
    }

    public function register()
    {
        //
    }
}
