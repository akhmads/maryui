<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
