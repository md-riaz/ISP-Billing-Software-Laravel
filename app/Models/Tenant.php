<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = ['name','slug','email','phone','logo','status','trial_ends_at','timezone'];
    protected $casts = ['trial_ends_at' => 'datetime'];

    public function users() { return $this->hasMany(User::class); }
    public function subscription() { return $this->hasOne(TenantSubscription::class)->latest(); }
    public function subscriptions() { return $this->hasMany(TenantSubscription::class); }
    public function customers() { return $this->hasMany(Customer::class); }
    public function packages() { return $this->hasMany(Package::class); }
    public function areas() { return $this->hasMany(Area::class); }
    public function pops() { return $this->hasMany(Pop::class); }
    public function oltDevices() { return $this->hasMany(OltDevice::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function settings() { return $this->hasMany(Setting::class); }

    public function getSetting(string $key, mixed $default = null): mixed {
        return $this->settings()->where('key', $key)->value('value') ?? $default;
    }

    public function isActive(): bool {
        return in_array($this->status, ['active','trial']);
    }
}
