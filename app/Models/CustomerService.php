<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class CustomerService extends Model {
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id','customer_id','package_id','monthly_price','status',
        'start_date','end_date','olt_device_id','pon_port','onu_identifier',
        'onu_serial','onu_name','service_profile','line_profile','remote_reference'
    ];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function package() { return $this->belongsTo(Package::class)->withoutGlobalScopes(); }
    public function oltDevice() { return $this->belongsTo(OltDevice::class)->withoutGlobalScopes(); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function statusHistories() { return $this->hasMany(StatusHistory::class); }
    public function actionLogs() { return $this->hasMany(OltActionLog::class); }
}
