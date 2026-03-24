<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::paginate(10);

        return view('admin.subscription-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.subscription-plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subscription_plans,name',
            'description' => 'required|string',
            'type' => 'required|string|in:individual,school',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_students' => 'nullable|integer|min:0',
            'pdf_access' => 'boolean',
            'audio_access' => 'boolean',
            'download_access' => 'boolean',
            'quiz_access' => 'boolean',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        SubscriptionPlan::create($data);

        return redirect()->route('admin.subscription-plans.index')->with('success', 'Subscription plan created successfully.');
    }

    public function edit(SubscriptionPlan $plan)
    {
        return view('admin.subscription-plans.edit', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subscription_plans,name,'.$plan->id,
            'description' => 'required|string',
            'type' => 'required|string|in:individual,school',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_students' => 'nullable|integer|min:0',
            'pdf_access' => 'boolean',
            'audio_access' => 'boolean',
            'download_access' => 'boolean',
            'quiz_access' => 'boolean',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        $plan->update($data);

        return redirect()->route('admin.subscription-plans.index')->with('success', 'Subscription plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        $plan->delete();

        return redirect()->route('admin.subscription-plans.index')->with('success', 'Subscription plan deleted successfully.');
    }

    public function activate(SubscriptionPlan $plan)
    {
        $plan->update(['is_active' => true]);

        return back()->with('success', 'Subscription plan activated successfully.');
    }

    public function deactivate(SubscriptionPlan $plan)
    {
        $plan->update(['is_active' => false]);

        return back()->with('success', 'Subscription plan deactivated successfully.');
    }
}
