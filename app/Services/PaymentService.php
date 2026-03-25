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

    public function __construct()
    {
        $this->initializeConfigurations();
    }

    /**
     * Get all possible configurations for the admin panel.
     */
    public function getGlobalConfigurations()
    {
        return [
            'countries' => $this->countryConfigs,
            'methods' => $this->supportedMethods
        ];
    }

    private function initializeConfigurations()
    {
        $this->countryConfigs = [
            'CI' => ['currency' => 'XOF', 'code' => '225', 'iso' => 'CI'],
            'BJ' => ['currency' => 'XOF', 'code' => '229', 'iso' => 'BJ'],
            'BF' => ['currency' => 'XOF', 'code' => '226', 'iso' => 'BF'],
            'CM' => ['currency' => 'XOF', 'code' => '237', 'iso' => 'CM'],
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
            'MA' => ['currency' => 'MAD', 'code' => '212', 'iso' => 'MAD'],
            'FR' => ['currency' => 'EUR', 'code' => '33', 'iso' => 'FR'],
            'TG' => ['currency' => 'XOF', 'code' => '228', 'iso' => 'TG'],
            'MZ' => ['currency' => 'MZN', 'code' => '258', 'iso' => 'MZ'],
        ];

        $this->supportedMethods = [
            'MOMOCI' => ['name' => 'MTN CI', 'icon_color' => '#FFCC00', 'countries' => ['CI']],
            'ORANGE_CIV' => ['name' => 'ORANGE', 'icon_color' => '#FFCC00', 'countries' => ['CI', 'FR']],
            'WAVE_CIV' => ['name' => 'WAVE', 'icon_color' => '#FFCC00', 'countries' => ['CI', 'FR']],
            'FLOOZ' => ['name' => 'MOOV CI', 'icon_color' => '#0066FF', 'countries' => ['CI']],
            'MOMOBJ' => ['name' => 'MTN BENIN', 'icon_color' => '#FFCC00', 'countries' => ['BJ']],
            'FLOOZBJ' => ['name' => 'MOOV BENIN', 'icon_color' => '#0066FF', 'countries' => ['BJ']],
            'OMBF' => ['name' => 'OM BF', 'icon_color' => '#FF6600', 'countries' => ['BF']],
            'FLOOZ_BFA' => ['name' => 'MOOV BF', 'icon_color' => '#FF6600', 'countries' => ['BF']],
            'OMCM' => ['name' => 'OM CM', 'icon_color' => '#FF6600', 'countries' => ['CM']],
            'WAVE_SEN' => ['name' => 'WAVE SN', 'icon_color' => '#00CC99', 'countries' => ['SN']],
            'CARD' => ['name' => 'VISA/MASTERCARD', 'icon_color' => '#1a1f71', 'countries' => array_keys($this->countryConfigs)],
        ];
    }

    public function getAvailablePaymentMethods(string $phone = null, string $countryIso = null): array
    {
        $countryCode = $countryIso ?? ($phone ? $this->detectCountryFromPhone($phone) : 'CI');
        $countryCode = strtoupper($countryCode);

        $availableMethods = [];

        // Fetch enabled methods from database
        $dbSettings = Setting::where('key', 'payment_methods')->first();
        $enabledMethods = $dbSettings ? json_decode($dbSettings->value, true) : null;

        foreach ($this->supportedMethods as $methodCode => $details) {
            // If we have DB settings, check if this method is enabled for this country
            if ($enabledMethods) {
                if (isset($enabledMethods[$countryCode][$methodCode]) && $enabledMethods[$countryCode][$methodCode] === 'on') {
                    $availableMethods[] = [
                        'id' => $methodCode,
                        'name' => $details['name'],
                        'icon_color' => $details['icon_color'],
                    ];
                }
            } else {
                // Fallback to hardcoded logic if no DB settings exist yet
                if (in_array($countryCode, $details['countries'])) {
                    $availableMethods[] = [
                        'id' => $methodCode,
                        'name' => $details['name'],
                        'icon_color' => $details['icon_color'],
                    ];
                }
            }
        }

        return [
            'country' => $countryCode,
            'currency' => $this->countryConfigs[$countryCode]['currency'] ?? 'XOF',
            'methods' => $availableMethods,
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

    public function initiatePayment(Request $request, Payment $payment, bool $returnAsJson = false, ?string $successUrl = null)
    {
        $network = $request->input('network', 'CARD');
        $amount = $payment->amount;
        $refNumber = $payment->transaction_id;
        
        $returnLink = route('payment.success');
        $errorLink = route('payment.failed');

        Session::put('payment_return_url', $successUrl ?? route('home'));

        // --- WAVE (CI or SN) ---
        if (in_array($network, ["WAVE_CIV", "WAVE_SEN"])) {
            try {
                $response = Http::withToken(env('WAVE_API_KEY'))
                    ->post("https://api.wave.com/v1/checkout/sessions", [
                        "amount" => (string) $amount,
                        "currency" => "XOF",
                        "aggregated_merchant_id" => env('WAVE_AGGREGATED_MERCHANT_ID'),
                        "error_url" => $errorLink,
                        "success_url" => $returnLink,
                    ]);

                if ($response->successful() && isset($response->json()['wave_launch_url'])) {
                    $payment->update(['payment_details' => array_merge($payment->payment_details ?? [], ['wave_id' => $response->json()['id']])]);
                    $redirectUrl = $response->json()['wave_launch_url'];
                    return $returnAsJson ? ['success' => true, 'redirect_url' => $redirectUrl] : Redirect::away($redirectUrl);
                }
                throw new \Exception('Erreur Wave API');
            } catch (\Exception $e) {
                Log::error("Wave Error: " . $e->getMessage());
                return $this->handleError($e->getMessage(), $returnAsJson, $errorLink);
            }
        }

        // --- TOUCHPAY (Orange Money, MTN CI, Moov CI) ---
        if (in_array($network, ['MOMOCI', 'ORANGE_CIV', 'FLOOZ'])) {
            try {
                $touchPayUrl = "https://api.gutouch.com/dist/api/touchpayapi/v1/".env('TOUCHPAY_MERCHANT_KEY')."/transaction?loginAgent=".env('TOUCHPAY_LOGIN_AGENT')."&passwordAgent=".env('TOUCHPAY_PASSWORD_AGENT');
                
                $serviceCode = match($network) {
                    'ORANGE_CIV' => 'PAIEMENTMARCHANDOMPAYCIDIRECT',
                    'MOMOCI' => 'PAIEMENTMARCHAND_MTN_CI',
                    'FLOOZ' => 'PAIEMENTMARCHAND_MOOV_CI',
                    default => null
                };

                $touchData = [
                    "idFromClient" => $refNumber,
                    "additionnalInfos" => [
                        "recipientEmail" => $payment->user->email,
                        "recipientFirstName" => $payment->user->first_name,
                        "recipientLastName" => $payment->user->last_name,
                        "destinataire" => $request->phone,
                    ],
                    "amount" => $amount,
                    "callback" => route('payment.callback', ['service' => 'touchpay']),
                    "recipientNumber" => $request->phone,
                    "serviceCode" => $serviceCode,
                ];

                if ($network == 'ORANGE_CIV') { $touchData['additionnalInfos']['otp'] = $request->otp; }

                $client = new Client();
                $response = $client->put($touchPayUrl, [
                    'headers' => ['Content-Type' => 'application/json'],
                    'auth' => [env('TOUCHPAY_API_KEY'), env('TOUCHPAY_API_SECRET'), 'digest'],
                    'json' => $touchData,
                ]);

                $resp = json_decode($response->getBody()->getContents(), true);
                if (in_array(strtoupper($resp['status'] ?? ''), ['INITIATED', 'PENDING', 'SUCCESSFUL'])) {
                    return $returnAsJson ? ['success' => true, 'redirect_url' => route('payment.pending')] : Redirect::route('payment.pending');
                }
                throw new \Exception($resp['message'] ?? 'Erreur TouchPay');
            } catch (\Exception $e) {
                Log::error("TouchPay Error: " . $e->getMessage());
                return $this->handleError($e->getMessage(), $returnAsJson, $errorLink);
            }
        }

        // --- PAIEMENTPRO (Cards & Other African Mobile Money) ---
        // MOMOBJ, FLOOZBJ, OMBF, FLOOZ_BFA, OMCM
        $paiementProNetworks = ['CARD', 'MOMOBJ', 'FLOOZBJ', 'OMBF', 'FLOOZ_BFA', 'OMCM'];
        if (in_array($network, $paiementProNetworks)) {
            try {
                // Surcharge pour carte
                if ($network === 'CARD') { $amount = ($amount * 1.05) + 780; }

                $paiementProArray = [
                    'merchantId' => env('PAIEMENTPRO_MERCHANT_ID'),
                    'countryCurrencyCode' => '952',
                    'amount' => $amount,
                    'channel' => $network,
                    'customerEmail' => $payment->user->email,
                    'customerFirstName' => $payment->user->first_name,
                    'customerLastname' => $payment->user->last_name,
                    'customerPhoneNumber' => $request->phone,
                    'referenceNumber' => $refNumber,
                    'notificationURL' => route('payment.callback', ['service' => 'paiementpro']),
                    'returnURL' => $returnLink,
                    'description' => "Achat sur ".config('platform.name'),
                ];

                $context = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
                $soapClient = new SoapClient('https://www.paiementpro.net/webservice/OnlineServicePayment_v2.php?wsdl', ['stream_context' => $context]);
                $response = $soapClient->initTransact($paiementProArray);

                if (isset($response->Sessionid) && $response->Description === "SUCCESS") {
                    $payment->update(['payment_details' => array_merge($payment->payment_details ?? [], ['session_id' => $response->Sessionid])]);
                    $redirectUrl = "https://www.paiementpro.net/webservice/onlinepayment/processing_v2.php?sessionid={$response->Sessionid}";
                    return $returnAsJson ? ['success' => true, 'redirect_url' => $redirectUrl] : Redirect::away($redirectUrl);
                }
                throw new \Exception($response->Description ?? 'Erreur PaiementPro');
            } catch (\Exception $e) {
                Log::error("PaiementPro Error: " . $e->getMessage());
                return $this->handleError($e->getMessage(), $returnAsJson, $errorLink);
            }
        }

        return $this->handleError("Mode de paiement non supporté ({$network})", $returnAsJson, $errorLink);
    }

    public function finalizePurchase(Payment $payment)
    {
        if (in_array($payment->payment_type, ['book_purchase', 'book_pdf', 'book_audio'])) {
            // Check if purchase already exists to avoid duplicates
            \App\Models\Purchase::updateOrCreate(
                ['payment_id' => $payment->id],
                [
                    'user_id' => $payment->user_id,
                    'book_id' => $payment->book_id,
                    'purchase_type' => $payment->payment_details['purchase_type'] ?? ($payment->payment_type === 'book_audio' ? 'audio' : 'pdf'),
                    'price' => $payment->amount,
                    'is_active' => true,
                ]
            );
            
            // Record revenue if it's a book purchase
            $revenueCalculator = app(\App\Services\RevenueCalculatorService::class);
            $revenueCalculator->recordRevenue($payment);

        } elseif (in_array($payment->payment_type, ['subscription', 'subscription_renewal'])) {
            $subscription = $payment->subscription ?? Subscription::find($payment->subscription_id);
            if ($subscription) {
                $plan = $subscription->subscriptionPlan;
                $duration = $plan->duration_days ?? 30;

                $subscription->update([
                    'status' => 'active',
                    'start_date' => $subscription->start_date ?? now(),
                    'end_date' => now()->addDays($duration),
                ]);
            }
        }
    }

    private function handleError($message, $returnAsJson, $errorLink)
    {
        if ($returnAsJson) {
            return ['success' => false, 'error' => $message];
        }
        return Redirect::to($errorLink)->with('danger', $message);
    }
}
