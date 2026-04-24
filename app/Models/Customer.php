<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class Customer extends Model {
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id','customer_code','full_name','company_name','customer_type',
        'primary_phone','secondary_phone','email','nid','address_line',
        'area_id','pop_id','thana','district','postal_code',
        'connection_date','activation_date','status',
        'discount_type','discount_value','opening_due','installation_charge',
        'billing_note','assigned_collector_id','assigned_technician_id'
    ];

    protected $casts = [
        'connection_date' => 'date',
        'activation_date' => 'date',
        'discount_value' => 'decimal:2',
        'opening_due' => 'decimal:2',
        'installation_charge' => 'decimal:2',
    ];

    public function area() { return $this->belongsTo(Area::class)->withoutGlobalScopes(); }
    public function pop() { return $this->belongsTo(Pop::class)->withoutGlobalScopes(); }
    public function services() { return $this->hasMany(CustomerService::class); }
    public function activeService() { return $this->hasOne(CustomerService::class)->where('status','active'); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function collector() { return $this->belongsTo(User::class, 'assigned_collector_id'); }
    public function technician() { return $this->belongsTo(User::class, 'assigned_technician_id'); }

    public function getStatusColorAttribute(): string {
        return match($this->status) {
            'active' => 'green',
            'pending_installation' => 'yellow',
            'temporary_hold' => 'orange',
            'suspended_due','suspended_manual' => 'red',
            'disconnected','terminated' => 'gray',
            default => 'gray'
        };
    }
}
