<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Setting;
use App\Models\Book;
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
        'XOF' => '952',
        'EUR' => '978',
        'CDF' => '976',
        'XAF' => '950',
        'KES' => '404',
        'MWK' => '454',
        'RWF' => '646',
        'SLL' => '694',
        'GHS' => '936',
        'TZS' => '834',
        'UGX' => '800',
        'ZMW' => '967',
        'NGN' => '566',
        'MAD' => '504',
        'MZN' => '943',
    ];

    public function __construct()
    {
        $this->initializeConfigurations();
    }

    /**
     * Get all possible configurations for the admin panel.
     */
    public function getGlobalConfigurations(): array
    {
        return [
            'countries' => $this->countryConfigs,
            'methods'   => $this->supportedMethods,
        ];
    }

    private function initializeConfigurations(): void
    {
        $this->countryConfigs = [
            'CI' => ['currency' => 'XOF', 'code' => '225', 'iso' => 'CI'],
            'BJ' => ['currency' => 'XOF', 'code' => '229', 'iso' => 'BJ'],
            'BF' => ['currency' => 'XOF', 'code' => '226', 'iso' => 'BF'],
            'CM' => ['currency' => 'XAF', 'code' => '237', 'iso' => 'CM'],
            'GW' => ['currency' => 'XOF', 'code' => '245', 'iso' => 'GW'],
            'ML' => ['currency' => 'XOF', 'code' => '223', 'iso' => 'ML'],
            'NE' => ['currency' => 'XOF', 'code' => '227', 'iso' => 'NE'],
            'SN' => ['currency' => 'XOF', 'code' => '221', 'iso' => 'SN'],
            'CD' => ['currency' => 'CDF', 'code' => '243', 'iso' => 'CD'],
            'GA' => ['currency' => 'XAF', 'code' => '241', 'iso' => 'GA'],
            'CG' => ['currency' => 'XAF', 'code' => '242', 'iso' => 'CG'],
            'KE' => ['currency' => 'KES', 'code' => '254', 'iso' => 'KE'],
            'MW' => ['currency' => 'MWK', 'code' => '265', 'iso' => 'MW'],
            'RW' => ['currency' => 'RWF', 'code' => '250', 'iso' => 'RW'],
            'SL' => ['currency' => 'SLL', 'code' => '232', 'iso' => 'SL'],
            'GH' => ['currency' => 'GHS', 'code' => '233', 'iso' => 'GH'],
            'TZ' => ['currency' => 'TZS', 'code' => '255', 'iso' => 'TZ'],
            'UG' => ['currency' => 'UGX', 'code' => '256', 'iso' => 'UG'],
            'ZM' => ['currency' => 'ZMW', 'code' => '260', 'iso' => 'ZM'],
            'NG' => ['currency' => 'NGN', 'code' => '234', 'iso' => 'NG'],
            'MA' => ['currency' => 'MAD', 'code' => '212', 'iso' => 'MA'],
            'FR' => ['currency' => 'EUR', 'code' => '33',  'iso' => 'FR'],
            'TG' => ['currency' => 'XOF', 'code' => '228', 'iso' => 'TG'],
            'MZ' => ['currency' => 'MZN', 'code' => '258', 'iso' => 'MZ'],
        ];

        $this->supportedMethods = [
            // Côte d'Ivoire – TouchPay / PaiementPro
            'MOMOCI'        => ['name' => 'MTN CI',        'icon_color' => '#FFCC00', 'countries' => ['CI']],
            'OMCIV2'        => ['name' => 'Orange Money CI','icon_color' => '#FF6600', 'countries' => ['CI', 'FR']],
            'FLOOZ'         => ['name' => 'Moov CI',       'icon_color' => '#0066FF', 'countries' => ['CI']],
            'WAVECI'        => ['name' => 'Wave CI',       'icon_color' => '#00CC99', 'countries' => ['CI', 'FR']],
            // Côte d'Ivoire – PawaPay
            'MTN_MOMO_CIV'  => ['name' => 'MTN (PawaPay)', 'icon_color' => '#FFCC00', 'countries' => ['CI']],
            'ORANGE_CIV'    => ['name' => 'Orange (PawaPay)','icon_color' => '#FF6600','countries' => ['CI', 'FR']],
            'WAVE_CIV'      => ['name' => 'Wave (PawaPay)', 'icon_color' => '#00CC99', 'countries' => ['CI', 'FR']],
            // Bénin
            'MOMOBJ'        => ['name' => 'MTN Bénin',     'icon_color' => '#FFCC00', 'countries' => ['BJ']],
            'FLOOZBJ'       => ['name' => 'Moov Bénin',    'icon_color' => '#0066FF', 'countries' => ['BJ']],
            'MTN_MOMO_BEN'  => ['name' => 'MTN Bénin (PawaPay)', 'icon_color' => '#FFCC00', 'countries' => ['BJ']],
            'MOOV_BEN'      => ['name' => 'Moov Bénin (PawaPay)', 'icon_color' => '#0066FF', 'countries' => ['BJ']],
            // Burkina Faso
            'OMBF'          => ['name' => 'Orange BF',     'icon_color' => '#FF6600', 'countries' => ['BF']],
            'FLOOZ_BFA'     => ['name' => 'Moov BF',       'icon_color' => '#0066FF', 'countries' => ['BF']],
            'MOOV_BFA'      => ['name' => 'Moov BF (PawaPay)', 'icon_color' => '#0066FF', 'countries' => ['BF']],
            'ORANGE_BFA'    => ['name' => 'Orange BF (PawaPay)', 'icon_color' => '#FF6600', 'countries' => ['BF']],
            // Cameroun
            'OMCM'          => ['name' => 'Orange CM',     'icon_color' => '#FF6600', 'countries' => ['CM']],
            'MOMOCM'        => ['name' => 'MTN CM',        'icon_color' => '#FFCC00', 'countries' => ['CM']],
            'MTN_MOMO_CMR'  => ['name' => 'MTN CM (PawaPay)', 'icon_color' => '#FFCC00', 'countries' => ['CM']],
            'ORANGE_CMR'    => ['name' => 'Orange CM (PawaPay)', 'icon_color' => '#FF6600', 'countries' => ['CM']],
            // Sénégal
            'OMSN'          => ['name' => 'Orange SN',     'icon_color' => '#FF6600', 'countries' => ['SN']],
            'WAVESN'        => ['name' => 'Wave SN',       'icon_color' => '#00CC99', 'countries' => ['SN']],
            'FREE_SEN'      => ['name' => 'Free SN (PawaPay)', 'icon_color' => '#E40000', 'countries' => ['SN']],
            'WAVE_SEN'      => ['name' => 'Wave SN (PawaPay)', 'icon_color' => '#00CC99', 'countries' => ['SN']],
            // Togo
            'MOOTG'         => ['name' => 'Flooz Togo',   'icon_color' => '#0066FF', 'countries' => ['TG']],
            'TOGOCEL'       => ['name' => 'Togo Cel',      'icon_color' => '#FFD700', 'countries' => ['TG']],
            // Guinée Bissau
            'OMGN'          => ['name' => 'Orange GN',     'icon_color' => '#FF6600', 'countries' => ['GW']],
            // Mali
            'OMML'          => ['name' => 'Orange ML',     'icon_color' => '#FF6600', 'countries' => ['ML']],
            // Niger
            'AIRTELNG'      => ['name' => 'Airtel NE',     'icon_color' => '#E40000', 'countries' => ['NE']],
            // RDC Congo
            'VODACOM_MPESA_COD' => ['name' => 'Vodacom MPesa', 'icon_color' => '#008000', 'countries' => ['CD']],
            'AIRTEL_COD'    => ['name' => 'Airtel COD',    'icon_color' => '#E40000', 'countries' => ['CD']],
            'ORANGE_COD'    => ['name' => 'Orange COD',    'icon_color' => '#FF6600', 'countries' => ['CD']],
            // Gabon
            'AIRTEL_GAB'    => ['name' => 'Airtel Gabon',  'icon_color' => '#E40000', 'countries' => ['GA']],
            // Congo Brazzaville
            'AIRTEL_COG'    => ['name' => 'Airtel Congo',  'icon_color' => '#E40000', 'countries' => ['CG']],
            'MTN_MOMO_COG'  => ['name' => 'MTN Congo',     'icon_color' => '#FFCC00', 'countries' => ['CG']],
            // Kenya
            'MPESA_KEN'     => ['name' => 'MPesa Kenya',   'icon_color' => '#34B233', 'countries' => ['KE']],
            // Malawi
            'AIRTEL_MWI'    => ['name' => 'Airtel Malawi', 'icon_color' => '#E40000', 'countries' => ['MW']],
            'TNM_MWI'       => ['name' => 'TNM Malawi',    'icon_color' => '#006400', 'countries' => ['MW']],
            // Rwanda
            'AIRTEL_RWA'    => ['name' => 'Airtel Rwanda', 'icon_color' => '#E40000', 'countries' => ['RW']],
            'MTN_MOMO_RWA'  => ['name' => 'MTN Rwanda',    'icon_color' => '#FFCC00', 'countries' => ['RW']],
            // Sierra Leone
            'ORANGE_SLE'    => ['name' => 'Orange SL',     'icon_color' => '#FF6600', 'countries' => ['SL']],
            // Ghana
            'MTN_MOMO_GHA'  => ['name' => 'MTN Ghana',     'icon_color' => '#FFCC00', 'countries' => ['GH']],
            'AIRTELTIGO_GHA'=> ['name' => 'AirtelTigo GH', 'icon_color' => '#8A2BE2', 'countries' => ['GH']],
            'VODAFONE_GHA'  => ['name' => 'Vodafone GH',   'icon_color' => '#E60000', 'countries' => ['GH']],
            // Tanzanie
            'AIRTEL_TZA'    => ['name' => 'Airtel TZ',     'icon_color' => '#E40000', 'countries' => ['TZ']],
            'VODACOM_TZA'   => ['name' => 'Vodacom TZ',    'icon_color' => '#008000', 'countries' => ['TZ']],
            'TIGO_TZA'      => ['name' => 'Tigo TZ',       'icon_color' => '#0000FF', 'countries' => ['TZ']],
            'HALOTEL_TZA'   => ['name' => 'Halotel TZ',    'icon_color' => '#FF8C00', 'countries' => ['TZ']],
            // Ouganda
            'AIRTEL_OAPI_UGA' => ['name' => 'Airtel UG',  'icon_color' => '#E40000', 'countries' => ['UG']],
            'MTN_MOMO_UGA'  => ['name' => 'MTN Uganda',    'icon_color' => '#FFCC00', 'countries' => ['UG']],
            // Zambie
            'AIRTEL_OAPI_ZMB' => ['name' => 'Airtel ZM',  'icon_color' => '#E40000', 'countries' => ['ZM']],
            'MTN_MOMO_ZMB'  => ['name' => 'MTN Zambia',    'icon_color' => '#FFCC00', 'countries' => ['ZM']],
            'ZAMTEL_ZMB'    => ['name' => 'Zamtel',        'icon_color' => '#228B22', 'countries' => ['ZM']],
            // Nigéria
            'AIRTEL_NGA'    => ['name' => 'Airtel NG',     'icon_color' => '#E40000', 'countries' => ['NG']],
            'MTN_MOMO_NGA'  => ['name' => 'MTN Nigeria',   'icon_color' => '#FFCC00', 'countries' => ['NG']],
            // Mozambique
            'VODACOM_MOZ'   => ['name' => 'Vodacom MOZ',   'icon_color' => '#008000', 'countries' => ['MZ']],
            // Carte bancaire (tous pays)
            'CARD'          => ['name' => 'Visa / Mastercard', 'icon_color' => '#1a1f71', 'countries' => array_keys($this->countryConfigs ?? [])],
        ];
    }

    // -------------------------------------------------------------------------
    // Méthodes de paiement disponibles
    // -------------------------------------------------------------------------

    public function getAvailablePaymentMethods(string $phone = null, string $countryIso = null): array
    {
        $countryCode = strtoupper($countryIso ?? ($phone ? $this->detectCountryFromPhone($phone) : 'CI'));

        $availableMethods = [];

        // Méthodes activées en base de données (optionnel)
        $dbSettings    = Setting::where('key', 'payment_methods')->first();
        $enabledMethods = $dbSettings ? json_decode($dbSettings->value, true) : null;

        foreach ($this->supportedMethods as $methodCode => $details) {
            if ($enabledMethods) {
                if (isset($enabledMethods[$countryCode][$methodCode]) && $enabledMethods[$countryCode][$methodCode] === 'on') {
                    $availableMethods[] = ['id' => $methodCode, 'name' => $details['name'], 'icon_color' => $details['icon_color']];
                }
            } else {
                if (in_array($countryCode, $details['countries'])) {
                    $availableMethods[] = ['id' => $methodCode, 'name' => $details['name'], 'icon_color' => $details['icon_color']];
                }
            }
        }

        // Fallback : au moins la carte
        if (empty($availableMethods)) {
            $availableMethods[] = ['id' => 'CARD', 'name' => 'Visa / Mastercard', 'icon_color' => '#1a1f71'];
        }

        return [
            'country'  => $countryCode,
            'currency' => $this->countryConfigs[$countryCode]['currency'] ?? 'XOF',
            'methods'  => $availableMethods,
        ];
    }

    private function detectCountryFromPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        foreach ($this->countryConfigs as $iso => $config) {
            if (str_starts_with($phone, $config['code'])) {
                return $iso;
            }
        }
        return 'CI';
    }

    // -------------------------------------------------------------------------
    // PawaPay – vérification disponibilité provider
    // -------------------------------------------------------------------------

    private function checkPawaPayProviderAvailability(string $country, string $provider, string $operationType = 'DEPOSIT'): ?string
    {
        $apiToken = env('PAWAPAY_API_TOKEN');
        $baseUrl  = env('PAWAPAY_BASE_URL', 'https://api.pawapay.io');

        try {
            $response = Http::withToken($apiToken)->timeout(10)
                ->get("{$baseUrl}/v2/availability", ['country' => $country, 'operationType' => $operationType]);

            if ($response->successful()) {
                foreach ($response->json() as $countryData) {
                    if ($countryData['country'] === $country) {
                        foreach ($countryData['providers'] as $providerData) {
                            if ($providerData['provider'] === $provider) {
                                foreach ($providerData['operationTypes'] as $opType => $status) {
                                    if ($opType === $operationType) {
                                        return $status;
                                    }
                                }
                            }
                        }
                    }
                }
                return null;
            }
            Log::error("PawaPay Availability Error: {$response->status()} – {$response->body()}");
            return null;
        } catch (\Exception $e) {
            Log::error("PawaPay Availability Exception: {$e->getMessage()}");
            return null;
        }
    }

    // -------------------------------------------------------------------------
    // PawaPay – configuration active du provider
    // -------------------------------------------------------------------------

    private function getPawaPayProviderConfig(string $country, string $provider, string $operationType = 'DEPOSIT'): ?array
    {
        $apiToken = env('PAWAPAY_API_TOKEN');
        $baseUrl  = env('PAWAPAY_BASE_URL', 'https://api.pawapay.io');

        try {
            $response = Http::withToken($apiToken)->timeout(10)
                ->get("{$baseUrl}/v2/active-conf", ['country' => $country, 'operationType' => $operationType]);

            if ($response->successful()) {
                foreach ($response->json()['countries'] ?? [] as $countryData) {
                    if ($countryData['country'] === $country) {
                        foreach ($countryData['providers'] as $providerData) {
                            if ($providerData['provider'] === $provider) {
                                foreach ($providerData['currencies'] as $currencyData) {
                                    foreach ($currencyData['operationTypes'] as $opType => $opData) {
                                        if ($opType === $operationType) {
                                            return [
                                                'currency'            => $currencyData['currency'],
                                                'minTransactionLimit' => $opData['minAmount'] ?? null,
                                                'maxTransactionLimit' => $opData['maxAmount'] ?? null,
                                                'status'              => $opData['status'] ?? 'UNKNOWN',
                                                'callbackUrl'         => $opData['callbackUrl'] ?? null,
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                return null;
            }
            Log::error("PawaPay Config Error: {$response->status()} – {$response->body()}");
            return null;
        } catch (\Exception $e) {
            Log::error("PawaPay Config Exception: {$e->getMessage()}");
            return null;
        }
    }

    // -------------------------------------------------------------------------
    // PawaPay – mapping réseau → pays / devise / indicatif
    // -------------------------------------------------------------------------

    private function getPawaPayCountryConfig(?string $network): ?array
    {
        $map = [
            // Côte d'Ivoire
            'MTN_MOMO_CIV'    => ['country' => 'CIV', 'currency' => 'XOF', 'countryCode' => '225'],
            'ORANGE_CIV'      => ['country' => 'CIV', 'currency' => 'XOF', 'countryCode' => '225'],
            'WAVE_CIV'        => ['country' => 'CIV', 'currency' => 'XOF', 'countryCode' => '225'],
            // Bénin
            'MTN_MOMO_BEN'    => ['country' => 'BEN', 'currency' => 'XOF', 'countryCode' => '229'],
            'MOOV_BEN'        => ['country' => 'BEN', 'currency' => 'XOF', 'countryCode' => '229'],
            // Burkina Faso
            'FLOOZ_BFA'       => ['country' => 'BFA', 'currency' => 'XOF', 'countryCode' => '226'],
            'MOOV_BFA'        => ['country' => 'BFA', 'currency' => 'XOF', 'countryCode' => '226'],
            'ORANGE_BFA'      => ['country' => 'BFA', 'currency' => 'XOF', 'countryCode' => '226'],
            // Cameroun
            'MTN_MOMO_CMR'    => ['country' => 'CMR', 'currency' => 'XAF', 'countryCode' => '237'],
            'ORANGE_CMR'      => ['country' => 'CMR', 'currency' => 'XAF', 'countryCode' => '237'],
            // Sénégal
            'FREE_SEN'        => ['country' => 'SEN', 'currency' => 'XOF', 'countryCode' => '221'],
            'WAVE_SEN'        => ['country' => 'SEN', 'currency' => 'XOF', 'countryCode' => '221'],
            'WAVESN'          => ['country' => 'SEN', 'currency' => 'XOF', 'countryCode' => '221'],
            // RDC Congo
            'VODACOM_MPESA_COD' => ['country' => 'COD', 'currency' => 'CDF', 'countryCode' => '243'],
            'AIRTEL_COD'      => ['country' => 'COD', 'currency' => 'CDF', 'countryCode' => '243'],
            'ORANGE_COD'      => ['country' => 'COD', 'currency' => 'CDF', 'countryCode' => '243'],
            // Gabon
            'AIRTEL_GAB'      => ['country' => 'GAB', 'currency' => 'XAF', 'countryCode' => '241'],
            // Congo Brazzaville
            'AIRTEL_COG'      => ['country' => 'COG', 'currency' => 'XAF', 'countryCode' => '242'],
            'MTN_MOMO_COG'    => ['country' => 'COG', 'currency' => 'XAF', 'countryCode' => '242'],
            // Kenya
            'MPESA_KEN'       => ['country' => 'KEN', 'currency' => 'KES', 'countryCode' => '254'],
            // Malawi
            'AIRTEL_MWI'      => ['country' => 'MWI', 'currency' => 'MWK', 'countryCode' => '265'],
            'TNM_MWI'         => ['country' => 'MWI', 'currency' => 'MWK', 'countryCode' => '265'],
            // Rwanda
            'AIRTEL_RWA'      => ['country' => 'RWA', 'currency' => 'RWF', 'countryCode' => '250'],
            'MTN_MOMO_RWA'    => ['country' => 'RWA', 'currency' => 'RWF', 'countryCode' => '250'],
            // Sierra Leone
            'ORANGE_SLE'      => ['country' => 'SLE', 'currency' => 'SLE', 'countryCode' => '232'],
            // Ghana
            'MTN_MOMO_GHA'    => ['country' => 'GHA', 'currency' => 'GHS', 'countryCode' => '233'],
            'AIRTELTIGO_GHA'  => ['country' => 'GHA', 'currency' => 'GHS', 'countryCode' => '233'],
            'VODAFONE_GHA'    => ['country' => 'GHA', 'currency' => 'GHS', 'countryCode' => '233'],
            // Tanzanie
            'AIRTEL_TZA'      => ['country' => 'TZA', 'currency' => 'TZS', 'countryCode' => '255'],
            'VODACOM_TZA'     => ['country' => 'TZA', 'currency' => 'TZS', 'countryCode' => '255'],
            'TIGO_TZA'        => ['country' => 'TZA', 'currency' => 'TZS', 'countryCode' => '255'],
            'HALOTEL_TZA'     => ['country' => 'TZA', 'currency' => 'TZS', 'countryCode' => '255'],
            // Ouganda
            'AIRTEL_OAPI_UGA' => ['country' => 'UGA', 'currency' => 'UGX', 'countryCode' => '256'],
            'MTN_MOMO_UGA'    => ['country' => 'UGA', 'currency' => 'UGX', 'countryCode' => '256'],
            // Zambie
            'AIRTEL_OAPI_ZMB' => ['country' => 'ZMB', 'currency' => 'ZMW', 'countryCode' => '260'],
            'MTN_MOMO_ZMB'    => ['country' => 'ZMB', 'currency' => 'ZMW', 'countryCode' => '260'],
            'ZAMTEL_ZMB'      => ['country' => 'ZMB', 'currency' => 'ZMW', 'countryCode' => '260'],
            // Nigéria
            'AIRTEL_NGA'      => ['country' => 'NGA', 'currency' => 'NGN', 'countryCode' => '234'],
            'MTN_MOMO_NGA'    => ['country' => 'NGA', 'currency' => 'NGN', 'countryCode' => '234'],
            // Mozambique
            'VODACOM_MOZ'     => ['country' => 'MOZ', 'currency' => 'MZN', 'countryCode' => '258'],
        ];

        return $map[$network] ?? null;
    }

    // =========================================================================
    // POINT D'ENTRÉE PRINCIPAL
    // =========================================================================

    /**
     * Initiate a payment for a given Payment model.
     *
     * @param  Request  $request
     * @param  Payment  $payment   Modèle Payment avec les champs : transaction_id, amount, user
     * @param  bool     $returnAsJson   Retourner un tableau JSON au lieu d'une redirection
     * @param  string|null $successUrl  URL de retour après succès
     */
    public function initiatePayment(Request $request, Payment $payment, bool $returnAsJson = false, ?string $successUrl = null)
    {
        $network    = $request->input('network', 'CARD');
        $amount     = $payment->amount;
        $refNumber  = $payment->transaction_id;

        $returnLink = route('payment.success');
        $errorLink  = route('payment.failed');

        Session::put('payment_return_url', $successUrl ?? route('home'));

        Log::channel('payment')->info("--- INITIATION PAIEMENT ---", [
            'reference'      => $refNumber,
            'network'        => $network,
            'amount'         => $amount,
            'return_as_json' => $returnAsJson,
        ]);

        // =====================================================================
        // 1. WAVE
        // =====================================================================
        if ($network === 'WAVECI') {
            try {
                $response = Http::withToken(env('WAVE_API_KEY'))
                    ->post("https://api.wave.com/v1/checkout/sessions", [
                        "amount"                  => (string) $amount,
                        "currency"                => "XOF",
                        "aggregated_merchant_id"  => env('WAVE_AGGREGATED_MERCHANT_ID'),
                        "error_url"               => $errorLink,
                        "success_url"             => $returnLink,
                    ]);

                if ($response->successful() && isset($response->json()['wave_launch_url'])) {
                    $waveId = $response->json()['id'];
                    $payment->update([
                        'payment_details' => array_merge($payment->payment_details ?? [], ['wave_id' => $waveId]),
                    ]);
                    $redirectUrl = $response->json()['wave_launch_url'];
                    return $returnAsJson
                        ? ['success' => true, 'redirect_url' => $redirectUrl]
                        : Redirect::away($redirectUrl);
                }
                throw new \Exception('Erreur Wave API');
            } catch (\Exception $e) {
                Log::error("Wave Error for Ref {$refNumber}: " . $e->getMessage());
                return $this->handleError($e->getMessage(), $returnAsJson, $errorLink);
            }
        }

        // =====================================================================
        // 2. TOUCHPAY (MTN CI, Orange Money CI, Moov CI)
        // =====================================================================
        if (in_array($network, ['MOMOCI', 'OMCIV2', 'FLOOZ'])) {
            try {
                $loginAgent    = env('TOUCHPAY_LOGIN_AGENT');
                $passwordAgent = env('TOUCHPAY_PASSWORD_AGENT');

                if (empty($loginAgent) || empty($passwordAgent)) {
                    throw new \Exception("Variables d'environnement TouchPay manquantes.");
                }

                $touchPayUrl = "https://api.gutouch.com/dist/api/touchpayapi/v1/RIKAC8213/transaction"
                    . "?loginAgent={$loginAgent}&passwordAgent={$passwordAgent}";

                $serviceCode = match ($network) {
                    'OMCIV2'  => 'PAIEMENTMARCHANDOMPAYCIDIRECT',
                    'MOMOCI'  => 'PAIEMENTMARCHAND_MTN_CI',
                    'FLOOZ'   => 'PAIEMENTMARCHAND_MOOV_CI',
                    default   => null,
                };

                $touchData = [
                    "idFromClient"    => $refNumber,
                    "additionnalInfos" => [
                        "recipientEmail"     => $payment->user->email ?? $request->email ?? '',
                        "recipientFirstName" => $payment->user->first_name ?? $request->firstname ?? 'N/A',
                        "recipientLastName"  => $payment->user->last_name  ?? $request->lastname  ?? '',
                        "destinataire"       => $request->phone,
                    ],
                    "amount"          => $amount,
                    "callback"        => route('payment.callback', ['service' => 'touchpay']),
                    "recipientNumber" => $request->phone,
                    "serviceCode"     => $serviceCode,
                ];

                if ($network === 'OMCIV2') {
                    $touchData['additionnalInfos']['otp'] = $request->otp;
                }

                $client   = new Client();
                $response = $client->put($touchPayUrl, [
                    'headers' => ['Content-Type' => 'application/json'],
                    'auth'    => [env('TOUCHPAY_API_KEY'), env('TOUCHPAY_API_SECRET'), 'digest'],
                    'json'    => $touchData,
                ]);

                $resp = json_decode($response->getBody()->getContents(), true);

                if (in_array($network, ['MOMOCI', 'FLOOZ'])) {
                    if (in_array(strtoupper($resp['status'] ?? ''), ['INITIATED', 'PENDING'])) {
                        Session::put('payment_reference', $refNumber);
                        return $returnAsJson
                            ? ['success' => true, 'redirect_url' => route('payment.pending')]
                            : Redirect::route('payment.pending');
                    }
                }

                if (strtoupper($resp['status'] ?? '') === 'SUCCESSFUL') {
                    return $returnAsJson
                        ? ['success' => true, 'redirect_url' => $returnLink]
                        : Redirect::to($returnLink);
                }

                throw new \Exception($resp['message'] ?? $resp['detailMessage'] ?? 'Échec TouchPay');
            } catch (\Exception $e) {
                Log::error("TouchPay Error for Ref {$refNumber}: " . $e->getMessage());
                return $this->handleError($e->getMessage(), $returnAsJson, $errorLink);
            }
        }

        // =====================================================================
        // 3. PAWAPAY
        // =====================================================================
        $pawaPayNetworks = [
            'MTN_MOMO_CIV', 'ORANGE_CIV', 'WAVE_CIV',
            'MTN_MOMO_BEN', 'MOOV_BEN',
            'FLOOZ_BFA', 'MOOV_BFA', 'ORANGE_BFA',
            'MTN_MOMO_CMR', 'ORANGE_CMR',
            'FREE_SEN', 'WAVE_SEN', 'WAVESN',
            'VODACOM_MPESA_COD', 'AIRTEL_COD', 'ORANGE_COD',
            'AIRTEL_GAB',
            'AIRTEL_COG', 'MTN_MOMO_COG',
            'MPESA_KEN',
            'AIRTEL_MWI', 'TNM_MWI',
            'AIRTEL_RWA', 'MTN_MOMO_RWA',
            'ORANGE_SLE',
            'MTN_MOMO_GHA', 'AIRTELTIGO_GHA', 'VODAFONE_GHA',
            'AIRTEL_TZA', 'VODACOM_TZA', 'TIGO_TZA', 'HALOTEL_TZA',
            'AIRTEL_OAPI_UGA', 'MTN_MOMO_UGA',
            'AIRTEL_OAPI_ZMB', 'MTN_MOMO_ZMB', 'ZAMTEL_ZMB',
            'AIRTEL_NGA', 'MTN_MOMO_NGA',
            'VODACOM_MOZ',
        ];

        if (in_array($network, $pawaPayNetworks)) {
            $apiToken = env('PAWAPAY_API_TOKEN');
            $baseUrl  = env('PAWAPAY_BASE_URL', 'https://api.pawapay.io');

            if (!$apiToken) {
                return $this->handleError('Configuration PawaPay manquante.', $returnAsJson, $errorLink);
            }

            $countryConfig = $this->getPawaPayCountryConfig($network);
            if (!$countryConfig) {
                return $this->handleError('Réseau PawaPay invalide.', $returnAsJson, $errorLink);
            }

            // Vérification disponibilité provider
            $providerStatus = $this->checkPawaPayProviderAvailability($countryConfig['country'], $network, 'DEPOSIT');
            if ($providerStatus !== 'OPERATIONAL') {
                $msg = "Provider {$network} est indisponible ({$providerStatus}).";
                $payment->update(['status' => 'failed']);
                return $this->handleError($msg, $returnAsJson, $errorLink);
            }

            // Configuration provider
            $providerConfig = $this->getPawaPayProviderConfig($countryConfig['country'], $network, 'DEPOSIT');
            if (!$providerConfig || $providerConfig['status'] !== 'OPERATIONAL') {
                return $this->handleError('Configuration provider PawaPay invalide.', $returnAsJson, $errorLink);
            }

            // Numéro de téléphone formé avec indicatif
            $rawPhone = preg_replace('/[^0-9]/', '', $request->input('phone', ''));
            if (str_starts_with($rawPhone, $countryConfig['countryCode'])) {
                $rawPhone = substr($rawPhone, strlen($countryConfig['countryCode']));
            }
            $payerPhoneNumber = $countryConfig['countryCode'] . $rawPhone;

            // UUID de dépôt PawaPay
            $depositId = Uuid::uuid4()->toString();

            // Mettre à jour la référence du paiement avec l'UUID PawaPay
            $payment->update([
                'transaction_id' => $depositId,
                'payment_details' => array_merge($payment->payment_details ?? [], [
                    'original_ref' => $refNumber,
                    'pawapay_deposit_id' => $depositId,
                ]),
            ]);

            $pawaCallback = $providerConfig['callbackUrl'] ?? route('payment.callback', ['service' => 'pawapay']);

            try {
                $pawaData = [
                    "depositId"            => $depositId,
                    "amount"               => (string) $amount,
                    "currency"             => $countryConfig['currency'],
                    "country"              => $countryConfig['country'],
                    "correspondent"        => $network,
                    "payer"                => ["type" => "MSISDN", "address" => ["value" => $payerPhoneNumber]],
                    "customerTimestamp"    => now()->toISOString(),
                    "statementDescription" => Str::limit("Achat " . substr($refNumber, -10), 22, ''),
                    "notificationUrl"      => $pawaCallback,
                ];

                Log::info("PawaPay Request {$depositId}: " . json_encode($pawaData));
                $response = Http::withToken($apiToken)->timeout(25)->post("{$baseUrl}/deposits", $pawaData);

                if ($response->successful()) {
                    $status = $response->json()['status'] ?? null;
                    if ($status === 'ACCEPTED') {
                        Session::put('payment_reference', $depositId);
                        return $returnAsJson
                            ? ['success' => true, 'redirect_url' => route('payment.pending'), 'message' => 'Confirmez le paiement sur votre mobile.']
                            : redirect()->route('payment.pending')->with('info', 'Veuillez confirmer le paiement sur votre mobile.');
                    }
                    $reason = $response->json()['rejectionReason']['rejectionMessage'] ?? 'Raison inconnue';
                    return $this->handleError("PawaPay: {$reason}", $returnAsJson, $errorLink);
                }

                Log::error("PawaPay API Error: " . $response->body());
                return $this->handleError('Erreur PawaPay: ' . $response->json('error_description', 'Erreur inconnue'), $returnAsJson, $errorLink);

            } catch (\Exception $e) {
                Log::error("PawaPay Exception: {$e->getMessage()}");
                return $this->handleError('Erreur technique PawaPay.', $returnAsJson, $errorLink);
            }
        }

        // =====================================================================
        // 4. PAIEMENTPRO (Carte + Mobile Money Afrique de l'Ouest/Centre)
        // =====================================================================
        $paiementProNetworks = [
            'CARD',
            'MOMOBJ', 'FLOOZBJ',
            'OMBF',
            'OMCM', 'MOMOCM',
            'OMCIV2', 'MOMOCI', 'FLOOZ', 'WAVECI',
            'OMGN', 'OMML',
            'AIRTELNG',
            'OMSN', 'WAVESN',
            'MOOTG', 'TOGOCEL',
        ];

        if (in_array($network, $paiementProNetworks)) {
            try {
                // Surcharge carte
                if ($network === 'CARD') {
                    $amount = ($amount * 1.05) + 780;
                }

                // Devise PaiementPro
                $userPhone              = $payment->user->phone ?? $request->phone ?? '';
                $detectedCountryIso     = $this->detectCountryFromPhone($userPhone);
                $currencyIso            = $this->countryConfigs[$detectedCountryIso]['currency'] ?? 'XOF';
                $paiementProCurrencyCode = $this->currencyToPaiementProCode[$currencyIso] ?? '952';

                $paiementProArray = [
                    'merchantId'          => env('PAIEMENTPRO_MERCHANT_ID', 'PP-F1278'),
                    'countryCurrencyCode' => $paiementProCurrencyCode,
                    'amount'              => $amount,
                    'channel'             => $network,
                    'customerEmail'       => $payment->user->email ?? $request->email ?? 'no-reply@example.com',
                    'customerFirstName'   => $payment->user->first_name ?? $request->firstname ?? 'N/A',
                    'customerLastname'    => $payment->user->last_name  ?? $request->lastname  ?? '',
                    'referenceNumber'     => $refNumber,
                    'notificationURL'     => route('payment.callback', ['service' => 'paiementpro']),
                    'returnURL'           => $returnLink,
                    'description'         => "Achat sur " . config('app.name'),
                ];

                if ($network !== 'CARD') {
                    $paiementProArray['customerPhoneNumber'] = $request->phone;
                }

                Log::info("PaiementPro payload pour Ref {$refNumber}", $paiementProArray);

                $context    = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]]);
                $soapClient = new SoapClient(
                    'https://www.paiementpro.net/webservice/OnlineServicePayment_v2.php?wsdl',
                    ['stream_context' => $context]
                );
                $response = $soapClient->initTransact($paiementProArray);

                if (isset($response->Sessionid) && $response->Description === 'SUCCESS') {
                    $payment->update([
                        'payment_details' => array_merge($payment->payment_details ?? [], ['session_id' => $response->Sessionid]),
                    ]);
                    $redirectUrl = "https://www.paiementpro.net/webservice/onlinepayment/processing_v2.php?sessionid={$response->Sessionid}";
                    return $returnAsJson
                        ? ['success' => true, 'redirect_url' => $redirectUrl]
                        : Redirect::away($redirectUrl);
                }

                throw new \Exception($response->Description ?? 'Erreur inconnue PaiementPro');

            } catch (\Exception $e) {
                Log::error("PaiementPro Error for Ref {$refNumber}: " . $e->getMessage());
                return $this->handleError("Erreur PaiementPro: " . $e->getMessage(), $returnAsJson, $errorLink);
            }
        }

        // Réseau non géré
        return $this->handleError("Mode de paiement non supporté ({$network})", $returnAsJson, $errorLink);
    }

    // =========================================================================
    // POST-PAIEMENT : finalisation (livre / abonnement)
    // =========================================================================

    /**
     * Finalise l'achat après confirmation du paiement (appelé depuis le callback).
     */
    public function finalizePurchase(Payment $payment): void
    {
        $notificationService = app(\App\Services\NotificationService::class);
        $user = $payment->user;

        if (in_array($payment->payment_type, ['book_purchase', 'book_pdf', 'book_audio'])) {

            \App\Models\Purchase::updateOrCreate(
                ['payment_id' => $payment->id],
                [
                    'user_id'       => $payment->user_id,
                    'book_id'       => $payment->book_id,
                    'purchase_type' => $payment->payment_details['purchase_type'] ?? ($payment->payment_type === 'book_audio' ? 'audio' : 'pdf'),
                    'price'         => $payment->amount,
                    'is_active'     => true,
                ]
            );

            $revenueCalculator = app(\App\Services\RevenueCalculatorService::class);
            $revenueCalculator->recordRevenue($payment);

            $book = $payment->book;
            $notificationService->sendNotification(
                $user,
                __('Achat confirmé'),
                __('Votre achat pour le livre ":title" a été validé. Vous pouvez maintenant le lire dans votre bibliothèque.', ['title' => $book->title]),
                route('book.show', $book->slug),
                'success'
            );

        } elseif (in_array($payment->payment_type, ['subscription', 'subscription_renewal'])) {

            $subscription = $payment->subscription ?? Subscription::find($payment->subscription_id);
            if ($subscription) {
                $plan     = $subscription->subscriptionPlan;
                $duration = $plan->duration_days ?? 30;

                $subscription->update([
                    'status'     => 'active',
                    'start_date' => $subscription->start_date ?? now(),
                    'end_date'   => now()->addDays($duration),
                ]);

                $notificationService->sendNotification(
                    $user,
                    __('Abonnement activé'),
                    __('Votre abonnement au plan ":plan" a été activé avec succès.', ['plan' => $plan->name]),
                    route('subscription.index'),
                    'success'
                );
            }
        }
    }

    // =========================================================================
    // HELPER PRIVÉ
    // =========================================================================

    private function handleError(string $message, bool $returnAsJson, string $errorLink)
    {
        Log::warning("PaymentService error: {$message}");
        if ($returnAsJson) {
            return ['success' => false, 'error' => $message];
        }
        return Redirect::to($errorLink)->with('danger', $message);
    }
}