<?php

use Illuminate\Support\Facades\Session;
use App\Models\Setting;

if (!function_exists('formatPrice')) {
    function formatPrice($amount, $baseCurrency = 'XOF')
    {
        if ($amount === null || $amount === '') {
            return '';
        }

        $targetCurrency = Session::get('currency', 'XOF');

        if ($targetCurrency === $baseCurrency) {
            return number_format($amount, 2, '.', ' ') . ' ' . $baseCurrency;
        }

        $currenciesSetting = Setting::where('key', 'platform.available_currencies')->first();
        $currencies = $currenciesSetting ? json_decode($currenciesSetting->value, true) : [];

        $exchangeRate = 1;
        foreach ($currencies as $currency) {
            if ($currency['code'] === $targetCurrency) {
                $exchangeRate = $currency['exchange_rate'] ?? 1;
                break;
            }
        }

        // Assuming exchange rate is target/base (e.g. 0.0016 for USD/XOF)
        $convertedAmount = $amount * $exchangeRate;

        // Determine decimal places. Usually 0 for XOF, 2 for others.
        $decimals = ($targetCurrency === 'XOF' || $targetCurrency === 'FCFA') ? 0 : 2;

        return number_format($convertedAmount, $decimals, '.', ' ') . ' ' . $targetCurrency;
    }
}
