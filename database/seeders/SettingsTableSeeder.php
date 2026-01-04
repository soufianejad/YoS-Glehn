<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->truncate();

        $settings = [
            // Platform revenue share settings
            [
                'key' => 'platform.default_author_share',
                'value' => '80',
                'type' => 'number',
                'group' => 'platform',
            ],
            [
                'key' => 'platform.default_platform_share',
                'value' => '20',
                'type' => 'number',
                'group' => 'platform',
            ],

            // Individual subscription prices
            [
                'key' => 'subscription.individual_monthly_price',
                'value' => '7000',
                'type' => 'number',
                'group' => 'subscription',
            ],
            [
                'key' => 'subscription.individual_annual_price',
                'value' => '50000',
                'type' => 'number',
                'group' => 'subscription',
            ],

            // Individual purchase prices
            [
                'key' => 'purchase.pdf_price',
                'value' => '3000',
                'type' => 'number',
                'group' => 'purchase',
            ],
            [
                'key' => 'purchase.audio_price',
                'value' => '3500',
                'type' => 'number',
                'group' => 'purchase',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
