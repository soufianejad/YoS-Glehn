<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\School;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $school = $user->managedSchool;

        if (! $school) {
            // Using correct route name and providing meaningful error message
            return redirect()->route('school.dashboard')->with('error', 'Vous n\'êtes associé à aucune école.');
        }

        $subscription = $school->subscription;
        // Eager load plan details with subscription
        if ($subscription) {
            $subscription->load('subscriptionPlan');
        }

        $payments = $school->payments()->latest()->paginate(10);

        return view('school.subscription.index', compact('school', 'subscription', 'payments'));
    }

    /**
     * Show available subscription plans for schools.
     */
    public function showPlans()
    {
        $plans = \App\Models\SubscriptionPlan::where('type', 'school')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('school.subscription.plans', compact('plans'));
    }

    /**
     * Handle the subscription process for a school to a specific plan.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscribe(Request $request, \App\Models\SubscriptionPlan $plan)
    {
        $user = auth()->user();
        $school = $user->managedSchool;

        if (! $school) {
            return redirect()->route('school.dashboard')->with('error', 'Vous n\'êtes associé à aucune école.');
        }

        // Optional: Check if the school already has an active subscription
        if ($school->subscription && $school->subscription->isActive()) {
            // Logic to handle upgrade/downgrade could go here
            return redirect()->route('school.subscription.index')->with('info', 'Vous avez déjà un abonnement actif.');
        }

        // Create the new subscription
        $subscription = $school->subscription()->create([
            'user_id' => $school->user_id, // Correctly link the subscription to the school's associated user
            'subscription_plan_id' => $plan->id,
            'start_date' => now(), // Corrected column name
            'end_date' => now()->addDays($plan->duration_days), // Corrected column name
            'status' => 'active', // Assuming immediate activation after mock payment
            'price' => $plan->price,
            'max_students' => $plan->max_students,
        ]);

        // Link the new subscription to the school
        $school->update(['subscription_id' => $subscription->id]);

        // Simulate a payment record
        $school->payments()->create([
            'user_id' => $school->user_id, // Use school's user_id for the payment record
            'subscription_id' => $subscription->id,
            'amount' => $plan->price,
            'currency' => 'USD', // Or your default currency
            'status' => 'completed',
            'payment_method' => 'card', // Use an allowed enum value
            'transaction_id' => 'mock_'.uniqid(),
        ]);

        return redirect()->route('school.subscription.index')->with('success', 'Abonnement au plan "'.$plan->name.'" réussi !');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
