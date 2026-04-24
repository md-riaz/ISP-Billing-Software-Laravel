<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Setting extends Model {
    use BelongsToTenant;
    protected $fillable = ['tenant_id','key','value'];

    public static function get(string $key, mixed $default = null): mixed {
        if (!app()->bound('currentTenant')) return $default;
        $tenantId = app('currentTenant')->id;
        return static::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('key', $key)
            ->value('value') ?? $default;
    }

    public static function set(string $key, mixed $value): void {
        if (!app()->bound('currentTenant')) return;
        $tenantId = app('currentTenant')->id;
        static::withoutGlobalScopes()->updateOrCreate(
            ['tenant_id' => $tenantId, 'key' => $key],
            ['value' => $value]
        );
    }
}
