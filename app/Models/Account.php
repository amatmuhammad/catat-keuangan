<?php

namespace App\Models;

use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'owner_id'])]
class Account extends Model
{
    /** @use HasFactory<AccountFactory> */
    use HasFactory;

    public const MAX_MEMBERS = 5;

    public const ROLE_OWNER = 'owner';

    public const ROLE_MEMBER = 'member';

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<Wallet, $this>
     */
    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    /**
     * @return HasMany<Category, $this>
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Dompet yang boleh dilihat anggota: dompet bersama dan dompet pribadinya.
     *
     * @return HasMany<Wallet, $this>
     */
    public function visibleWallets(User $user): HasMany
    {
        return $this->wallets()->visibleTo($user);
    }

    /**
     * @return HasMany<Wallet, $this>
     */
    public function sharedWallets(): HasMany
    {
        return $this->wallets()->where('is_shared', true);
    }

    /**
     * @return HasMany<Wallet, $this>
     */
    public function personalWalletsFor(User $user): HasMany
    {
        return $this->wallets()
            ->where('is_shared', false)
            ->where('user_id', $user->id);
    }

    /**
     * @return HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Transaksi bersama dan transaksi pribadi milik anggota ini.
     *
     * @return HasMany<Transaction, $this>
     */
    public function visibleTransactions(User $user): HasMany
    {
        return $this->transactions()->whereHas(
            'wallet',
            fn (Builder $query) => $query->visibleTo($user),
        );
    }

    /**
     * @return HasMany<ActivityLog, $this>
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isOwner(User $user): bool
    {
        return (int) $this->owner_id === (int) $user->id;
    }

    public function hasMember(User $user): bool
    {
        return $this->members()->whereKey($user->id)->exists();
    }

    public function canAddMember(): bool
    {
        return $this->members()->count() < self::MAX_MEMBERS;
    }

    public function memberCount(): int
    {
        return $this->members()->count();
    }
}
