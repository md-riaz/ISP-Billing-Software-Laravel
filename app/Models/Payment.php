<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Payment extends Model {
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id','payment_number','customer_id','payment_date','amount','method',
        'transaction_reference','collector_id','note','status','reversed_at','reversed_by','reversal_reason'
    ];

    protected $casts = ['payment_date' => 'date', 'reversed_at' => 'datetime'];

    public function customer() { return $this->belongsTo(Customer::class)->withoutGlobalScopes(); }
    public function collector() { return $this->belongsTo(User::class, 'collector_id'); }
    public function reverser() { return $this->belongsTo(User::class, 'reversed_by'); }
    public function allocations() { return $this->hasMany(PaymentAllocation::class); }
}
