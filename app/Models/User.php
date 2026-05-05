<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

#[Fillable([
    'name',
    'username',
    'email',
    'password',
    'profile_photo_path',
    'account_owner_id',
    'subscription_id',
    'subscription_expiry',
    'is_admin',
    'is_worker',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'account_owner_id' => 'integer',
            'subscription_expiry' => 'datetime',
            'is_admin' => 'boolean',
            'is_worker' => 'boolean',
        ];
    }

    public function accountOwner(): BelongsTo
    {
        return $this->belongsTo(self::class, 'account_owner_id');
    }

    public function workerAccounts(): HasMany
    {
        return $this->hasMany(self::class, 'account_owner_id')
            ->where('is_worker', true)
            ->orderBy('name');
    }

    public function scopeWithinAccount(Builder $query, User|int|null $account): Builder
    {
        $ownerId = $account instanceof self ? $account->accountOwnerId() : $account;

        if (! $ownerId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $builder) use ($ownerId) {
            $builder->whereKey($ownerId)
                ->orWhere('account_owner_id', $ownerId);
        });
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function accountOwnerId(): int
    {
        return (int) (($this->is_worker && $this->account_owner_id)
            ? $this->account_owner_id
            : $this->id);
    }

    public function subscriptionAccount(): self
    {
        if ($this->is_worker && $this->accountOwner) {
            return $this->accountOwner;
        }

        return $this;
    }

    public function canManageWorkers(): bool
    {
        return ! $this->is_admin && ! $this->is_worker;
    }

    public function workerLimit(): int
    {
        $account = $this->subscriptionAccount();

        return $account->subscription?->workerLimit() ?? 0;
    }

    public function workerCount(): int
    {
        $account = $this->subscriptionAccount();

        return $account->workerAccounts()->count();
    }

    public function remainingWorkerSlots(): int
    {
        return max(0, $this->workerLimit() - $this->workerCount());
    }

    public function hasActiveSubscription(): bool
    {
        if ($this->is_admin) {
            return true;
        }

        $account = $this->subscriptionAccount();

        return $account->subscription_id !== null
            && $account->subscription_expiry !== null
            && $account->subscription_expiry->isFuture();
    }

    public function remainingSubscriptionDays(): int
    {
        $account = $this->subscriptionAccount();

        if (! $account->subscription_expiry) {
            return 0;
        }

        return max(0, Carbon::now()->diffInDays($account->subscription_expiry, false));
    }
}
