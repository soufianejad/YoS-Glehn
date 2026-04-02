<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Setting;
use App\Models\Subscription;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use SoapClient;

class PaymentService
{
    protected $countryConfigs;
    protected $supportedMethods;

    protected $currencyToPaiementProCode = [
        'XOF' => '952', 'EUR' => '978', 'CDF' => '976', 'XAF' => '950',
        'KES' => '404', 'MWK' => '454', 'RWF' => '646', 'SLL' => '694',
        'GHS' => '936', 'TZS' => '834', 'UGX' => '800', 'ZMW' => '967',
        'NGN' => '566', 'MAD' => '504', 'MZN' => '943',
    ];

    // Devises qui nécessitent la multiplication × 100 pour Paystack (en centimes)
    protected $paystackKoboCurrencies = ['NGN', 'GHS', 'KES', 'ZAR'];

    public function __construct()
    {
        $this->initializeConfigurations();
    }

    public function getGlobalConfigurations(): array
    {
        // Dynamically fetch countries from DB
        $countries = \App\Models\Country::orderBy('order')->get()->keyBy('iso')->toArray();

        // Merge with defaults so we don't lose default countries if DB only has custom ones.
        if (empty($countries)) {
            $countries = $this->countryConfigs;
        } else {
            foreach ($this->countryConfigs as $iso => $config) {
                if (!isset($countries[$iso])) {
                    $countries[$iso] = $config;
                }
            }
        }

        return ['countries' => $countries, 'methods' => $this->supportedMethods];
    }

