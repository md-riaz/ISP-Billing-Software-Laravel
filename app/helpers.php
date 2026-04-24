<?php

if (!function_exists('taka')) {
    function taka(float|int|string $amount): string {
        return '৳' . number_format((float)$amount, 2);
    }
}

if (!function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed {
        return \App\Models\Setting::get($key, $default);
    }
}

if (!function_exists('currentTenant')) {
    function currentTenant(): ?\App\Models\Tenant {
        return app()->bound('currentTenant') ? app('currentTenant') : null;
    }
}

if (!function_exists('logActivity')) {
    function logActivity(string $action, string $entityType = null, mixed $entityId = null, array $oldValues = null, array $newValues = null): void {
        try {
            \App\Models\ActivityLog::create([
                'tenant_id' => app()->bound('currentTenant') ? app('currentTenant')->id : null,
                'user_id' => auth()->id(),
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Exception $e) {
            // Silent fail for logging
        }
    }
}
