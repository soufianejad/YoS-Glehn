<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
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

    public function subscribe(Request $request, SubscriptionPlan $plan)
{
    if (!auth()->check()) {
        return redirect()->route('login')->with('error', 'You must be logged in to subscribe to a plan.');
    }

    if (!$plan) {
        return back()->with('error', 'Subscription plan not found.');
    }

    $user = auth()->user();

    // 1️⃣ Create the subscription first
    $subscription = Subscription::create([
        'user_id' => $user->id,
        'subscription_plan_id' => $plan->id,
        'start_date' => now(),
        'end_date' => now()->addDays($plan->duration_days),
        'status' => 'active',
        'auto_renew' => true,
    ]);

    // 2️⃣ Create a payment and attach subscription_id
    $payment = Payment::create([
        'user_id' => $user->id,
        'subscription_id' => $subscription->id, // ⬅️ ADD THIS FIELD
        'transaction_id' => 'TRX-' . uniqid(),
        'payment_type' => 'subscription',
        'amount' => $plan->price,
        'currency' => 'USD',
        'payment_method' => 'simulated',
        'payment_provider' => 'simulated',
        'status' => 'completed',
        'paid_at' => now(),
    ]);

    // Send notification
    $this->notificationService->sendNotification(
        $user,
        'Abonnement activé',
        "Félicitations ! Vous êtes maintenant abonné(e) au plan '{$plan->name}'.",
        route('library.index'),
        'success'
    );

    return redirect()->route('subscription.index')->with('success', 'Subscription successful!');
}


    public function renew()
    {
        $user = auth()->user();
        $subscription = $user->subscription;

        if (! $subscription || ! $subscription->isActive()) {
            return back()->with('error', 'No active subscription to renew.');
        }

        // In a real application, this would integrate with a payment gateway.
        // For now, we'll simulate a successful payment and extend the subscription.

        // Create a dummy payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'transaction_id' => 'TRX-'.uniqid(),
            'payment_type' => 'subscription',
            'amount' => $subscription->subscriptionPlan->price,
            'currency' => 'USD',
            'payment_method' => 'simulated',
            'payment_provider' => 'simulated',
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        $subscription->update([
            'end_date' => $subscription->end_date->addDays($subscription->subscriptionPlan->duration_days),
            'status' => 'active',
            'cancelled_at' => null,
        ]);

        return back()->with('success', 'Subscription renewed successfully!');
    }

    public function cancel()
    {
        $user = auth()->user();
        $subscription = $user->subscription;

        if (! $subscription || ! $subscription->isActive()) {
            return back()->with('error', 'No active subscription to cancel.');
        }

        $subscription->update([
            'status' => 'cancelled',
            'auto_renew' => false,
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'Subscription cancelled successfully.');
    }
}
