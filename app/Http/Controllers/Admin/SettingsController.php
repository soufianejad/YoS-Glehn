<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
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
