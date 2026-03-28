<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Route unique : GET|POST /payment/callback/{service}
     * name         : payment.callback
     *
     * Services gérés : wave | touchpay | pawapay | paiementpro | paystack
     * Types de paiements : book_pdf | book_audio | book_purchase | subscription | subscription_renewal
     */
    public function handle(Request $request, string $service)
    {
        Log::channel('payment')->info("=== CALLBACK {$service} ===", [
            'ip'     => $request->ip(),
            'method' => $request->method(),
            'body'   => $request->getContent(),
        ]);

        return match ($service) {
            'wave'        => $this->handleWave($request),
            'touchpay'    => $this->handleTouchPay($request),
            'pawapay'     => $this->handlePawaPay($request),
            'paiementpro' => $this->handlePaiementPro($request),
            'paystack'    => $this->handlePaystack($request),
            default       => response('Service non géré', 404),
        };
    }

    // =========================================================================
    // WAVE
    // =========================================================================

    private function handleWave(Request $request)
    {
        $data      = $request->json()->all();
        $sessionId = $data['data']['id'] ?? null;
        $status    = $data['data']['payment_status'] ?? null;

        if (!$sessionId || $status !== 'succeeded') {
            return response('OK', 200);
        }

        $payment = Payment::where(function ($q) use ($sessionId) {
                $q->where('transaction_id', $sessionId)
                  ->orWhereJsonContains('payment_details->wave_id', $sessionId);
            })
            ->where('status', 'pending')
            ->first();

        if ($payment) {
            $this->validateAndFinalize($payment);
        } else {
            Log::warning("Wave – aucun paiement pending pour session {$sessionId}");
        }

        return response('OK', 200);
    }

    // =========================================================================
    // TOUCHPAY
    // =========================================================================

    private function handleTouchPay(Request $request)
    {
        $body      = $request->json()->all();
        $reference = $body['partner_transaction_id'] ?? null;
        $status    = strtoupper($body['status'] ?? '');

        if (!$reference || $status !== 'SUCCESSFUL') {
            return response('OK', 200);
        }

        $payment = Payment::where('transaction_id', $reference)->where('status', 'pending')->first();

        if ($payment) {
            $this->validateAndFinalize($payment, $body['gu_transaction_id'] ?? null);
        } else {
            Log::warning("TouchPay – aucun paiement pending pour ref {$reference}");
        }

        return response('OK', 200);
    }

    // =========================================================================
    // PAWAPAY
    // =========================================================================

    private function handlePawaPay(Request $request)
    {
        $body      = $request->json()->all();
        $depositId = $body['depositId'] ?? null;
        $status    = strtoupper($body['status'] ?? '');

        if (!$depositId || $status !== 'COMPLETED') {
            return response('OK', 200);
        }

        $payment = Payment::where('transaction_id', $depositId)->where('status', 'pending')->first();

        if ($payment) {
            $this->validateAndFinalize($payment);
        } else {
            Log::warning("PawaPay – aucun paiement pending pour deposit {$depositId}");
        }

        return response('OK', 200);
    }

    // =========================================================================
    // PAIEMENTPRO (redirect navigateur)
    // =========================================================================

    private function handlePaiementPro(Request $request)
    {
        $reference    = $request->input('referenceNumber');
        $responseCode = $request->input('responsecode');
        $payId        = $request->input('payId');

        if (!$reference) {
            return redirect()->route('home');
        }

        $payment = Payment::where('transaction_id', $reference)->where('status', 'pending')->first();

        if ($payment && $responseCode == '0') {
            $this->validateAndFinalize($payment, $payId);
            return redirect()->route('payment.success')->with('success', 'Paiement validé avec succès !');
        }

        Log::warning("PaiementPro – paiement non trouvé ou refusé", ['ref' => $reference, 'code' => $responseCode]);
        return redirect()->route('payment.failed')->with('danger', 'Transaction refusée ou introuvable.');
    }

    // =========================================================================
    // PAYSTACK (webhook JSON)
    // =========================================================================

    private function handlePaystack(Request $request)
    {
        // Vérification de la signature Paystack
        $secretKey = env('PAYSTACK_SECRET_KEY');
      

        $payload   = $request->json()->all();
        $event     = $payload['event'] ?? null;
        $reference = $payload['data']['reference'] ?? null;
        $paystackId = $payload['data']['id'] ?? null;
        $status    = $payload['data']['status'] ?? null;

        Log::channel('payment')->info("Paystack event: {$event}", ['reference' => $reference, 'status' => $status]);

        // Seul l'événement charge.success nous intéresse
        if ($status !== 'success' || !$reference) {
            return response('OK', 200);
        }

   
        $payment = Payment::where('transaction_id', $reference)->where('status', 'pending')->first();

        if ($payment) {
            $this->validateAndFinalize($payment, (string) $paystackId);
        } else {
            Log::warning("Paystack – aucun paiement pending pour ref {$reference}");
        }

        return response('OK', 200);
    }

    // =========================================================================
    // VALIDATION & FINALISATION (commune à tous les prestataires)
    // =========================================================================

    private function validateAndFinalize(Payment $payment, ?string $providerRef = null): void
    {
        DB::transaction(function () use ($payment, $providerRef) {
            // Verrou pour éviter la double validation
            $fresh = Payment::where('id', $payment->id)->lockForUpdate()->first();

            if (!$fresh || $fresh->status !== 'pending') {
                Log::warning("Paiement déjà traité ou introuvable", [
                    'id' => $payment->id, 'status' => $fresh->status ?? 'NULL',
                ]);
                return;
            }

            $details = $fresh->payment_details ?? [];
            if ($providerRef) $details['provider_ref'] = $providerRef;

            $fresh->update(['status' => 'completed', 'payment_details' => $details]);

            Log::channel('payment')->info("Paiement validé", [
                'id'             => $fresh->id,
                'transaction_id' => $fresh->transaction_id,
                'payment_type'   => $fresh->payment_type,
                'amount'         => $fresh->amount,
            ]);

            // Délégation métier : Purchase, Subscription, notifications…
            $this->paymentService->finalizePurchase($fresh);
        });
    }
}