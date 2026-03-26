<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Point d'entrée unique pour tous les callbacks de prestataires de paiement.
     *
     * Route : GET|POST /payment/callback/{service}
     *         name    : payment.callback
     *
     * Prestataires supportés : wave | touchpay | pawapay | paiementpro
     * Types de paiements gérés : book_pdf | book_audio | book_purchase | subscription | subscription_renewal
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
            Log::warning("Wave callback – aucun paiement pending trouvé pour session {$sessionId}");
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

        $payment = Payment::where('transaction_id', $reference)
            ->where('status', 'pending')
            ->first();

        if ($payment) {
            $providerRef = $body['gu_transaction_id'] ?? null;
            $this->validateAndFinalize($payment, $providerRef);
        } else {
            Log::warning("TouchPay callback – aucun paiement pending trouvé pour ref {$reference}");
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

        $payment = Payment::where('transaction_id', $depositId)
            ->where('status', 'pending')
            ->first();

        if ($payment) {
            $this->validateAndFinalize($payment);
        } else {
            Log::warning("PawaPay callback – aucun paiement pending trouvé pour deposit {$depositId}");
        }

        return response('OK', 200);
    }

    // =========================================================================
    // PAIEMENTPRO
    // =========================================================================

    private function handlePaiementPro(Request $request)
    {
        $reference    = $request->input('referenceNumber');
        $responseCode = $request->input('responsecode');
        $payId        = $request->input('payId');

        if (!$reference) {
            return redirect()->route('home');
        }

        $payment = Payment::where('transaction_id', $reference)
            ->where('status', 'pending')
            ->first();

        if ($payment && $responseCode == '0') {
            $this->validateAndFinalize($payment, $payId);

            // PaiementPro redirige le navigateur de l'utilisateur → on renvoie vers une page de succès
            return redirect()->route('payment.success')
                ->with('success', 'Paiement validé avec succès !');
        }

        Log::warning("PaiementPro callback – paiement non trouvé ou code refus", [
            'reference'     => $reference,
            'response_code' => $responseCode,
        ]);

        return redirect()->route('payment.failed')
            ->with('danger', 'Transaction refusée ou introuvable.');
    }

    // =========================================================================
    // VALIDATION & FINALISATION (commune à tous les prestataires)
    // =========================================================================

    /**
     * Valide le paiement en base (statut pending → completed) puis
     * appelle PaymentService::finalizePurchase() qui active le bon objet
     * métier selon payment_type (livre ou abonnement).
     */
    private function validateAndFinalize(Payment $payment, ?string $providerRef = null): void
    {
        DB::transaction(function () use ($payment, $providerRef) {

            // Verrou pour éviter la double validation en cas de callback dupliqué
            $fresh = Payment::where('id', $payment->id)->lockForUpdate()->first();

            if (!$fresh || $fresh->status !== 'pending') {
                Log::warning("Paiement déjà traité ou introuvable – abandon", [
                    'id'     => $payment->id,
                    'status' => $fresh->status ?? 'NULL',
                ]);
                return;
            }

            // Mise à jour du statut + référence provider si disponible
            $details = $fresh->payment_details ?? [];
            if ($providerRef) {
                $details['provider_ref'] = $providerRef;
            }

            $fresh->update([
                'status'          => 'completed',
                'payment_details' => $details,
            ]);

            Log::channel('payment')->info("Paiement validé", [
                'id'             => $fresh->id,
                'transaction_id' => $fresh->transaction_id,
                'payment_type'   => $fresh->payment_type,
                'amount'         => $fresh->amount,
                'provider_ref'   => $providerRef,
            ]);

            // Délégation de la logique métier à PaymentService
            // (crée le Purchase, active l'abonnement, envoie les notifications…)
            $this->paymentService->finalizePurchase($fresh);
        });
    }
}