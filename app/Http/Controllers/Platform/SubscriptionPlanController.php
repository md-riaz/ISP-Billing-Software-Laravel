<?php
namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionPlanController extends Controller {
    public function index() {
        $plans = SubscriptionPlan::withCount('subscriptions')->orderBy('price_monthly')->get();
        return view('platform.plans.index', compact('plans'));
    }

    public function create() {
        return view('platform.plans.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_customers' => 'required|integer|min:1',
            'max_staff' => 'required|integer|min:1',
            'max_olt_devices' => 'required|integer|min:1',
            'max_sms_monthly' => 'required|integer|min:0',
            'has_reports' => 'boolean',
            'has_api' => 'boolean',
            'has_branding' => 'boolean',
        ]);

        $slug = Str::slug($validated['name']);
        $i = 1;
        while (SubscriptionPlan::where('slug', $slug)->exists()) {
            $slug = Str::slug($validated['name']) . '-' . $i++;
        }

        SubscriptionPlan::create(array_merge($validated, [
            'slug' => $slug,
            'has_reports' => $request->boolean('has_reports'),
            'has_api' => $request->boolean('has_api'),
            'has_branding' => $request->boolean('has_branding'),
            'is_active' => true,
        ]));

        return redirect()->route('platform.plans.index')->with('success', 'Plan created.');
    }

    public function show(SubscriptionPlan $plan) {
        return redirect()->route('platform.plans.edit', $plan);
    }

    public function edit(SubscriptionPlan $plan) {
        return view('platform.plans.edit', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_customers' => 'required|integer|min:1',
            'max_staff' => 'required|integer|min:1',
            'max_olt_devices' => 'required|integer|min:1',
            'max_sms_monthly' => 'required|integer|min:0',
            'has_reports' => 'boolean',
            'has_api' => 'boolean',
            'has_branding' => 'boolean',
            'is_active' => 'boolean',
        ]);
        $plan->update(array_merge($validated, [
            'has_reports' => $request->boolean('has_reports'),
            'has_api' => $request->boolean('has_api'),
            'has_branding' => $request->boolean('has_branding'),
            'is_active' => $request->boolean('is_active', true),
        ]));
        return redirect()->route('platform.plans.index')->with('success', 'Plan updated.');
    }

    public function destroy(SubscriptionPlan $plan) {
        $plan->delete();
        return redirect()->route('platform.plans.index')->with('success', 'Plan deleted.');
    }
}
