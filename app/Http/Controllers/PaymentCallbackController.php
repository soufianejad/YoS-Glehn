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
     * ┌─────────────────┬────────────┬──────────────────────────────────────────────┐
     * │ Provider        │ Méthode    │ Comment ça marche                            │
     * ├─────────────────┼────────────┼──────────────────────────────────────────────┤
     * │ PaiementPro     │ GET        │ returnURL : redirect navigateur après paiement│
     * │                 │            │ notificationURL : webhook POST (même route)   │
     * │                 │            │ On vérifie responsecode == '0'               │
     * ├─────────────────┼────────────┼──────────────────────────────────────────────┤
     * │ TouchPay        │ POST JSON  │ Webhook avec partner_transaction_id + status │
     * ├─────────────────┼────────────┼──────────────────────────────────────────────┤
     * │ PawaPay         │ POST JSON  │ Webhook avec depositId + status              │
     * ├─────────────────┼────────────┼──────────────────────────────────────────────┤
     * │ Wave            │ POST JSON  │ Webhook avec data.id + payment_status        │
     * ├─────────────────┼────────────┼──────────────────────────────────────────────┤
     * │ Paystack        │ POST JSON  │ Webhook avec event + data.reference + status │
     * │                 │            │ NE PAS confondre avec le redirect GET de PP  │
     * └─────────────────┴────────────┴──────────────────────────────────────────────┘
     */
    public function handle(Request $request, string $service)
    {
        $logger = Log::build([
            'driver' => 'single',
            'path'   => storage_path('logs/payment_callback.log'),
            'level'  => 'debug',
        ]);

        $logger->info("=== CALLBACK {$service} ===", [
            'ip'      => $request->ip(),
            'method'  => $request->method(),
            'query'   => $request->query(),
            'body'    => $request->getContent(),
            'headers' => $request->headers->all(),
        ]);

        return match ($service) {
            'wave'        => $this->handleWave($request, $logger),
            'touchpay'    => $this->handleTouchPay($request, $logger),
            'pawapay'     => $this->handlePawaPay($request, $logger),
            'paiementpro' => $this->handlePaiementPro($request, $logger),
            'paystack'    => $this->handlePaystack($request, $logger),
            default       => response('Service non géré', 404),
        };
    }

    // =========================================================================
    // 1. WAVE  (webhook POST JSON)
    // =========================================================================

    private function handleWave(Request $request, $logger)
    {
        $data      = $request->json()->all();
        $sessionId = $data['data']['id'] ?? null;
        $status    = $data['data']['payment_status'] ?? null;

        $logger->info('Wave payload', ['session_id' => $sessionId, 'status' => $status]);

        if (!$sessionId || $status !== 'succeeded') {
            $logger->warning('Wave – ignoré', ['status' => $status]);
            return response('OK', 200);
        }

        // Recherche par wave_id stocké dans payment_details OU par transaction_id
        $payment = Payment::where(function ($q) use ($sessionId) {
                $q->where('transaction_id', $sessionId)
                  ->orWhereJsonContains('payment_details->wave_id', $sessionId);
            })
            ->where('status', 'pending')
            ->first();

        if ($payment) {
            $logger->info('Wave – paiement trouvé', ['id' => $payment->id, 'type' => $payment->payment_type]);
            $this->validateAndFinalize($payment, null, $logger);
        } else {
            $logger->warning('Wave – aucun paiement pending pour session', ['session_id' => $sessionId]);
        }

        return response('OK', 200);
    }

    // =========================================================================
    // 2. TOUCHPAY  (webhook POST JSON)
    // =========================================================================

    private function handleTouchPay(Request $request, $logger)
    {
        $body      = $request->json()->all();
        $reference = $body['partner_transaction_id'] ?? null;
        $status    = strtoupper($body['status'] ?? '');
        $guTxId    = $body['gu_transaction_id'] ?? null;

        $logger->info('TouchPay payload', ['reference' => $reference, 'status' => $status]);

        if (!$reference || $status !== 'SUCCESSFUL') {
            $logger->warning('TouchPay – ignoré', ['status' => $status]);
            return response('OK', 200);
        }

        $payment = Payment::where('transaction_id', $reference)
            ->where('status', 'pending')
            ->first();

        if ($payment) {
            $logger->info('TouchPay – paiement trouvé', ['id' => $payment->id, 'type' => $payment->payment_type]);
            $this->validateAndFinalize($payment, $guTxId, $logger);
        } else {
            $logger->warning('TouchPay – aucun paiement pending', ['reference' => $reference]);
        }

        return response('OK', 200);
    }

    // =========================================================================
    // 3. PAWAPAY  (webhook POST JSON)
    // =========================================================================

    private function handlePawaPay(Request $request, $logger)
    {
        $body      = $request->json()->all();
        $depositId = $body['depositId'] ?? null;
        $status    = strtoupper($body['status'] ?? '');

        $logger->info('PawaPay payload', ['deposit_id' => $depositId, 'status' => $status]);

        if (!$depositId || $status !== 'COMPLETED') {
            $logger->warning('PawaPay – ignoré', ['status' => $status]);
            return response('OK', 200);
        }

        $payment = Payment::where('transaction_id', $depositId)
            ->where('status', 'pending')
            ->first();

        if ($payment) {
            $logger->info('PawaPay – paiement trouvé', ['id' => $payment->id, 'type' => $payment->payment_type]);
            $this->validateAndFinalize($payment, null, $logger);
        } else {
            $logger->warning('PawaPay – aucun paiement pending', ['deposit_id' => $depositId]);
        }

        return response('OK', 200);
    }

    // =========================================================================
    // 4. PAIEMENTPRO
    //    — notificationURL : POST avec referenceNumber + responsecode + payId
    //    — returnURL       : GET  avec les mêmes params (redirect navigateur)
    //
    //    IMPORTANT : La validation n'est faite que si responsecode == '0'.
    //    La returnURL et notificationURL pointent toutes les deux vers cette route.
    //    On traite les deux cas : si le paiement est déjà validé (par le webhook POST
    //    arrivé avant), on redirige quand même vers la page de succès.
    // =========================================================================

    private function handlePaiementPro(Request $request, $logger)
    {
        $reference    = $request->input('referenceNumber');
        $responseCode = $request->input('responsecode');
        $payId        = $request->input('payId');

        $logger->info('PaiementPro callback', [
            'method'        => $request->method(),
            'reference'     => $reference,
            'response_code' => $responseCode,
            'pay_id'        => $payId,
        ]);

        if (!$reference) {
            $logger->warning('PaiementPro – referenceNumber manquant');
            // GET sans référence = l'utilisateur arrive sur la page d'accueil
            return $request->isMethod('GET')
                ? redirect()->route('home')
                : response('Bad Request', 400);
        }

        // Chercher le paiement (pending OU déjà completed, car le webhook
        // POST arrive parfois avant la redirect GET)
        $payment = Payment::where('transaction_id', $reference)
            ->whereIn('status', ['pending', 'completed'])
            ->first();

        $logger->info('PaiementPro – résultat recherche', [
            'found'  => (bool) $payment,
            'status' => $payment->status ?? 'NULL',
        ]);

        // ── Paiement refusé ────────────────────────────────────────────────
        if ($responseCode != '0') {
            $logger->warning('PaiementPro – paiement refusé', ['code' => $responseCode]);
            if ($request->isMethod('GET')) {
                return redirect()->route('payment.failed')
                    ->with('danger', 'Transaction refusée par PaiementPro (code ' . $responseCode . ').');
            }
            return response('REFUSED', 200);
        }

        // ── Paiement accepté ───────────────────────────────────────────────
        if (!$payment) {
            $logger->warning('PaiementPro – aucun paiement trouvé pour ref', ['reference' => $reference]);
            if ($request->isMethod('GET')) {
                return redirect()->route('payment.failed')->with('danger', 'Paiement introuvable.');
            }
            return response('NOT_FOUND', 200);
        }

        // Valider si encore pending (évite la double validation)
        if ($payment->status === 'pending') {
            $this->validateAndFinalize($payment, $payId, $logger);
        } else {
            $logger->info('PaiementPro – déjà validé, skip', ['id' => $payment->id]);
        }

        // Réponse selon le type de requête
        if ($request->isMethod('GET')) {
            // Redirect navigateur → page de succès lisible
            return redirect()->route('payment.success')
                ->with('success', 'Votre paiement a été validé avec succès !');
        }

        // Notification serveur → juste 200
        return response('OK', 200);
    }

    // =========================================================================
    // 5. PAYSTACK  (webhook POST JSON UNIQUEMENT)
    //
    //    Paystack envoie :
    //    - Un webhook JSON POST sur la callback_url configurée dans l'API
    //    - Après paiement, Paystack redirige le navigateur vers callback_url
    //      avec ?trxref=REF&reference=REF en GET
    //
    //    On distingue les deux : GET = simple redirection de succès,
    //    POST = webhook à traiter avec vérification de signature.
    // =========================================================================

    private function handlePaystack(Request $request, $logger)
    {
        $logger->info('Paystack callback reçu', [
            'method' => $request->method(),
            'query'  => $request->query(),
            'body'   => $request->getContent(),
        ]);

        // ── GET : l'utilisateur est redirigé par Paystack après paiement ──
        // On ne valide PAS ici — la validation se fait via le webhook POST.
        // On cherche juste le paiement pour afficher la bonne page.
        if ($request->isMethod('GET')) {
            $reference = $request->query('reference') ?? $request->query('trxref');

            $logger->info('Paystack – redirect GET navigateur', ['reference' => $reference]);

            if (!$reference) {
                return redirect()->route('payment.success');
            }

            $payment = Payment::where('transaction_id', $reference)->first();

            if ($payment && $payment->status === 'completed') {
                // Déjà validé par le webhook POST → succès
                return redirect()->route('payment.success')
                    ->with('success', 'Paiement validé avec succès !');
            }

            if ($payment && $payment->status === 'pending') {
                // Le webhook n'est pas encore arrivé → on vérifie directement via l'API Paystack
                $logger->info('Paystack – webhook pas encore reçu, vérification API', ['reference' => $reference]);
                $verified = $this->verifyPaystackTransaction($reference, $logger);

                if ($verified) {
                    $this->validateAndFinalize($payment, $verified['id'], $logger);
                    return redirect()->route('payment.success')
                        ->with('success', 'Paiement validé avec succès !');
                }

                return redirect()->route('payment.pending')
                    ->with('info', 'Paiement en cours de confirmation...');
            }

            return redirect()->route('payment.failed')
                ->with('danger', 'Transaction introuvable.');
        }

        // ── POST : webhook Paystack ────────────────────────────────────────
        // Vérification de la signature HMAC
        $secretKey = env('PAYSTACK_SECRET_KEY');
        if ($secretKey) {
            $hash = hash_hmac('sha512', $request->getContent(), $secretKey);
            if ($hash !== $request->header('x-paystack-signature')) {
                $logger->warning('Paystack – signature HMAC invalide');
                return response('Unauthorized', 401);
            }
        }

        $payload    = $request->json()->all();
        $event      = $payload['event'] ?? null;
        $reference  = $payload['data']['reference'] ?? null;
        $paystackId = $payload['data']['id'] ?? null;
        $status     = $payload['data']['status'] ?? null;

        $logger->info('Paystack webhook', [
            'event'     => $event,
            'reference' => $reference,
            'status'    => $status,
        ]);

        // Seul l'événement qui nous intéresse
        if ($event !== 'charge.success' || $status !== 'success' || !$reference) {
            $logger->info('Paystack – événement ignoré', ['event' => $event, 'status' => $status]);
            return response('OK', 200);
        }

        $payment = Payment::where('transaction_id', $reference)
            ->where('status', 'pending')
            ->first();

        if ($payment) {
            $logger->info('Paystack – paiement trouvé', ['id' => $payment->id, 'type' => $payment->payment_type]);
            $this->validateAndFinalize($payment, (string) $paystackId, $logger);
        } else {
            $logger->warning('Paystack – aucun paiement pending pour ref', ['reference' => $reference]);
        }

        return response('OK', 200);
    }

    // =========================================================================
    // VÉRIFICATION PAYSTACK VIA API (fallback si webhook pas encore reçu)
    // =========================================================================

    private function verifyPaystackTransaction(string $reference, $logger): ?array
    {
        $secretKey = env('PAYSTACK_SECRET_KEY');
        if (!$secretKey) return null;

        try {
            $resp = Http::withToken($secretKey)
                ->timeout(10)
                ->get("https://api.paystack.co/transaction/verify/{$reference}");

            $logger->info('Paystack verify API', ['reference' => $reference, 'status_code' => $resp->status()]);

            if ($resp->successful()) {
                $data   = $resp->json()['data'] ?? [];
                $status = $data['status'] ?? null;

                if ($status === 'success') {
                    $logger->info('Paystack verify – succès', ['id' => $data['id'] ?? null]);
                    return $data;
                }

                $logger->warning('Paystack verify – statut non succès', ['status' => $status]);
            }
        } catch (\Exception $e) {
            $logger->error('Paystack verify – exception', ['message' => $e->getMessage()]);
        }

        return null;
    }

    // =========================================================================
    // VALIDATION & FINALISATION  (commune à tous les providers)
    // =========================================================================

    private function validateAndFinalize(Payment $payment, ?string $providerRef = null, $logger = null): void
    {
        DB::transaction(function () use ($payment, $providerRef, $logger) {

            // Verrou en base pour éviter la double validation (webhook + redirect simultanés)
            $fresh = Payment::where('id', $payment->id)->lockForUpdate()->first();

            if (!$fresh || $fresh->status !== 'pending') {
                if ($logger) {
                    $logger->warning('Paiement déjà traité ou introuvable – abandon double validation', [
                        'id'     => $payment->id,
                        'status' => $fresh->status ?? 'NULL',
                    ]);
                }
                return;
            }

            // Mise à jour du statut
            $details = $fresh->payment_details ?? [];
            if ($providerRef) {
                $details['provider_ref'] = $providerRef;
            }

            $fresh->update([
                'status'          => 'completed',
                'payment_details' => $details,
            ]);

            if ($logger) {
                $logger->info('✅ Paiement validé en base', [
                    'id'             => $fresh->id,
                    'transaction_id' => $fresh->transaction_id,
                    'payment_type'   => $fresh->payment_type,
                    'amount'         => $fresh->amount,
                    'provider_ref'   => $providerRef,
                ]);
            }

            // Délégation à PaymentService : crée le Purchase ou active l'Abonnement + notifications
            $this->paymentService->finalizePurchase($fresh);
        });
    }
}