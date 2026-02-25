<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class LanguageSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'platform.available_languages'],
            [
                'value' => json_encode([
                    ['code' => 'en', 'name' => 'English'],
                    ['code' => 'fr', 'name' => 'French'],
                    ['code' => 'es', 'name' => 'Spanish'],
                ]),
                'type' => 'json',
                'group' => 'platform',
            ]
        );
    }
}
