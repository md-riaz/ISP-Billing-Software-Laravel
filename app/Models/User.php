<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = ['tenant_id','name','email','phone','password','status','last_login_at'];

    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function isPlatformAdmin(): bool { return is_null($this->tenant_id); }
    public function isActive(): bool { return $this->status === 'active'; }
}
