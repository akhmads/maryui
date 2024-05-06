<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Helpers\Cast;

class SalesInvoice extends Model
{
    protected $table = 'sales_invoice';
    protected $guarded = [];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function details(): HasMany
    {
        return $this->HasMany(SalesInvoiceDetail::class,'sales_invoice_id','id');
    }

    public function getTotalAttribute()
    {
        return Cast::currency($this->attributes['total_invoice']);
    }

    public function getQtyAttribute()
    {
        return Cast::currency($this->attributes['total_qty']);
    }
}
