<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantMiddleware {
    public function handle(Request $request, Closure $next) {
        $user = $request->user();
        if (!$user || is_null($user->tenant_id)) {
            return redirect()->route('login');
        }
        $tenant = $user->tenant;
        if (!$tenant) {
            abort(403, 'Tenant not found.');
        }
        if (!$tenant->isActive()) {
            abort(403, 'Your account is ' . $tenant->status . '. Please contact support.');
        }
        app()->instance('currentTenant', $tenant);
        return $next($request);
    }
}
