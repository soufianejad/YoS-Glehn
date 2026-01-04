<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'site_name', 'value' => 'Plateforme de Lecture', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Une plateforme pour la promotion de la littérature Africaine.', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'contact@plateforme.com', 'group' => 'general'],
            ['key' => 'default_language', 'value' => 'fr', 'group' => 'general'],

            // Appearance
            ['key' => 'primary_color', 'value' => '#1e40af', 'group' => 'appearance'],

            // Payment
            ['key' => 'currency_code', 'value' => 'XOF', 'group' => 'payment'],
            ['key' => 'currency_symbol', 'value' => 'FCFA', 'group' => 'payment'],

            // Mail
            ['key' => 'mail_from_address', 'value' => 'noreply@plateforme.com', 'group' => 'mail'],
            ['key' => 'mail_from_name', 'value' => 'Plateforme de Lecture', 'group' => 'mail'],

            // Platform revenue share settings
            ['key' => 'platform.default_author_share', 'value' => '80', 'group' => 'platform'],
            ['key' => 'platform.default_platform_share', 'value' => '20', 'group' => 'platform'],

            // Individual subscription prices
            ['key' => 'subscription.individual_monthly_price', 'value' => '7000', 'group' => 'subscription'],
            ['key' => 'subscription.individual_annual_price', 'value' => '50000', 'group' => 'subscription'],

            // Individual purchase prices
            ['key' => 'purchase.pdf_price', 'value' => '3000', 'group' => 'purchase'],
            ['key' => 'purchase.audio_price', 'value' => '3500', 'group' => 'purchase'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
