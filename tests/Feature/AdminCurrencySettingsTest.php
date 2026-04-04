<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;

class AdminCurrencySettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_view_currencies()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.currencies'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.settings.currencies');
    }

    public function test_admin_can_add_currency()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.settings.currencies.update'), [
            'add_currency' => true,
            'currency_code' => 'USD',
            'currency_name' => 'US Dollar',
            'exchange_rate' => '0.0016'
        ]);

        $response->assertRedirect();

        $setting = Setting::where('key', 'platform.available_currencies')->first();
        $this->assertNotNull($setting);

        $currencies = json_decode($setting->value, true);
        $this->assertCount(1, $currencies);
        $this->assertEquals('USD', $currencies[0]['code']);
    }
}
