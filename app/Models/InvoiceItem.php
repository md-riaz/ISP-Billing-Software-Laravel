<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class InvoiceItem extends Model {
    use BelongsToTenant;
    protected $fillable = ['invoice_id','tenant_id','description','quantity','unit_price','amount'];
    public function invoice() { return $this->belongsTo(Invoice::class)->withoutGlobalScopes(); }
}
