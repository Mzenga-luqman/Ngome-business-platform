<?php

namespace App\Models;

use App\Models\Concerns\BelongsToAccount;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use BelongsToAccount;

    protected $fillable = ['account_owner_id', 'name', 'amount', 'date', 'notes'];

    protected $casts = [
        'account_owner_id' => 'integer',
        'amount' => 'decimal:2',
        'date'   => 'date',
    ];
}
