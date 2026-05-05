<?php

namespace App\Models;

use App\Models\Concerns\BelongsToAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Creditor extends Model
{
    use BelongsToAccount;

    public $timestamps = false;

    protected $fillable = [
        'account_owner_id',
        'user_id',
        'sale_id',
        'customer_name',
        'customer_phone',
        'quantity',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'total',
        'status',
        'created_at',
        'paid_at',
    ];

    protected $casts = [
        'account_owner_id' => 'integer',
        'user_id' => 'integer',
        'sale_id' => 'integer',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(CreditorItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
