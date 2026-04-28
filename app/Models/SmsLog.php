<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class SmsLog extends Model {
    use BelongsToTenant;
    protected $fillable = ['tenant_id','customer_id','phone','message','status','provider_response','sent_at'];
    protected $casts = ['sent_at' => 'datetime'];
    public function customer() { return $this->belongsTo(Customer::class)->withoutGlobalScopes(); }
}
