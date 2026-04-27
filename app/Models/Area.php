<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class Area extends Model {
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','name','description'];
    public function pops() { return $this->hasMany(Pop::class); }
    public function customers() { return $this->hasMany(Customer::class); }
}
