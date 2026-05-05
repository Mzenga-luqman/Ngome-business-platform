<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditorItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'creditor_id',
        'product_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'creditor_id' => 'integer',
        'product_id' => 'integer',
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];

    public function creditor(): BelongsTo
    {
        return $this->belongsTo(Creditor::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
