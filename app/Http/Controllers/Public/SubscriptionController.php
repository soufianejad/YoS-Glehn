<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\NotificationService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    protected $notificationService;
    protected $paymentService;

    public function __construct(NotificationService $notificationService, PaymentService $paymentService)
    {
        $this->notificationService = $notificationService;
        $this->paymentService      = $paymentService;
    }

    // =========================================================================
    // PAGES
    // =========================================================================

    public function index()
    {
        $user         = auth()->user();
        $subscription = $user->activeSubscription;
        $history      = $user->subscriptions()->with('subscriptionPlan')->latest()->paginate(10);
        $plans        = SubscriptionPlan::where('is_active', true)->orderBy('order')->get(); // Fetch available plans
        $currentSubscription = $user->activeSubscription; // Fetch current subscription again for highlighting in plans list

        return view('subscription.index', compact('subscription', 'history', 'plans', 'currentSubscription'));
    }

    public function plans()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('order')->get();
        $currentSubscription = auth()->check() ? auth()->user()->activeSubscription : null;

        return view('subscription.plans', compact('plans', 'currentSubscription'));
    }

    public function checkout(SubscriptionPlan $plan)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', __('You must be logged in to subscribe.'));
        }

        $methods = $this->paymentService->getAvailablePaymentMethods();
        $type    = 'subscription';
        $price   = $plan->price;

        return view('subscription.checkout', compact('plan', 'methods', 'type', 'price'));
    }

    public function checkoutRenew()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', __('You must be logged in to renew.'));
        }

        $user         = auth()->user();
        $subscription = $user->activeSubscription()->first() ?? $user->subscriptions()->latest()->first();

        if (!$subscription) {
            return redirect()->route('subscription.plans')->with('error', __('No subscription found to renew.'));
        }

        $plan    = $subscription->subscriptionPlan;
        $methods = $this->paymentService->getAvailablePaymentMethods();
        $type    = 'renewal';
        $price   = $plan->price;

        return view('subscription.checkout', compact('plan', 'methods', 'type', 'price', 'subscription'));
    }

    // =========================================================================
    // INITIATION DU PAIEMENT
    // =========================================================================

    public function subscribe(Request $request, SubscriptionPlan $plan)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', __('You must be logged in to subscribe to a plan.'));
        }

        $request->validate([
            'phone'   => 'required|string',
            'network' => 'required|string',
        ]);

        $user = auth()->user();

        // 1. Déterminer la date de début (extension ou nouveau)
        $activeSub = $user->activeSubscription;
        $startDate = $activeSub ? $activeSub->end_date->addDay() : now();

        // 2. Création de l'abonnement en attente
        $subscription = Subscription::create([
            'user_id'              => $user->id,
            'subscription_plan_id' => $plan->id,
            'start_date'           => $startDate,
            'end_date'             => $startDate->copy()->addDays($plan->duration_days),
            'status'               => 'pending',
            'auto_renew'           => true,
        ]);

        // 2. Création du paiement en attente
        $payment = Payment::create([
            'user_id'          => $user->id,
            'subscription_id'  => $subscription->id,
            'transaction_id'   => 'SUB-' . strtoupper(Str::random(10)),
            'payment_type'     => 'subscription',
            'amount'           => $plan->price,
            'currency'         => 'XOF',
            'payment_method'   => $request->network === 'CARD' ? 'card' : 'mobile_money',
            'payment_provider' => $request->network,
            'status'           => 'pending',
            'payment_details'  => ['plan_id' => $plan->id],
        ]);

        // 3. Initiation du paiement
        return $this->paymentService->initiatePayment($request, $payment, false, route('subscription.index'));
    }

    public function renew(Request $request)
    {
        $user         = auth()->user();
        $subscription = $user->activeSubscription()->first() ?? $user->subscriptions()->latest()->first();

        if (!$subscription) {
            return back()->with('error', __('No subscription found to renew.'));
        }

        $request->validate([
            'phone'   => 'required|string',
            'network' => 'required|string',
        ]);

        $payment = Payment::create([
            'user_id'          => $user->id,
            'subscription_id'  => $subscription->id,
            'transaction_id'   => 'RENEW-' . strtoupper(Str::random(10)),
            'payment_type'     => 'subscription_renewal',
            'amount'           => $subscription->subscriptionPlan->price,
            'currency'         => 'XOF',
            'payment_method'   => $request->network === 'CARD' ? 'card' : 'mobile_money',
            'payment_provider' => $request->network,
            'status'           => 'pending',
            'payment_details'  => ['subscription_id' => $subscription->id],
        ]);

        return $this->paymentService->initiatePayment($request, $payment, false, route('subscription.index'));
    }

    // =========================================================================
    // CALLBACKS PAIEMENT
    // =========================================================================

    /**
     * Point d'entrée unique pour tous les callbacks de prestataires.
     * Route : payment.callback → /payment/callback/{service}
     *
     * Note : Si BookController gère aussi les callbacks via la même route,
     * ce contrôleur peut être supprimé et tout centraliser dans BookController.
     * En attendant, cette méthode réplique la même logique proprement.
     */
    public function paymentCallback(Request $request, string $service)
    {
        Log::channel('payment')->info("Subscription – Callback reçu – service : {$service}", [
            'ip'   => $request->ip(),
            'body' => $request->getContent(),
        ]);

        // ------------------------------------------------------------------
        // WAVE
        // ------------------------------------------------------------------
        if ($service === 'wave') {
            $data      = $request->json()->all();
            $sessionId = $data['data']['id'] ?? null;

            if (!$sessionId) {
                return response('OK', 200);
            }

            $payment = Payment::where(function ($q) use ($sessionId) {
                $q->where('transaction_id', $sessionId)
                  ->orWhereJsonContains('payment_details->wave_id', $sessionId);
            })->where('status', 'pending')
              ->whereIn('payment_type', ['subscription', 'subscription_renewal'])
              ->first();

            if ($payment && ($data['data']['payment_status'] ?? '') === 'succeeded') {
                $this->validateAndFinalize($payment);
            }

            return response('OK', 200);
        }

        // ------------------------------------------------------------------
        // TOUCHPAY
        // ------------------------------------------------------------------
        if ($service === 'touchpay') {
            $body      = $request->json()->all();
            $reference = $body['partner_transaction_id'] ?? null;

            if (!$reference) {
                return response('OK', 200);
            }

            $payment = Payment::where('transaction_id', $reference)
                ->where('status', 'pending')
                ->whereIn('payment_type', ['subscription', 'subscription_renewal'])
                ->first();

            if ($payment && strtoupper($body['status'] ?? '') === 'SUCCESSFUL') {
                $this->validateAndFinalize($payment, $body['gu_transaction_id'] ?? null);
            }

            return response('OK', 200);
        }

        // ------------------------------------------------------------------
        // PAWAPAY
        // ------------------------------------------------------------------
        if ($service === 'pawapay') {
            $body      = $request->json()->all();
            $depositId = $body['depositId'] ?? null;

            if (!$depositId) {
                return response('OK', 200);
            }

            $payment = Payment::where('transaction_id', $depositId)
                ->where('status', 'pending')
                ->whereIn('payment_type', ['subscription', 'subscription_renewal'])
                ->first();

            if ($payment && strtoupper($body['status'] ?? '') === 'COMPLETED') {
                $this->validateAndFinalize($payment);
            }

            return response('OK', 200);
        }

        // ------------------------------------------------------------------
        // PAIEMENTPRO
        // ------------------------------------------------------------------
        if ($service === 'paiementpro') {
            $reference    = $request->input('referenceNumber');
            $responseCode = $request->input('responsecode');
            $payId        = $request->input('payId');

            if (!$reference) {
                return redirect()->route('home');
            }

            $payment = Payment::where('transaction_id', $reference)
                ->where('status', 'pending')
                ->whereIn('payment_type', ['subscription', 'subscription_renewal'])
                ->first();

            if ($payment && $responseCode == '0') {
                $this->validateAndFinalize($payment, $payId);

                return redirect()->route('subscription.index')
                    ->with('success', 'Abonnement activé avec succès !');
            }

            return redirect()->route('payment.failed')
                ->with('danger', 'Transaction refusée ou introuvable.');
        }

        return response('Service non géré', 404);
    }

    // =========================================================================
    // ANNULATION
    // =========================================================================

    public function cancel()
    {
        $user         = auth()->user();
        $subscription = $user->activeSubscription;

        if (!$subscription) {
            return back()->with('error', __('No active subscription to cancel.'));
        }

        $subscription->update([
            'status'       => 'cancelled',
            'auto_renew'   => false,
            'cancelled_at' => now(),
        ]);

        return back()->with('success', __('Subscription cancelled successfully.'));
    }

    // =========================================================================
    // HELPER : Validation + finalisation
    // =========================================================================

    private function validateAndFinalize(Payment $payment, ?string $providerRef = null): void
    {
        // Protection contre la double validation (verrou optimiste)
        $fresh = Payment::where('id', $payment->id)->lockForUpdate()->first();

        if (!$fresh || $fresh->status !== 'pending') {
            Log::warning("Paiement abonnement déjà traité ou introuvable", ['id' => $payment->id]);
            return;
        }

        $updateData = ['status' => 'completed'];
        if ($providerRef) {
            $updateData['payment_details'] = array_merge($fresh->payment_details ?? [], ['provider_ref' => $providerRef]);
        }

        $fresh->update($updateData);

        // Finalisation : activation de l'abonnement + notification
        $this->paymentService->finalizePurchase($fresh);

        Log::info("Abonnement activé après paiement validé", [
            'transaction_id' => $fresh->transaction_id,
            'payment_type'   => $fresh->payment_type,
        ]);
    }
}