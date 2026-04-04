<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Setting;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Middleware\SetCurrency;

class SetCurrencyMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sets_default_currency_if_none_set()
    {
        $setting = Setting::firstOrNew(['key' => 'platform.available_currencies']);
        $setting->value = json_encode([['code' => 'XOF', 'name' => 'Franc CFA', 'exchange_rate' => 1]]);
        $setting->type = 'json';
        $setting->group = 'platform';
        $setting->save();

        $request = Request::create('/test', 'GET');

        // Ensure session is started and flush it
        $request->setLaravelSession($this->app['session']->driver());
        Session::flush();

        $middleware = new SetCurrency();
        $middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals('XOF', Session::get('currency'));
    }

    public function test_it_does_not_override_existing_currency()
    {
        $setting = Setting::firstOrNew(['key' => 'platform.available_currencies']);
        $setting->value = json_encode([['code' => 'XOF', 'name' => 'Franc CFA', 'exchange_rate' => 1], ['code' => 'USD', 'name' => 'Dollar', 'exchange_rate' => 0.0016]]);
        $setting->type = 'json';
        $setting->group = 'platform';
        $setting->save();

        $request = Request::create('/test', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        Session::put('currency', 'USD');

        $middleware = new SetCurrency();
        $middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals('USD', Session::get('currency'));
    }
}
