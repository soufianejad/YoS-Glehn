<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            $setting = Setting::firstOrNew(['key' => $key]);
            // Extract the group from the first part of the key if possible, e.g., platform.show_prices -> platform
            $parts = explode('.', $key);
            $group = count($parts) > 1 ? $parts[0] : 'general';

            $setting->group = $group;
            $setting->value = $value;
            $setting->save();
        }

        return back()->with('success', __('Paramètres mis à jour avec succès.'));
    }

    public function index()
    {
        // For now, redirect to general settings
        return redirect()->route('admin.settings.general');
    }

    public function general()
    {
        $settings = Setting::where('group', 'general')->pluck('value', 'key');

        return view('admin.settings.general', compact('settings'));
    }

    public function payment()
    {
        $settings = Setting::where('group', 'payment')->pluck('value', 'key');
        // Decode JSON values if they exist
        $payment_methods = isset($settings['payment_methods']) ? json_decode($settings['payment_methods'], true) : [];

        return view('admin.settings.payment', compact('settings', 'payment_methods'));
    }

    public function storeCountry(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'iso' => 'required|string|size:2|unique:countries,iso',
            'currency' => 'required|string|size:3',
            'code' => 'nullable|string|max:10',
        ]);

        $maxOrder = \App\Models\Country::max('order') ?? 0;
        $validated['order'] = $maxOrder + 1;
        $validated['iso'] = strtoupper($validated['iso']);
        $validated['currency'] = strtoupper($validated['currency']);

        \App\Models\Country::create($validated);

        return back()->with('success', __('Pays ajouté avec succès.'));
    }

    public function updatePayment(Request $request)
    {
        // Update countries order
        $countriesOrder = $request->input('countries_order', []);
        if (!empty($countriesOrder)) {
            foreach ($countriesOrder as $index => $iso) {
                \App\Models\Country::where('iso', $iso)->update(['order' => $index + 1]);
            }
        }

        // We expect an array of [country_code][method_code] = on
        $methods = $request->input('methods', []);
        $methodsOrder = $request->input('methods_order', []);

        // Include ALL countries that exist, even if they have no methods sent in the request (empty array)
        // so that they don't fall back to default methods in PaymentService.
        $allCountriesIso = \App\Models\Country::pluck('iso')->toArray();

        // Sort the checked methods array according to the order submitted
        $orderedMethods = [];

        foreach ($allCountriesIso as $iso) {
            $orderedMethods[$iso] = []; // Initialize empty for the country

            if (isset($methods[$iso]) && isset($methodsOrder[$iso])) {
                $countryMethods = $methods[$iso];
                // Add methods in the order they appear in methods_order
                foreach ($methodsOrder[$iso] as $methodCode) {
                    if (isset($countryMethods[$methodCode])) {
                        $orderedMethods[$iso][$methodCode] = $countryMethods[$methodCode];
                    }
                }
            } elseif (isset($methods[$iso])) {
                 $orderedMethods[$iso] = $methods[$iso];
            }
        }
        
        $setting = Setting::firstOrNew(['key' => 'payment_methods', 'group' => 'payment']);
        $setting->value = json_encode($orderedMethods);
        $setting->type = 'json';
        $setting->save();

        // Also update other individual payment settings if any
        foreach ($request->except(['_token', '_method', 'methods', 'countries_order', 'methods_order']) as $key => $value) {
            $s = Setting::firstOrNew(['key' => $key, 'group' => 'payment']);
            $s->value = $value;
            $s->type = 'string';
            $s->save();
        }

        return back()->with('success', __('Paramètres de paiement mis à jour.'));
    }

    public function email()
    {
        $settings = Setting::where('group', 'email')->pluck('value', 'key');

        return view('admin.settings.email', compact('settings'));
    }

    public function appearance()
    {
        $settings = Setting::where('group', 'appearance')->pluck('value', 'key');

        return view('admin.settings.appearance', compact('settings'));
    }

    public function languages()
    {
        $languagesSetting = Setting::where('key', 'platform.available_languages')->first();
        $languages = $languagesSetting ? json_decode($languagesSetting->value, true) : [];

        return view('admin.settings.languages', compact('languages'));
    }

    public function updateLanguages(Request $request)
    {
        $languagesSetting = Setting::firstOrNew(['key' => 'platform.available_languages']);
        $languages = $languagesSetting->value ? json_decode($languagesSetting->value, true) : [];

        if ($request->has('add_language')) {
            $request->validate([
                'language_code' => 'required|string',
                'language_name' => 'required|string',
            ]);

            // check if language already exists
            foreach ($languages as $language) {
                if ($language['code'] === $request->language_code) {
                    return back()->with('error', __('Language already exists.'));
                }
            }

            $languages[] = ['code' => $request->language_code, 'name' => $request->language_name];
        }

        if ($request->has('remove_language')) {
            $languages = array_filter($languages, function ($language) use ($request) {
                return $language['code'] !== $request->language_code;
            });
        }

        $languagesSetting->value = json_encode(array_values($languages));
        $languagesSetting->type = 'json';
        $languagesSetting->group = 'platform';
        $languagesSetting->save();

        return back()->with('success', __('Languages updated successfully.'));
    }


    public function currencies()
    {
        $currenciesSetting = Setting::where('key', 'platform.available_currencies')->first();
        $currencies = $currenciesSetting ? json_decode($currenciesSetting->value, true) : [];

        return view('admin.settings.currencies', compact('currencies'));
    }

    public function updateCurrencies(Request $request)
    {
        $currenciesSetting = Setting::firstOrNew(['key' => 'platform.available_currencies']);
        $currencies = $currenciesSetting->value ? json_decode($currenciesSetting->value, true) : [];

        if ($request->has('add_currency')) {
            $request->validate([
                'currency_code' => 'required|string',
                'currency_name' => 'required|string',
                'exchange_rate' => 'required|numeric',
            ]);

            // check if currency already exists
            foreach ($currencies as $currency) {
                if ($currency['code'] === strtoupper($request->currency_code)) {
                    return back()->with('error', __('Devise déjà existante.'));
                }
            }

            $currencies[] = [
                'code' => strtoupper($request->currency_code),
                'name' => $request->currency_name,
                'exchange_rate' => $request->exchange_rate
            ];
        }

        if ($request->has('remove_currency')) {
            $currencies = array_filter($currencies, function ($currency) use ($request) {
                return $currency['code'] !== $request->currency_code;
            });
        }

        $currenciesSetting->value = json_encode(array_values($currencies));
        $currenciesSetting->type = 'json';
        $currenciesSetting->group = 'platform';
        $currenciesSetting->save();

        return back()->with('success', __('Devises mises à jour avec succès.'));
    }
    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');

        return back()->with('success', __('Application cache cleared.'));
    }

    public function toggleMaintenance()
    {
        if (app()->isDownForMaintenance()) {
            Artisan::call('up');
            $message = __('Application is now live.');
        } else {
            Artisan::call('down');
            $message = __('Application is now in maintenance mode.');
        }

        return back()->with('success', $message);
    }
}
