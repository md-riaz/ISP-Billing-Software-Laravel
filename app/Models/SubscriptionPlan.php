<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model {
    protected $fillable = [
        'name','slug','price_monthly','price_yearly','max_customers','max_staff',
        'max_olt_devices','max_sms_monthly','has_reports','has_api','has_branding','is_active'
    ];
    protected $casts = [
        'has_reports' => 'boolean',
        'has_api' => 'boolean',
        'has_branding' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function subscriptions() { return $this->hasMany(TenantSubscription::class, 'plan_id'); }
}
