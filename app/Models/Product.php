<?php

namespace App\Models;

use App\Models\Concerns\BelongsToAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use BelongsToAccount;

    protected $fillable = ['account_owner_id', 'name', 'barcode', 'price', 'quantity', 'image_path'];

    protected $appends = ['image_url'];

    protected $casts = [
        'account_owner_id' => 'integer',
        'price'    => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function isLowStock(int $threshold = 5): bool
    {
        return $this->quantity <= $threshold;
    }

    public function hasManagedImage(): bool
    {
        return is_string($this->image_path)
            && str_starts_with($this->image_path, 'products/')
            && ! str_contains($this->image_path, '..')
            && Storage::disk('public')->exists($this->image_path);
    }

    public function setNameAttribute(string $value): void
    {
        $value = trim(strip_tags($value));
        $value = preg_replace('/\s+/', ' ', $value) ?: '';

        $this->attributes['name'] = $value;
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->hasManagedImage()) {
            return asset('storage/' . $this->image_path);
        }

        return asset('images/product-placeholder.svg');
    }
}
