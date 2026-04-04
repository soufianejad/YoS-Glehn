<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use App\Models\Setting;

class ChangeCurrencyController extends Controller
{
    /**
     * Change the application currency and redirect back.
     *
     * @param  string  $currencyCode
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeCurrency($currencyCode)
    {
        $currenciesSetting = Setting::where('key', 'platform.available_currencies')->first();
        $availableCurrencies = $currenciesSetting ? json_decode($currenciesSetting->value, true) : [];
        $allowedCodes = array_column($availableCurrencies, 'code');

        if (in_array(strtoupper($currencyCode), $allowedCodes)) {
            Session::put('currency', strtoupper($currencyCode));
        }

        return redirect()->back();
    }
}
