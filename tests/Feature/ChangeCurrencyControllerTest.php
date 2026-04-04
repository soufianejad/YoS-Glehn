<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Setting;
use Illuminate\Support\Facades\Session;

class ChangeCurrencyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_changes_the_currency_if_valid()
    {
        $setting = Setting::firstOrNew(['key' => 'platform.available_currencies']);
        $setting->value = json_encode([['code' => 'XOF', 'name' => 'Franc CFA', 'exchange_rate' => 1], ['code' => 'USD', 'name' => 'Dollar', 'exchange_rate' => 0.0016]]);
        $setting->type = 'json';
        $setting->group = 'platform';
        $setting->save();

        $response = $this->get(route('change.currency', ['currency' => 'usd']));

        $response->assertRedirect();
        $this->assertEquals('USD', Session::get('currency'));
    }

    public function test_it_does_not_change_currency_if_invalid()
    {
        $setting = Setting::firstOrNew(['key' => 'platform.available_currencies']);
        $setting->value = json_encode([['code' => 'XOF', 'name' => 'Franc CFA', 'exchange_rate' => 1]]);
        $setting->type = 'json';
        $setting->group = 'platform';
        $setting->save();

        Session::put('currency', 'XOF');

        $response = $this->get(route('change.currency', ['currency' => 'EUR']));

        $response->assertRedirect();
        $this->assertEquals('XOF', Session::get('currency'));
    }
}
