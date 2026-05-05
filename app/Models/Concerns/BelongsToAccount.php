<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToAccount
{
    public function scopeForAccount(Builder $query, User|int|null $account): Builder
    {
        $ownerId = $account instanceof User ? $account->accountOwnerId() : $account;

        if (! $ownerId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(
            $query->getModel()->qualifyColumn('account_owner_id'),
            $ownerId
        );
    }

    public function accountOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_owner_id');
    }
}
