<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class Package extends Model {
    use SoftDeletes, BelongsToTenant;
    protected $fillable = [
        'tenant_id','package_code','package_name','speed_label','package_type',
        'monthly_price','description','is_active','service_profile_label','line_profile_label'
    ];
    protected $casts = ['is_active' => 'boolean'];
    public function customerServices() { return $this->hasMany(CustomerService::class); }
}
