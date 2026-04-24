<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class SyncedOnu extends Model {
    use BelongsToTenant;
    protected $fillable = ['tenant_id','olt_device_id','onu_identifier','onu_serial','onu_name','pon_port','status','signal_level','last_seen_at','raw_data'];
    protected $casts = ['last_seen_at' => 'datetime', 'raw_data' => 'array'];
    public function oltDevice() { return $this->belongsTo(OltDevice::class)->withoutGlobalScopes(); }
}
