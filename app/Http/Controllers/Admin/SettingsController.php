<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
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

        return view('admin.settings.payment', compact('settings'));
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
                    return back()->with('error', 'Language already exists.');
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

        return back()->with('success', 'Languages updated successfully.');
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');

        return back()->with('success', 'Application cache cleared.');
    }

    public function toggleMaintenance()
    {
        if (app()->isDownForMaintenance()) {
            Artisan::call('up');
            $message = 'Application is now live.';
        } else {
            Artisan::call('down');
            $message = 'Application is now in maintenance mode.';
        }

        return back()->with('success', $message);
    }
}
