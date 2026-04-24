<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ResolveTenant {
    public function handle(Request $request, Closure $next) {
        $user = $request->user();
        if ($user && $user->tenant_id) {
            $tenant = $user->tenant;
            if ($tenant) {
                app()->instance('currentTenant', $tenant);
            }
        }
        return $next($request);
    }
}
