<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class ChangeLanguageController extends Controller
{
    /**
     * Change the application language and redirect back.
     *
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeLocale($locale)
    {
        if (in_array($locale, config('app.available_locales', ['en', 'fr']))) {
            App::setLocale($locale);
            Session::put('locale', $locale);
        }

        return redirect()->back();
    }
}
