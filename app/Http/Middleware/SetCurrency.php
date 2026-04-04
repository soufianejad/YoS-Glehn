<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Setting;

class SetCurrency
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('currency')) {
            $currenciesSetting = Setting::where('key', 'platform.available_currencies')->first();
            $availableCurrencies = $currenciesSetting ? json_decode($currenciesSetting->value, true) : [];

            if (!empty($availableCurrencies)) {
                Session::put('currency', $availableCurrencies[0]['code']);
            } else {
                Session::put('currency', 'XOF');
            }
        }

        return $next($request);
    }
}
