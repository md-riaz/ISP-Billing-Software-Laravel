<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class SmsTemplate extends Model {
    use BelongsToTenant;
    protected $fillable = ['tenant_id','event_type','template_name','message_body','variables','is_active'];
    protected $casts = ['variables' => 'array', 'is_active' => 'boolean'];
}
