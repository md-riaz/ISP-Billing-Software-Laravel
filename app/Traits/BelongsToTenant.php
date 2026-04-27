<?php
namespace App\Traits;

use App\Scopes\TenantScope;

trait BelongsToTenant {
    protected static function bootBelongsToTenant(): void {
        static::addGlobalScope(new TenantScope());
        static::creating(function ($model) {
            if (empty($model->tenant_id) && app()->bound('currentTenant')) {
                $model->tenant_id = app('currentTenant')->id;
            }
        });
    }

    public function tenant() {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}
