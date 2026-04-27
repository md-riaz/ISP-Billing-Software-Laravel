<?php
namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\TenantSubscriptionPayment;
use App\Models\User;

class PlatformDashboardController extends Controller {
    public function index() {
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status','active')->count(),
            'trial_tenants' => Tenant::where('status','trial')->count(),
            'suspended_tenants' => Tenant::where('status','suspended')->count(),
            'monthly_revenue' => TenantSubscriptionPayment::whereMonth('payment_date', now()->month)->sum('amount'),
        ];

        $recentTenants = Tenant::with('subscription.plan')->orderByDesc('created_at')->limit(5)->get();

        return view('platform.dashboard', compact('stats','recentTenants'));
    }
}
