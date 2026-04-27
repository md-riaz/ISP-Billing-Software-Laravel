<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSubscriptionPayment extends Model {
    protected $fillable = ['tenant_id','subscription_id','amount','payment_date','method','reference','note','recorded_by'];
    protected $casts = ['payment_date' => 'date'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function subscription() { return $this->belongsTo(TenantSubscription::class, 'subscription_id'); }
    public function recorder() { return $this->belongsTo(User::class, 'recorded_by'); }
}
