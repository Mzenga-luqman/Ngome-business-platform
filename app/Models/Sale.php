<?php

namespace App\Models;

use App\Models\Concerns\BelongsToAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use BelongsToAccount;

    public $timestamps = false;

    protected $fillable = [
        'account_owner_id',
        'product_id',
        'user_id',
        'quantity',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'total',
        'sold_at',
    ];

    protected $casts = [
        'account_owner_id' => 'integer',
        'product_id' => 'integer',
        'subtotal' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total'   => 'decimal:2',
        'sold_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}
