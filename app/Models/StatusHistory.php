<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class StatusHistory extends Model {
    use BelongsToTenant;
    protected $fillable = ['tenant_id','customer_service_id','old_status','new_status','reason','changed_by','olt_action_log_id'];
    public function customerService() { return $this->belongsTo(CustomerService::class)->withoutGlobalScopes(); }
    public function changer() { return $this->belongsTo(User::class, 'changed_by'); }
    public function oltActionLog() { return $this->belongsTo(OltActionLog::class)->withoutGlobalScopes(); }
}
