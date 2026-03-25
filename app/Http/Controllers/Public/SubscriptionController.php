<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\NotificationService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    protected $notificationService;
    protected $paymentService;

    public function __construct(NotificationService $notificationService, PaymentService $paymentService)
    {
        $this->notificationService = $notificationService;
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription; // Use the correct relationship for the active subscription

        return view('subscription.index', compact('subscription'));
    }

    public function plans()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('order')->get();

        return view('subscription.plans', compact('plans'));
    }

    public function checkout(SubscriptionPlan $plan)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', __('You must be logged in to subscribe.'));
        }

        $methods = $this->paymentService->getAvailablePaymentMethods();
        $type = 'subscription';
        $price = $plan->price;

        return view('subscription.checkout', compact('plan', 'methods', 'type', 'price'));
    }

    public function checkoutRenew()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', __('You must be logged in to renew.'));
        }

        $user = auth()->user();
        $subscription = $user->activeSubscription()->first() ?? $user->subscriptions()->latest()->first();

        if (! $subscription) {
            return redirect()->route('subscription.plans')->with('error', __('No subscription found to renew.'));
        }

        $plan = $subscription->subscriptionPlan;
        $methods = $this->paymentService->getAvailablePaymentMethods();
        $type = 'renewal';
        $price = $plan->price;

        return view('subscription.checkout', compact('plan', 'methods', 'type', 'price', 'subscription'));
    }

    public function subscribe(Request $request, SubscriptionPlan $plan)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', __('You must be logged in to subscribe to a plan.'));
        }

        $request->validate([
            'phone' => 'required|string',
            'network' => 'required|string',
        ]);

        $user = auth()->user();

        // 1. Create the subscription in pending status
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'start_date' => now(),
            'end_date' => now()->addDays($plan->duration_days),
            'status' => 'pending', // Pending payment
            'auto_renew' => true,
        ]);

        // 2. Create the payment in pending status
        $payment = Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'transaction_id' => 'SUB-' . strtoupper(Str::random(10)),
            'payment_type' => 'subscription',
            'amount' => $plan->price,
            'currency' => 'XOF',
            'payment_method' => $request->network,
            'payment_provider' => $request->network,
            'status' => 'pending',
            'payment_details' => ['plan_id' => $plan->id],
        ]);

        // 3. Initiate the real payment process
        return $this->paymentService->initiatePayment($request, $payment, false, route('subscription.index'));
    }


    public function renew(Request $request)
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription()->first() ?? $user->subscriptions()->latest()->first();

        if (! $subscription) {
            return back()->with('error', __('No subscription found to renew.'));
        }

        $request->validate([
            'phone' => 'required|string',
            'network' => 'required|string',
        ]);

        // Create a pending payment for the renewal
        $payment = Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'transaction_id' => 'RENEW-' . strtoupper(Str::random(10)),
            'payment_type' => 'subscription_renewal',
            'amount' => $subscription->subscriptionPlan->price,
            'currency' => 'XOF',
            'payment_method' => $request->network,
            'payment_provider' => $request->network,
            'status' => 'pending',
            'payment_details' => ['subscription_id' => $subscription->id],
        ]);

        return $this->paymentService->initiatePayment($request, $payment, false, route('subscription.index'));
    }

    public function cancel()
    {
        $user = auth()->user();
        $subscription = $user->subscription;

        if (! $subscription || ! $subscription->isActive()) {
            return back()->with('error', __('No active subscription to cancel.'));
        }

        $subscription->update([
            'status' => 'cancelled',
            'auto_renew' => false,
            'cancelled_at' => now(),
        ]);

        return back()->with('success', __('Subscription cancelled successfully.'));
    }
}
