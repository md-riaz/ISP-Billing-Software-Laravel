<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class OltActionLog extends Model {
    use BelongsToTenant;
    protected $fillable = ['tenant_id','olt_device_id','customer_service_id','action_type','payload','response','status','error_message','executed_by'];
    protected $casts = ['payload' => 'array', 'response' => 'array'];
    public function oltDevice() { return $this->belongsTo(OltDevice::class)->withoutGlobalScopes(); }
    public function customerService() { return $this->belongsTo(CustomerService::class)->withoutGlobalScopes(); }
    public function executor() { return $this->belongsTo(User::class, 'executed_by'); }
}
