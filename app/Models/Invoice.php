<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Invoice extends Model {
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id','invoice_number','customer_id','customer_service_id','billing_month',
        'invoice_type','issue_date','due_date','subtotal','previous_due','discount_amount',
        'adjustment_amount','total_amount','paid_amount','due_amount','status','notes','generated_by'
    ];

    protected $casts = ['issue_date' => 'date', 'due_date' => 'date'];

    public function customer() { return $this->belongsTo(Customer::class)->withoutGlobalScopes(); }
    public function customerService() { return $this->belongsTo(CustomerService::class)->withoutGlobalScopes(); }
    public function items() { return $this->hasMany(InvoiceItem::class); }
    public function allocations() { return $this->hasMany(PaymentAllocation::class); }
    public function generator() { return $this->belongsTo(User::class, 'generated_by'); }

    public function getStatusColorAttribute(): string {
        return match($this->status) {
            'paid' => 'green',
            'partially_paid' => 'blue',
            'unpaid' => 'red',
            'draft' => 'gray',
            'waived' => 'purple',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }
}
