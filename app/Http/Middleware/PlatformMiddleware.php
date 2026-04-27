<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PlatformMiddleware {
    public function handle(Request $request, Closure $next) {
        $user = $request->user();
        if (!$user || !is_null($user->tenant_id)) {
            abort(403, 'Access denied. Platform admin only.');
        }
        return $next($request);
    }
}