    private function initializeConfigurations(): void
    {
        // Default configs, just in case DB is empty. Real data is in the database now.
        $this->countryConfigs = [
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

        $all = array_keys($this->countryConfigs);

        $this->supportedMethods = [
            // ─── TouchPay / PaiementPro ────────────────────────────────────
            'MOMOCI'    => ['name'=>'MTN CI',        'icon_color'=>'#FFCC00','provider'=>'touchpay',    'group'=>'Mobile Money','countries'=>['CI']],
            'OMCIV2'    => ['name'=>'Orange CI',     'icon_color'=>'#FF6600','provider'=>'touchpay',    'group'=>'Mobile Money','countries'=>['CI','FR']],
            'FLOOZ'     => ['name'=>'Moov CI',       'icon_color'=>'#0055B3','provider'=>'touchpay',    'group'=>'Mobile Money','countries'=>['CI']],
            'WAVECI'    => ['name'=>'Wave CI',       'icon_color'=>'#009FE3','provider'=>'touchpay',    'group'=>'Mobile Money','countries'=>['CI','FR']],
            'MOMOBJ'    => ['name'=>'MTN Bénin',     'icon_color'=>'#FFCC00','provider'=>'paiementpro', 'group'=>'Mobile Money','countries'=>['BJ']],
            'FLOOZBJ'   => ['name'=>'Moov Bénin',   'icon_color'=>'#0055B3','provider'=>'paiementpro', 'group'=>'Mobile Money','countries'=>['BJ']],
            'OMBF'      => ['name'=>'Orange BF',     'icon_color'=>'#FF6600','provider'=>'paiementpro', 'group'=>'Mobile Money','countries'=>['BF']],
            'FLOOZ_BFA' => ['name'=>'Moov BF',       'icon_color'=>'#0055B3','provider'=>'paiementpro', 'group'=>'Mobile Money','countries'=>['BF']],
            'OMCM'      => ['name'=>'Orange CM',     'icon_color'=>'#FF6600','provider'=>'paiementpro', 'group'=>'Mobile Money','countries'=>['CM']],
            'MOMOCM'    => ['name'=>'MTN CM',        'icon_color'=>'#FFCC00','provider'=>'paiementpro', 'group'=>'Mobile Money','countries'=>['CM']],
            'OMGN'      => ['name'=>'Orange GN',     'icon_color'=>'#FF6600','provider'=>'paiementpro', 'group'=>'Mobile Money','countries'=>['GW']],
            'OMML'      => ['name'=>'Orange ML',     'icon_color'=>'#FF6600','provider'=>'paiementpro', 'group'=>'Mobile Money','countries'=>['ML']],
            'AIRTELNG'  => ['name'=>'Airtel NE',     'icon_color'=>'#E40000','provider'=>'paiementpro', 'group'=>'Mobile Money','countries'=>['NE']],
            'OMSN'      => ['name'=>'Orange SN',     'icon_color'=>'#FF6600','provider'=>'paiementpro', 'group'=>'Mobile Money','countries'=>['SN']],
            'WAVESN'    => ['name'=>'Wave SN',       'icon_color'=>'#009FE3','provider'=>'paiementpro', 'group'=>'Mobile Money','countries'=>['SN']],
            'MOOTG'     => ['name'=>'Flooz Togo',    'icon_color'=>'#0055B3','provider'=>'paiementpro', 'group'=>'Mobile Money','countries'=>['TG']],
            'TOGOCEL'   => ['name'=>'Togocel',       'icon_color'=>'#FFD700','provider'=>'paiementpro', 'group'=>'Mobile Money','countries'=>['TG']],
            // ─── PawaPay ──────────────────────────────────────────────────
            'MTN_MOMO_CIV'      => ['name'=>'MTN CI',       'icon_color'=>'#FFCC00','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['CI']],
            'ORANGE_CIV'        => ['name'=>'Orange CI',    'icon_color'=>'#FF6600','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['CI','FR']],
            'WAVE_CIV'          => ['name'=>'Wave CI',      'icon_color'=>'#009FE3','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['CI','FR']],
            'MTN_MOMO_BEN'      => ['name'=>'MTN Bénin',    'icon_color'=>'#FFCC00','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['BJ']],
            'MOOV_BEN'          => ['name'=>'Moov Bénin',   'icon_color'=>'#0055B3','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['BJ']],
            'MOOV_BFA'          => ['name'=>'Moov BF',      'icon_color'=>'#0055B3','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['BF']],
            'ORANGE_BFA'        => ['name'=>'Orange BF',    'icon_color'=>'#FF6600','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['BF']],
            'MTN_MOMO_CMR'      => ['name'=>'MTN CM',       'icon_color'=>'#FFCC00','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['CM']],
            'ORANGE_CMR'        => ['name'=>'Orange CM',    'icon_color'=>'#FF6600','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['CM']],
            'FREE_SEN'          => ['name'=>'Free SN',      'icon_color'=>'#E40000','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['SN']],
            'WAVE_SEN'          => ['name'=>'Wave SN',      'icon_color'=>'#009FE3','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['SN']],
            'VODACOM_MPESA_COD' => ['name'=>'Vodacom MPesa','icon_color'=>'#E40000','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['CD']],
            'AIRTEL_COD'        => ['name'=>'Airtel RDC',   'icon_color'=>'#E40000','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['CD']],
            'ORANGE_COD'        => ['name'=>'Orange RDC',   'icon_color'=>'#FF6600','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['CD']],
            'AIRTEL_GAB'        => ['name'=>'Airtel Gabon', 'icon_color'=>'#E40000','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['GA']],
            'AIRTEL_COG'        => ['name'=>'Airtel Congo', 'icon_color'=>'#E40000','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['CG']],
            'MTN_MOMO_COG'      => ['name'=>'MTN Congo',    'icon_color'=>'#FFCC00','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['CG']],
            'MPESA_KEN'         => ['name'=>'MPesa Kenya',  'icon_color'=>'#34B233','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['KE']],
            'AIRTEL_MWI'        => ['name'=>'Airtel Malawi','icon_color'=>'#E40000','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['MW']],
            'TNM_MWI'           => ['name'=>'TNM Malawi',   'icon_color'=>'#006400','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['MW']],
            'AIRTEL_RWA'        => ['name'=>'Airtel RW',    'icon_color'=>'#E40000','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['RW']],
            'MTN_MOMO_RWA'      => ['name'=>'MTN Rwanda',   'icon_color'=>'#FFCC00','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['RW']],
            'ORANGE_SLE'        => ['name'=>'Orange SL',    'icon_color'=>'#FF6600','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['SL']],
            'MTN_MOMO_GHA'      => ['name'=>'MTN Ghana',    'icon_color'=>'#FFCC00','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['GH']],
            'AIRTELTIGO_GHA'    => ['name'=>'AirtelTigo',   'icon_color'=>'#8A2BE2','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['GH']],
            'VODAFONE_GHA'      => ['name'=>'Vodafone GH',  'icon_color'=>'#E60000','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['GH']],
            'AIRTEL_TZA'        => ['name'=>'Airtel TZ',    'icon_color'=>'#E40000','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['TZ']],
            'VODACOM_TZA'       => ['name'=>'Vodacom TZ',   'icon_color'=>'#E40000','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['TZ']],
            'TIGO_TZA'          => ['name'=>'Tigo TZ',      'icon_color'=>'#0000FF','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['TZ']],
            'HALOTEL_TZA'       => ['name'=>'Halotel TZ',   'icon_color'=>'#FF8C00','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['TZ']],
            'AIRTEL_OAPI_UGA'   => ['name'=>'Airtel UG',    'icon_color'=>'#E40000','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['UG']],
            'MTN_MOMO_UGA'      => ['name'=>'MTN Uganda',   'icon_color'=>'#FFCC00','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['UG']],
            'AIRTEL_OAPI_ZMB'   => ['name'=>'Airtel ZM',    'icon_color'=>'#E40000','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['ZM']],
            'MTN_MOMO_ZMB'      => ['name'=>'MTN Zambia',   'icon_color'=>'#FFCC00','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['ZM']],
            'ZAMTEL_ZMB'        => ['name'=>'Zamtel',       'icon_color'=>'#228B22','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['ZM']],
            'AIRTEL_NGA'        => ['name'=>'Airtel NG',    'icon_color'=>'#E40000','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['NG']],
            'MTN_MOMO_NGA'      => ['name'=>'MTN Nigeria',  'icon_color'=>'#FFCC00','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['NG']],
            'VODACOM_MOZ'       => ['name'=>'Vodacom MOZ',  'icon_color'=>'#E40000','provider'=>'pawapay','group'=>'Mobile Money','countries'=>['MZ']],
            // ─── Paystack (préfixe PS_) ───────────────────────────────────
            'PS_CARD'   => ['name'=>'Carte (Paystack)',       'icon_color'=>'#1a1f71','provider'=>'paystack','group'=>'Carte',         'countries'=>$all],
            'PS_MTN'    => ['name'=>'MTN (Paystack)',         'icon_color'=>'#FFCC00','provider'=>'paystack','group'=>'Mobile Money', 'countries'=>['GH','NG','CI','CM','RW','UG','ZM']],
            'PS_ORANGE' => ['name'=>'Orange (Paystack)',      'icon_color'=>'#FF6600','provider'=>'paystack','group'=>'Mobile Money', 'countries'=>['CI','SN','BF','ML','CM','MA','FR']],
            'PS_WAVE'   => ['name'=>'Wave (Paystack)',        'icon_color'=>'#009FE3','provider'=>'paystack','group'=>'Mobile Money', 'countries'=>['CI','SN','FR']],
            'PS_MPESA'  => ['name'=>'MPesa (Paystack)',       'icon_color'=>'#34B233','provider'=>'paystack','group'=>'Mobile Money', 'countries'=>['KE','MA']],
            'PS_ATL'    => ['name'=>'AirtelTigo (Paystack)',  'icon_color'=>'#8A2BE2','provider'=>'paystack','group'=>'Mobile Money', 'countries'=>['GH','KE']],
            'PS_VOD'    => ['name'=>'Vodafone (Paystack)',    'icon_color'=>'#E60000','provider'=>'paystack','group'=>'Mobile Money', 'countries'=>['GH','MA']],
            // ─── Carte PaiementPro ────────────────────────────────────────
            'CARD'      => ['name'=>'Visa / Mastercard',     'icon_color'=>'#1a1f71','provider'=>'paiementpro','group'=>'Carte',    'countries'=>$all],
        ];
    }

    // =========================================================================
    // MÉTHODES DISPONIBLES
    // =========================================================================

    public function getAvailablePaymentMethods(string $phone = null, string $countryIso = null): array
    {
        $countryCode = strtoupper($countryIso ?? ($phone ? $this->detectCountryFromPhone($phone) : 'CI'));

        // Si le pays n'est pas dans notre liste configurée, on bascule sur "Autres pays" (OT)
        if (!isset($this->countryConfigs[$countryCode])) {
            $countryCode = 'OT';
        }

        $dbSettings     = Setting::where('key', 'payment_methods')->first();
        $enabledMethods = $dbSettings ? json_decode($dbSettings->value, true) : null;

        $availableMethods = [];
        foreach ($this->supportedMethods as $code => $details) {
            if ($enabledMethods) {
                if (isset($enabledMethods[$countryCode][$code]) && $enabledMethods[$countryCode][$code] === 'on') {
                    $availableMethods[] = $this->fmt($code, $details);
                }
            } else {
                if (in_array($countryCode, $details['countries'])) {
                    $availableMethods[] = $this->fmt($code, $details);
                }
            }
        }

        if (empty($availableMethods)) {
            $availableMethods[] = ['id'=>'CARD','name'=>'Visa / Mastercard','icon_color'=>'#1a1f71','provider'=>'paiementpro','group'=>'Carte'];
        }

        return [
            'country'  => $countryCode,
            'currency' => $this->countryConfigs[$countryCode]['currency'] ?? 'XOF',
            'methods'  => $availableMethods,
        ];
    }

    private function fmt(string $code, array $d): array
    {
        return ['id'=>$code,'name'=>$d['name'],'icon_color'=>$d['icon_color'],'provider'=>$d['provider'],'group'=>$d['group']];
    }

    private function detectCountryFromPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        foreach ($this->countryConfigs as $iso => $config) {
            if (str_starts_with($phone, $config['code'])) return $iso;
        }
        return 'CI';
    }

    // =========================================================================
    // PAWAPAY – helpers internes
    // =========================================================================

    private function checkPawaPayProviderAvailability(string $country, string $provider, string $op = 'DEPOSIT'): ?string
    {
        try {
            $r = Http::withToken(env('PAWAPAY_API_TOKEN'))->timeout(10)
                ->get(env('PAWAPAY_BASE_URL','https://api.pawapay.io').'/v2/availability', ['country'=>$country,'operationType'=>$op]);
            if ($r->successful()) {
                foreach ($r->json() as $c) {
                    if ($c['country'] !== $country) continue;
                    foreach ($c['providers'] as $p) {
                        if ($p['provider'] !== $provider) continue;
                        foreach ($p['operationTypes'] as $t => $s) { if ($t === $op) return $s; }
                    }
                }
            }
        } catch (\Exception $e) { Log::error("PawaPay avail: ".$e->getMessage()); }
        return null;
    }

    private function getPawaPayProviderConfig(string $country, string $provider, string $op = 'DEPOSIT'): ?array
    {
        try {
            $r = Http::withToken(env('PAWAPAY_API_TOKEN'))->timeout(10)
                ->get(env('PAWAPAY_BASE_URL','https://api.pawapay.io').'/v2/active-conf', ['country'=>$country,'operationType'=>$op]);
            if ($r->successful()) {
                foreach ($r->json()['countries'] ?? [] as $c) {
                    if ($c['country'] !== $country) continue;
                    foreach ($c['providers'] as $p) {
                        if ($p['provider'] !== $provider) continue;
                        foreach ($p['currencies'] as $cur) {
                            foreach ($cur['operationTypes'] as $t => $d) {
                                if ($t === $op) return ['currency'=>$cur['currency'],'status'=>$d['status']??'UNKNOWN','callbackUrl'=>$d['callbackUrl']??null];
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) { Log::error("PawaPay conf: ".$e->getMessage()); }
        return null;
    }

    private function getPawaPayCountryConfig(?string $network): ?array
    {
        $map = [
            'MTN_MOMO_CIV'=>['country'=>'CIV','currency'=>'XOF','countryCode'=>'225'],
            'ORANGE_CIV'=>['country'=>'CIV','currency'=>'XOF','countryCode'=>'225'],
            'WAVE_CIV'=>['country'=>'CIV','currency'=>'XOF','countryCode'=>'225'],
            'MTN_MOMO_BEN'=>['country'=>'BEN','currency'=>'XOF','countryCode'=>'229'],
            'MOOV_BEN'=>['country'=>'BEN','currency'=>'XOF','countryCode'=>'229'],
            'MOOV_BFA'=>['country'=>'BFA','currency'=>'XOF','countryCode'=>'226'],
            'ORANGE_BFA'=>['country'=>'BFA','currency'=>'XOF','countryCode'=>'226'],
            'MTN_MOMO_CMR'=>['country'=>'CMR','currency'=>'XAF','countryCode'=>'237'],
            'ORANGE_CMR'=>['country'=>'CMR','currency'=>'XAF','countryCode'=>'237'],
            'FREE_SEN'=>['country'=>'SEN','currency'=>'XOF','countryCode'=>'221'],
            'WAVE_SEN'=>['country'=>'SEN','currency'=>'XOF','countryCode'=>'221'],
            'WAVESN'=>['country'=>'SEN','currency'=>'XOF','countryCode'=>'221'],
            'VODACOM_MPESA_COD'=>['country'=>'COD','currency'=>'CDF','countryCode'=>'243'],
            'AIRTEL_COD'=>['country'=>'COD','currency'=>'CDF','countryCode'=>'243'],
            'ORANGE_COD'=>['country'=>'COD','currency'=>'CDF','countryCode'=>'243'],
            'AIRTEL_GAB'=>['country'=>'GAB','currency'=>'XAF','countryCode'=>'241'],
            'AIRTEL_COG'=>['country'=>'COG','currency'=>'XAF','countryCode'=>'242'],
            'MTN_MOMO_COG'=>['country'=>'COG','currency'=>'XAF','countryCode'=>'242'],
            'MPESA_KEN'=>['country'=>'KEN','currency'=>'KES','countryCode'=>'254'],
            'AIRTEL_MWI'=>['country'=>'MWI','currency'=>'MWK','countryCode'=>'265'],
            'TNM_MWI'=>['country'=>'MWI','currency'=>'MWK','countryCode'=>'265'],
            'AIRTEL_RWA'=>['country'=>'RWA','currency'=>'RWF','countryCode'=>'250'],
            'MTN_MOMO_RWA'=>['country'=>'RWA','currency'=>'RWF','countryCode'=>'250'],
            'ORANGE_SLE'=>['country'=>'SLE','currency'=>'SLE','countryCode'=>'232'],
            'MTN_MOMO_GHA'=>['country'=>'GHA','currency'=>'GHS','countryCode'=>'233'],
            'AIRTELTIGO_GHA'=>['country'=>'GHA','currency'=>'GHS','countryCode'=>'233'],
            'VODAFONE_GHA'=>['country'=>'GHA','currency'=>'GHS','countryCode'=>'233'],
            'AIRTEL_TZA'=>['country'=>'TZA','currency'=>'TZS','countryCode'=>'255'],
            'VODACOM_TZA'=>['country'=>'TZA','currency'=>'TZS','countryCode'=>'255'],
            'TIGO_TZA'=>['country'=>'TZA','currency'=>'TZS','countryCode'=>'255'],
            'HALOTEL_TZA'=>['country'=>'TZA','currency'=>'TZS','countryCode'=>'255'],
            'AIRTEL_OAPI_UGA'=>['country'=>'UGA','currency'=>'UGX','countryCode'=>'256'],
            'MTN_MOMO_UGA'=>['country'=>'UGA','currency'=>'UGX','countryCode'=>'256'],
            'AIRTEL_OAPI_ZMB'=>['country'=>'ZMB','currency'=>'ZMW','countryCode'=>'260'],
            'MTN_MOMO_ZMB'=>['country'=>'ZMB','currency'=>'ZMW','countryCode'=>'260'],
            'ZAMTEL_ZMB'=>['country'=>'ZMB','currency'=>'ZMW','countryCode'=>'260'],
            'AIRTEL_NGA'=>['country'=>'NGA','currency'=>'NGN','countryCode'=>'234'],
            'MTN_MOMO_NGA'=>['country'=>'NGA','currency'=>'NGN','countryCode'=>'234'],
            'VODACOM_MOZ'=>['country'=>'MOZ','currency'=>'MZN','countryCode'=>'258'],
        ];
        return $map[$network] ?? null;
    }

    // =========================================================================
    // INITIATION PAIEMENT
    // =========================================================================

    public function initiatePayment(Request $request, Payment $payment, bool $returnAsJson = false, ?string $successUrl = null)
    {
        $network   = $request->input('network', 'CARD');
        $amount    = $payment->amount;
        $refNumber = $payment->transaction_id;

        $returnLink = route('payment.success');
        $errorLink  = route('payment.failed');

        Session::put('payment_return_url', $successUrl ?? route('home'));

        Log::channel('payment')->info("🚀 Initiation paiement", [
            'ref'     => $refNumber,
            'network' => $network,
            'amount'  => $amount,
        ]);

        // =====================================================================
        // 1. WAVE
        // =====================================================================
        if ($network === 'WAVECI') {
            try {
                $resp = Http::withToken(env('WAVE_API_KEY'))
                    ->post('https://api.wave.com/v1/checkout/sessions', [
                        'amount'                 => (string) $amount,
                        'currency'               => 'XOF',
                        'aggregated_merchant_id' => env('WAVE_AGGREGATED_MERCHANT_ID'),
                        'error_url'              => $errorLink,
                        'success_url'            => $returnLink,
                    ]);

                Log::channel('payment')->info("Wave réponse", [
                    'status' => $resp->status(),
                    'body'   => $resp->json(),
                ]);

                if ($resp->successful() && isset($resp->json()['wave_launch_url'])) {
                    $payment->update([
                        'payment_details' => array_merge($payment->payment_details ?? [], [
                            'wave_id' => $resp->json()['id'],
                        ]),
                    ]);
                    $url = $resp->json()['wave_launch_url'];
                    return $returnAsJson ? ['success' => true, 'redirect_url' => $url] : Redirect::away($url);
                }

                // Extraire le message d'erreur Wave
                $waveError = $resp->json()['message']
                    ?? $resp->json()['error']
                    ?? $resp->json()['detail']
                    ?? ('Erreur Wave (HTTP ' . $resp->status() . ')');

                return $this->handleError($waveError, $returnAsJson, $errorLink);

            } catch (\Exception $e) {
                Log::error("Wave exception [{$refNumber}]", ['error' => $e->getMessage()]);
                return $this->handleError('Erreur de connexion Wave : ' . $e->getMessage(), $returnAsJson, $errorLink);
            }
        }

        // =====================================================================
        // 2. TOUCHPAY  (MTN CI, Orange CI, Moov CI)
        // =====================================================================
        if (in_array($network, ['MOMOCI', 'OMCIV2', 'FLOOZ'])) {
            try {
                $login = env('TOUCHPAY_LOGIN_AGENT');
                $pass  = env('TOUCHPAY_PASSWORD_AGENT');

                if (!$login || !$pass) {
                    return $this->handleError('Identifiants TouchPay manquants dans .env', $returnAsJson, $errorLink);
                }

                $tpUrl = "https://api.gutouch.com/dist/api/touchpayapi/v1/RIKAC8213/transaction"
                       . "?loginAgent={$login}&passwordAgent={$pass}";

                $serviceCode = match ($network) {
                    'OMCIV2' => 'PAIEMENTMARCHANDOMPAYCIDIRECT',
                    'MOMOCI' => 'PAIEMENTMARCHAND_MTN_CI',
                    'FLOOZ'  => 'PAIEMENTMARCHAND_MOOV_CI',
                    default  => null,
                };

                $touchData = [
                    'idFromClient'     => $refNumber,
                    'additionnalInfos' => [
                        'recipientEmail'     => $payment->user->email      ?? '',
                        'recipientFirstName' => $payment->user->first_name ?? 'N/A',
                        'recipientLastName'  => $payment->user->last_name  ?? '',
                        'destinataire'       => $request->phone,
                    ],
                    'amount'          => $amount,
                    'callback'        => route('payment.callback', ['service' => 'touchpay']),
                    'recipientNumber' => $request->phone,
                    'serviceCode'     => $serviceCode,
                ];

                if ($network === 'OMCIV2') {
                    $touchData['additionnalInfos']['otp'] = $request->otp;
                }

                Log::channel('payment')->info("TouchPay payload envoyé", $touchData);

                // http_errors => false : on lit TOUJOURS la réponse, même si HTTP 4xx/5xx
                $rawResponse = (new Client())->put($tpUrl, [
                    'headers'     => ['Content-Type' => 'application/json'],
                    'auth'        => [env('TOUCHPAY_API_KEY'), env('TOUCHPAY_API_SECRET'), 'digest'],
                    'json'        => $touchData,
                    'http_errors' => false,
                    'timeout'     => 30,
                ]);

                $respRaw  = $rawResponse->getBody()->getContents();
                $resp     = json_decode($respRaw, true);
                $httpCode = $rawResponse->getStatusCode();

                Log::channel('payment')->info("TouchPay réponse brute", [
                    'http_code' => $httpCode,
                    'response'  => $resp,
                ]);

                $tpStatus = strtoupper($resp['status'] ?? '');

                // Succès immédiat (Orange Money)
                if ($tpStatus === 'SUCCESSFUL') {
                    return $returnAsJson
                        ? ['success' => true, 'redirect_url' => $returnLink]
                        : Redirect::to($returnLink);
                }

                // En attente de confirmation (MTN, Moov → push mobile)
                if (in_array($network, ['MOMOCI', 'FLOOZ']) && in_array($tpStatus, ['INITIATED', 'PENDING'])) {
                    Session::put('payment_reference', $refNumber);
                    return $returnAsJson
                        ? ['success' => true, 'redirect_url' => route('payment.pending')]
                        : Redirect::route('payment.pending');
                }

                // Erreur provider — extraire le message le plus précis possible
                $errorMsg = $resp['message']
                    ?? $resp['detailMessage']
                    ?? $resp['description']
                    ?? $resp['error']
                    ?? null;

                // Si aucun champ message, construire un message à partir du statut
                if (!$errorMsg) {
                    $errorMsg = $tpStatus
                        ? "TouchPay a retourné le statut : {$tpStatus}"
                        : "Réponse TouchPay invalide (HTTP {$httpCode})";
                }

                Log::error("❌ TouchPay échec [{$refNumber}]", [
                    'network'   => $network,
                    'http_code' => $httpCode,
                    'status'    => $tpStatus,
                    'message'   => $errorMsg,
                    'full_resp' => $resp,
                ]);

                return $this->handleError($errorMsg, $returnAsJson, $errorLink);

            } catch (\Exception $e) {
                Log::error("TouchPay exception [{$refNumber}]", ['error' => $e->getMessage()]);
                return $this->handleError('Erreur de connexion TouchPay : ' . $e->getMessage(), $returnAsJson, $errorLink);
            }
        }

        // =====================================================================
        // 3. PAWAPAY
        // =====================================================================
        $pawaNetworks = [
            'MTN_MOMO_CIV','ORANGE_CIV','WAVE_CIV','MTN_MOMO_BEN','MOOV_BEN',
            'MOOV_BFA','ORANGE_BFA','MTN_MOMO_CMR','ORANGE_CMR','FREE_SEN','WAVE_SEN','WAVESN',
            'VODACOM_MPESA_COD','AIRTEL_COD','ORANGE_COD','AIRTEL_GAB',
            'AIRTEL_COG','MTN_MOMO_COG','MPESA_KEN','AIRTEL_MWI','TNM_MWI',
            'AIRTEL_RWA','MTN_MOMO_RWA','ORANGE_SLE','MTN_MOMO_GHA','AIRTELTIGO_GHA',
            'VODAFONE_GHA','AIRTEL_TZA','VODACOM_TZA','TIGO_TZA','HALOTEL_TZA',
            'AIRTEL_OAPI_UGA','MTN_MOMO_UGA','AIRTEL_OAPI_ZMB','MTN_MOMO_ZMB',
            'ZAMTEL_ZMB','AIRTEL_NGA','MTN_MOMO_NGA','VODACOM_MOZ',
        ];

        if (in_array($network, $pawaNetworks)) {
            $token = env('PAWAPAY_API_TOKEN');
            $base  = env('PAWAPAY_BASE_URL', 'https://api.pawapay.io');

            if (!$token) {
                return $this->handleError('Clé API PawaPay manquante dans .env (PAWAPAY_API_TOKEN)', $returnAsJson, $errorLink);
            }

            $cc = $this->getPawaPayCountryConfig($network);
            if (!$cc) {
                return $this->handleError("Réseau PawaPay non reconnu : {$network}", $returnAsJson, $errorLink);
            }

            // Vérification disponibilité
            $providerStatus = $this->checkPawaPayProviderAvailability($cc['country'], $network);
            if ($providerStatus !== 'OPERATIONAL') {
                $msg = "Le provider {$network} est actuellement indisponible"
                     . ($providerStatus ? " (statut : {$providerStatus})" : '') . '.';
                $payment->update(['status' => 'failed']);
                return $this->handleError($msg, $returnAsJson, $errorLink);
            }

            $cfg = $this->getPawaPayProviderConfig($cc['country'], $network);
            if (!$cfg || $cfg['status'] !== 'OPERATIONAL') {
                return $this->handleError("Configuration PawaPay invalide pour {$network}.", $returnAsJson, $errorLink);
            }

            $rawPhone = preg_replace('/[^0-9]/', '', $request->input('phone', ''));
            if (str_starts_with($rawPhone, $cc['countryCode'])) {
                $rawPhone = substr($rawPhone, strlen($cc['countryCode']));
            }

            $depositId = Uuid::uuid4()->toString();
            $payment->update([
                'transaction_id'  => $depositId,
                'payment_details' => array_merge($payment->payment_details ?? [], [
                    'original_ref'       => $refNumber,
                    'pawapay_deposit_id' => $depositId,
                ]),
            ]);

            try {
                $pawaPayload = [
                    'depositId'            => $depositId,
                    'amount'               => (string) $amount,
                    'currency'             => $cc['currency'],
                    'country'              => $cc['country'],
                    'correspondent'        => $network,
                    'payer'                => ['type' => 'MSISDN', 'address' => ['value' => $cc['countryCode'] . $rawPhone]],
                    'customerTimestamp'    => now()->toISOString(),
                    'statementDescription' => Str::limit('Achat ' . substr($refNumber, -10), 22, ''),
                    'notificationUrl'      => $cfg['callbackUrl'] ?? route('payment.callback', ['service' => 'pawapay']),
                ];

                Log::channel('payment')->info("PawaPay payload", $pawaPayload);

                $resp = Http::withToken($token)->timeout(25)->post("{$base}/deposits", $pawaPayload);

                Log::channel('payment')->info("PawaPay réponse", [
                    'status' => $resp->status(),
                    'body'   => $resp->json(),
                ]);

                if ($resp->successful() && ($resp->json()['status'] ?? null) === 'ACCEPTED') {
                    Session::put('payment_reference', $depositId);
                    $msg = 'Veuillez confirmer le paiement sur votre téléphone.';
                    return $returnAsJson
                        ? ['success' => true, 'redirect_url' => route('payment.pending'), 'message' => $msg]
                        : redirect()->route('payment.pending')->with('info', $msg);
                }

                // Extraire l'erreur PawaPay la plus précise
                $pawaError = $resp->json()['rejectionReason']['rejectionMessage']
                    ?? $resp->json()['rejectionReason']['rejectionCode']
                    ?? $resp->json()['message']
                    ?? $resp->json()['error']
                    ?? null;

                if (!$pawaError) {
                    $pawaStatus = $resp->json()['status'] ?? null;
                    $pawaError  = $pawaStatus
                        ? "PawaPay a retourné le statut : {$pawaStatus} (HTTP {$resp->status()})"
                        : "Réponse PawaPay invalide (HTTP {$resp->status()})";
                }

                Log::error("❌ PawaPay échec [{$depositId}]", [
                    'network'   => $network,
                    'http_code' => $resp->status(),
                    'error'     => $pawaError,
                    'full_resp' => $resp->json(),
                ]);

                return $this->handleError($pawaError, $returnAsJson, $errorLink);

            } catch (\Exception $e) {
                Log::error("PawaPay exception [{$depositId}]", ['error' => $e->getMessage()]);
                return $this->handleError('Erreur de connexion PawaPay : ' . $e->getMessage(), $returnAsJson, $errorLink);
            }
        }

        // =====================================================================
        // 4. PAYSTACK  (tous les codes commençant par PS_)
        // =====================================================================
        if (str_starts_with($network, 'PS_')) {
            try {
                $secretKey = env('PAYSTACK_SECRET_KEY');
                if (!$secretKey) {
                    return $this->handleError('Clé API Paystack manquante dans .env (PAYSTACK_SECRET_KEY)', $returnAsJson, $errorLink);
                }

                $userPhone    = $payment->user->phone ?? $request->phone ?? '';
                $countryIso   = $this->detectCountryFromPhone($userPhone);
                $currency     = $this->countryConfigs[$countryIso]['currency'] ?? 'XOF';
                $amountFinal  = in_array($currency, $this->paystackKoboCurrencies) ? $amount * 100 : $amount;

                $channelMap = [
                    'PS_CARD'   => 'card',
                    'PS_MTN'    => 'mobile_money',
                    'PS_ORANGE' => 'mobile_money',
                    'PS_WAVE'   => 'mobile_money',
                    'PS_MPESA'  => 'mobile_money',
                    'PS_ATL'    => 'mobile_money',
                    'PS_VOD'    => 'mobile_money',
                ];

                $payload = [
                    'email'        => $payment->user->email ?? $request->email ?? 'no-reply@example.com',
                    'amount'       => (int) $amountFinal,
                    'currency'     => $currency,
                    'reference'    => $refNumber,
                    'callback_url' => route('payment.callback', ['service' => 'paystack']),
                    'channels'     => [$channelMap[$network] ?? 'card'],
                    'metadata'     => [
                        'payment_id'    => $payment->id,
                        'payment_type'  => $payment->payment_type,
                        'cancel_action' => $errorLink,
                    ],
                ];

                if ($request->phone) {
                    $payload['phone'] = $request->phone;
                }

                Log::channel('payment')->info("Paystack payload", $payload);

                $resp = Http::withToken($secretKey)
                    ->post('https://api.paystack.co/transaction/initialize', $payload);

                Log::channel('payment')->info("Paystack réponse", [
                    'status' => $resp->status(),
                    'body'   => $resp->json(),
                ]);

                if ($resp->successful() && ($resp->json()['status'] ?? false) === true) {
                    $url = $resp->json()['data']['authorization_url'];
                    return $returnAsJson ? ['success' => true, 'redirect_url' => $url] : Redirect::away($url);
                }

                // Message d'erreur Paystack
                $psError = $resp->json()['message']
                    ?? $resp->json()['error']
                    ?? "Erreur Paystack (HTTP {$resp->status()})";

                Log::error("❌ Paystack échec [{$refNumber}]", [
                    'network'   => $network,
                    'http_code' => $resp->status(),
                    'error'     => $psError,
                    'full_resp' => $resp->json(),
                ]);

                return $this->handleError($psError, $returnAsJson, $errorLink);

            } catch (\Exception $e) {
                Log::error("Paystack exception [{$refNumber}]", ['error' => $e->getMessage()]);
                return $this->handleError('Erreur de connexion Paystack : ' . $e->getMessage(), $returnAsJson, $errorLink);
            }
        }

        // =====================================================================
        // 5. PAIEMENTPRO  (Carte + Mobile Money Afrique Ouest/Centre)
        // =====================================================================
        $ppNetworks = [
            'CARD', 'MOMOBJ', 'FLOOZBJ', 'OMBF', 'FLOOZ_BFA',
            'OMCM', 'MOMOCM', 'OMCIV2', 'MOMOCI', 'FLOOZ',
            'OMGN', 'OMML', 'AIRTELNG', 'OMSN', 'WAVESN', 'MOOTG', 'TOGOCEL',
        ];

        if (in_array($network, $ppNetworks)) {
            try {
                if ($network === 'CARD') {
                    $amount = ($amount * 1.05) + 780;
                }

                $userPhone   = $payment->user->phone ?? $request->phone ?? '';
                $currency    = $this->countryConfigs[$this->detectCountryFromPhone($userPhone)]['currency'] ?? 'XOF';
                $currCode    = $this->currencyToPaiementProCode[$currency] ?? '952';

                $payload = [
                    'merchantId'          => env('PAIEMENTPRO_MERCHANT_ID', 'PP-F1278'),
                    'countryCurrencyCode' => $currCode,
                    'amount'              => $amount,
                    'channel'             => $network,
                    'customerEmail'       => $payment->user->email      ?? $request->email     ?? 'no-reply@example.com',
                    'customerFirstName'   => $payment->user->first_name ?? $request->firstname ?? 'N/A',
                    'customerLastname'    => $payment->user->last_name  ?? $request->lastname  ?? '',
                    'referenceNumber'     => $refNumber,
                    'notificationURL'     => route('payment.callback', ['service' => 'paiementpro']),
                    'returnURL'           => route('payment.callback', ['service' => 'paiementpro']),
                    'description'         => 'Achat sur ' . config('app.name'),
                ];

                if ($network !== 'CARD') {
                    $payload['customerPhoneNumber'] = $request->phone;
                }

                Log::channel('payment')->info("PaiementPro payload", $payload);

                $ctx  = stream_context_create([
                    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true],
                ]);
                $soap = new SoapClient(
                    'https://www.paiementpro.net/webservice/OnlineServicePayment_v2.php?wsdl',
                    ['stream_context' => $ctx]
                );
                $resp = $soap->initTransact($payload);

                Log::channel('payment')->info("PaiementPro réponse brute", (array) $resp);

                if (isset($resp->Sessionid) && $resp->Description === 'SUCCESS') {
                    $payment->update([
                        'payment_details' => array_merge($payment->payment_details ?? [], [
                            'session_id' => $resp->Sessionid,
                        ]),
                    ]);
                    $url = "https://www.paiementpro.net/webservice/onlinepayment/processing_v2.php?sessionid={$resp->Sessionid}";
                    return $returnAsJson ? ['success' => true, 'redirect_url' => $url] : Redirect::away($url);
                }

                // Message d'erreur PaiementPro
                $ppError = $resp->Description
                    ?? $resp->message
                    ?? $resp->Message
                    ?? 'Erreur inconnue PaiementPro';

                // Enrichir si la description est trop générique
                if (in_array(strtolower($ppError), ['error', 'failed', 'failure', ''])) {
                    $ppError = "PaiementPro a refusé la transaction (réseau : {$network})";
                }

                Log::error("❌ PaiementPro échec [{$refNumber}]", [
                    'network'   => $network,
                    'error'     => $ppError,
                    'full_resp' => (array) $resp,
                ]);

                return $this->handleError($ppError, $returnAsJson, $errorLink);

            } catch (\SoapFault $e) {
                // SoapFault contient souvent le message du serveur dans faultstring
                $soapMsg = $e->faultstring ?? $e->getMessage();
                Log::error("PaiementPro SoapFault [{$refNumber}]", ['fault' => $soapMsg]);
                return $this->handleError("PaiementPro (SOAP) : {$soapMsg}", $returnAsJson, $errorLink);

            } catch (\Exception $e) {
                Log::error("PaiementPro exception [{$refNumber}]", ['error' => $e->getMessage()]);
                return $this->handleError('Erreur de connexion PaiementPro : ' . $e->getMessage(), $returnAsJson, $errorLink);
            }
        }

        return $this->handleError("Mode de paiement non supporté : {$network}", $returnAsJson, $errorLink);
    }

    // =========================================================================
    // FINALISATION
    // =========================================================================

    public function finalizePurchase(Payment $payment): void
    {
        $ns  = app(\App\Services\NotificationService::class);
        $user = $payment->user;

        if (in_array($payment->payment_type,['book_purchase','book_pdf','book_audio'])) {
            \App\Models\Purchase::updateOrCreate(['payment_id'=>$payment->id],[
                'user_id'=>$payment->user_id,'book_id'=>$payment->book_id,
                'purchase_type'=>$payment->payment_details['purchase_type']??($payment->payment_type==='book_audio'?'audio':'pdf'),
                'price'=>$payment->amount,'is_active'=>true,
            ]);
            app(\App\Services\RevenueCalculatorService::class)->recordRevenue($payment);
            $book=$payment->book;
            $ns->sendNotification($user,__('Achat confirmé'),__('Votre achat pour le livre ":title" a été validé.',['title'=>$book->title]),route('book.show',$book->slug),'success');
        } elseif ($payment->payment_type === 'subscription') {
            $sub=$payment->subscription??Subscription::find($payment->subscription_id);
            if ($sub) {
                $plan=$sub->subscriptionPlan;
                $sub->update([
                    'status' => 'active',
                    'start_date' => $sub->start_date ?? now(),
                    'end_date' => $sub->end_date ?? now()->addDays($plan->duration_days ?? 30)
                ]);
                $ns->sendNotification($user,__('Abonnement activé'),__('Votre abonnement ":plan" est actif.',['plan'=>$plan->name]),route('subscription.index'),'success');
            }
        }
    }

    private function handleError(string $msg, bool $json, string $errorLink)
    {
        Log::warning("PaymentService error: {$msg}");
        return $json ? ['success'=>false,'error'=>$msg] : Redirect::to($errorLink)->with('danger',$msg);
    }
}