<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countryConfigs = [
            'CI' => ['currency' => 'XOF', 'code' => '225', 'iso' => 'CI', 'name' => "Côte d'Ivoire"],
            'BJ' => ['currency' => 'XOF', 'code' => '229', 'iso' => 'BJ', 'name' => 'Bénin'],
            'BF' => ['currency' => 'XOF', 'code' => '226', 'iso' => 'BF', 'name' => 'Burkina Faso'],
            'CM' => ['currency' => 'XAF', 'code' => '237', 'iso' => 'CM', 'name' => 'Cameroun'],
            'GW' => ['currency' => 'XOF', 'code' => '245', 'iso' => 'GW', 'name' => 'Guinée-Bissau'],
            'ML' => ['currency' => 'XOF', 'code' => '223', 'iso' => 'ML', 'name' => 'Mali'],
            'NE' => ['currency' => 'XOF', 'code' => '227', 'iso' => 'NE', 'name' => 'Niger'],
            'SN' => ['currency' => 'XOF', 'code' => '221', 'iso' => 'SN', 'name' => 'Sénégal'],
            'TG' => ['currency' => 'XOF', 'code' => '228', 'iso' => 'TG', 'name' => 'Togo'],
            'CD' => ['currency' => 'CDF', 'code' => '243', 'iso' => 'CD', 'name' => 'RD Congo'],
            'GA' => ['currency' => 'XAF', 'code' => '241', 'iso' => 'GA', 'name' => 'Gabon'],
            'CG' => ['currency' => 'XAF', 'code' => '242', 'iso' => 'CG', 'name' => 'Congo Brazzaville'],
            'KE' => ['currency' => 'KES', 'code' => '254', 'iso' => 'KE', 'name' => 'Kenya'],
            'MW' => ['currency' => 'MWK', 'code' => '265', 'iso' => 'MW', 'name' => 'Malawi'],
            'RW' => ['currency' => 'RWF', 'code' => '250', 'iso' => 'RW', 'name' => 'Rwanda'],
            'SL' => ['currency' => 'SLL', 'code' => '232', 'iso' => 'SL', 'name' => 'Sierra Leone'],
            'GH' => ['currency' => 'GHS', 'code' => '233', 'iso' => 'GH', 'name' => 'Ghana'],
            'TZ' => ['currency' => 'TZS', 'code' => '255', 'iso' => 'TZ', 'name' => 'Tanzanie'],
            'UG' => ['currency' => 'UGX', 'code' => '256', 'iso' => 'UG', 'name' => 'Ouganda'],
            'ZM' => ['currency' => 'ZMW', 'code' => '260', 'iso' => 'ZM', 'name' => 'Zambie'],
            'NG' => ['currency' => 'NGN', 'code' => '234', 'iso' => 'NG', 'name' => 'Nigéria'],
            'MA' => ['currency' => 'MAD', 'code' => '212', 'iso' => 'MA', 'name' => 'Maroc'],
            'FR' => ['currency' => 'EUR', 'code' => '33',  'iso' => 'FR', 'name' => 'France'],
            'MZ' => ['currency' => 'MZN', 'code' => '258', 'iso' => 'MZ', 'name' => 'Mozambique'],
            'OT' => ['currency' => 'XOF', 'code' => '',    'iso' => 'OT', 'name' => 'Autres pays'],
        ];

        $order = 1;
        foreach ($countryConfigs as $iso => $data) {
            Country::updateOrCreate(
                ['iso' => $iso],
                [
                    'name' => $data['name'],
                    'currency' => $data['currency'],
                    'code' => $data['code'],
                    'order' => $order++
                ]
            );
        }
    }
}
