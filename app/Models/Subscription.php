<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'duration_months',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_months' => 'integer',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function workerLimit(): int
    {
        return match (strtolower(trim($this->name))) {
            'basic' => 1,
            'standard' => 3,
            'premium' => 10,
            default => 0,
        };
    }
}
