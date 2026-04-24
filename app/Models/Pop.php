<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class Pop extends Model {
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','area_id','name','location','description'];
    public function area() { return $this->belongsTo(Area::class); }
    public function customers() { return $this->hasMany(Customer::class); }
    public function oltDevices() { return $this->hasMany(OltDevice::class); }
}
