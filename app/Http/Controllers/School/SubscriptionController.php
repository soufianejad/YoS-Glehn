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
        return redirect()->route('subscription.index');
    }

    /**
     * Show available subscription plans for schools.
     */
    public function showPlans()
    {
        return redirect()->route('subscription.plans');
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

        // Handle upgrade/downgrade
        if ($school->subscription && $school->subscription->isActive()) {
            $oldSubscription = $school->subscription;
            $oldSubscription->update(['status' => 'cancelled', 'cancelled_at' => now()]);
        }

        // Create the new subscription
        $subscription = Subscription::create([
            'user_id' => $school->user_id,
            'subscription_plan_id' => $plan->id,
            'start_date' => now(),
            'end_date' => now()->addDays($plan->duration_days),
            'status' => 'active',
            // Notice: models like 'price' or 'max_students' shouldn't be here unless added to fillable of the model.
            // In the DB, max_students should be updated in 'schools' directly
        ]);

        // Link the new subscription to the school and update student limits
        $school->update([
            'subscription_id' => $subscription->id,
            'max_students' => $plan->max_students
        ]);

        // Simulate a payment record
        Payment::create([
            'user_id' => $school->user_id,
            'subscription_id' => $subscription->id,
            'amount' => $plan->price,
            'currency' => __('XOF'), // Use local currency
            'status' => 'completed',
            'payment_method' => 'card',
            'transaction_id' => 'mock_'.uniqid(),
        ]);

        return redirect()->route('school.dashboard')->with('success', __('Abonnement au plan "').$plan->name.'" réussi !');
    }
}
