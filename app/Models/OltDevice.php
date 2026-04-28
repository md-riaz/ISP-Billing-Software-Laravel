<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class OltDevice extends Model {
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id','device_name','vendor','model','base_url','ip_address','port',
        'auth_type','username','password','api_token','area_id','pop_id','status','notes','last_synced_at'
    ];

    protected $casts = [
        'password' => 'encrypted',
        'api_token' => 'encrypted',
        'last_synced_at' => 'datetime',
    ];

    protected $hidden = ['password','api_token'];

    public function area() { return $this->belongsTo(Area::class)->withoutGlobalScopes(); }
    public function pop() { return $this->belongsTo(Pop::class)->withoutGlobalScopes(); }
    public function syncedOnus() { return $this->hasMany(SyncedOnu::class); }
    public function actionLogs() { return $this->hasMany(OltActionLog::class); }
    public function customerServices() { return $this->hasMany(CustomerService::class); }
}
