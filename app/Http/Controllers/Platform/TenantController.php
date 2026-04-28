<?php
namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\TenantSubscriptionPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantController extends Controller {
    public function index(Request $request) {
        $query = Tenant::with('subscription.plan');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name','like',"%$search%")->orWhere('email','like',"%$search%");
            });
        }
        if ($request->filled('status')) $query->where('status', $request->status);
        $tenants = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        return view('platform.tenants.index', compact('tenants'));
    }

    public function create() {
        $plans = SubscriptionPlan::where('is_active',true)->get();
        return view('platform.tenants.create', compact('plans'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email',
            'phone' => 'nullable|string|max:20',
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly,trial',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8',
            'trial_days' => 'nullable|integer|min:1',
        ]);

        $slug = Str::slug($validated['name']);
        $baseSlug = $slug;
        $i = 1;
        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }

        $tenant = Tenant::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => 'trial',
            'trial_ends_at' => now()->addDays($validated['trial_days'] ?? 14),
            'timezone' => 'Asia/Dhaka',
        ]);

        $plan = SubscriptionPlan::find($validated['plan_id']);
        $price = $validated['billing_cycle'] === 'yearly' ? $plan->price_yearly : $plan->price_monthly;

        TenantSubscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'billing_cycle' => $validated['billing_cycle'],
            'price' => $price,
            'starts_at' => now(),
            'expires_at' => match ($validated['billing_cycle']) {
                'trial'  => now()->addDays($validated['trial_days'] ?? 14),
                'yearly' => now()->addYear(),
                default  => now()->addMonth(),
            },
            'status' => 'trial',
        ]);

        User::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['admin_name'],
            'email' => $validated['admin_email'],
            'password' => Hash::make($validated['admin_password']),
            'status' => 'active',
        ]);

        return redirect()->route('platform.tenants.show', $tenant)->with('success', 'Tenant created successfully.');
    }

    public function show(Tenant $tenant) {
        $tenant->load('subscriptions.plan','users');
        $tenant->loadCount('customers');
        return view('platform.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant) {
        $plans = SubscriptionPlan::where('is_active',true)->get();
        return view('platform.tenants.edit', compact('tenant','plans'));
    }

    public function update(Request $request, Tenant $tenant) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:trial,active,suspended,past_due,cancelled',
            'timezone' => 'nullable|string|max:50',
        ]);
        $tenant->update($validated);
        return redirect()->route('platform.tenants.show', $tenant)->with('success', 'Tenant updated.');
    }

    public function destroy(Tenant $tenant) {
        $tenant->delete();
        return redirect()->route('platform.tenants.index')->with('success', 'Tenant deleted.');
    }

    public function recordPayment(Request $request, Tenant $tenant) {
        $validated = $request->validate([
            'subscription_id' => 'required|exists:tenant_subscriptions,id',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'method' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:100',
            'note' => 'nullable|string',
        ]);

        TenantSubscriptionPayment::create(array_merge($validated, [
            'tenant_id' => $tenant->id,
            'recorded_by' => auth()->id(),
        ]));

        // Activate tenant if trial
        if ($tenant->status === 'trial') {
            $tenant->update(['status' => 'active']);
            TenantSubscription::find($validated['subscription_id'])->update(['status' => 'active']);
        }

        return back()->with('success', 'Payment recorded.');
    }
}
