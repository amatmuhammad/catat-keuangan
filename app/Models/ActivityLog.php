<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'account_id',
    'user_id',
    'action',
    'description',
    'subject_type',
    'subject_id',
    'properties',
    'ip_address',
    'user_agent',
    'is_shared',
])]
class ActivityLog extends Model
{
    public const ACCOUNT_CREATED = 'account.created';

    public const MEMBER_ADDED = 'member.added';

    public const MEMBER_REMOVED = 'member.removed';

    public const WALLET_CREATED = 'wallet.created';

    public const WALLET_UPDATED = 'wallet.updated';

    public const WALLET_DELETED = 'wallet.deleted';

    public const CATEGORY_CREATED = 'category.created';

    public const CATEGORY_UPDATED = 'category.updated';

    public const CATEGORY_DELETED = 'category.deleted';

    public const TRANSACTION_CREATED = 'transaction.created';

    public const TRANSACTION_UPDATED = 'transaction.updated';

    public const TRANSACTION_DELETED = 'transaction.deleted';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'is_shared' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    public static function record(
        Account $account,
        ?User $actor,
        string $action,
        string $description,
        ?Model $subject = null,
        array $properties = [],
        bool $isShared = true,
    ): self {
        $request = app()->runningInConsole() ? null : request();

        return self::create([
            'account_id' => $account->id,
            'user_id' => $actor?->id,
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'properties' => $properties === [] ? null : $properties,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'is_shared' => $isShared,
        ]);
    }

    public function actionLabel(): string
    {
        return self::labelFor($this->action);
    }

    public static function labelFor(string $action): string
    {
        return match ($action) {
            self::ACCOUNT_CREATED => 'Akun dibuat',
            self::MEMBER_ADDED => 'Anggota ditambahkan',
            self::MEMBER_REMOVED => 'Anggota dikeluarkan',
            self::WALLET_CREATED => 'Dompet dibuat',
            self::WALLET_UPDATED => 'Dompet diubah',
            self::WALLET_DELETED => 'Dompet dihapus',
            self::CATEGORY_CREATED => 'Kategori dibuat',
            self::CATEGORY_UPDATED => 'Kategori diubah',
            self::CATEGORY_DELETED => 'Kategori dihapus',
            self::TRANSACTION_CREATED => 'Transaksi dicatat',
            self::TRANSACTION_UPDATED => 'Transaksi diubah',
            self::TRANSACTION_DELETED => 'Transaksi dihapus',
            default => ucfirst(str_replace('.', ' ', $action)),
        };
    }

    public function scopeLabel(): string
    {
        return $this->is_shared ? 'Bersama' : 'Pribadi';
    }

    public function icon(): string
    {
        return match (true) {
            str_starts_with($this->action, 'transaction.') => 'ti ti-arrows-exchange',
            str_starts_with($this->action, 'wallet.') => 'ti ti-wallet',
            str_starts_with($this->action, 'category.') => 'ti ti-tag',
            str_starts_with($this->action, 'member.') => 'ti ti-users',
            default => 'ti ti-user-circle',
        };
    }

    public function tone(): string
    {
        return match (true) {
            str_ends_with($this->action, '.created') => 'success',
            str_ends_with($this->action, '.updated') => 'primary',
            str_ends_with($this->action, '.deleted'), $this->action === self::MEMBER_REMOVED => 'danger',
            default => 'secondary',
        };
    }
}
