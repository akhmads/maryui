<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesInvoiceDetail extends Model
{
    protected $table = 'sales_invoice_detail';
    protected $guarded = [];

    public function items(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}
