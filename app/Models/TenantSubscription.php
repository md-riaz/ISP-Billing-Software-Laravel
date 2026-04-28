<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSubscription extends Model {
    protected $fillable = ['tenant_id','plan_id','billing_cycle','price','starts_at','expires_at','status','notes'];
    protected $casts = ['starts_at' => 'datetime', 'expires_at' => 'datetime'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function plan() { return $this->belongsTo(SubscriptionPlan::class, 'plan_id'); }
    public function payments() { return $this->hasMany(TenantSubscriptionPayment::class, 'subscription_id'); }
}
