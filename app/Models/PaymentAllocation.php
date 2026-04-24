<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class PaymentAllocation extends Model {
    use BelongsToTenant;
    protected $fillable = ['tenant_id','payment_id','invoice_id','allocated_amount'];
    public function payment() { return $this->belongsTo(Payment::class)->withoutGlobalScopes(); }
    public function invoice() { return $this->belongsTo(Invoice::class)->withoutGlobalScopes(); }
}
