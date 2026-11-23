<?php

namespace App\Models;

use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['account_id', 'user_id', 'wallet_id', 'category_id', 'type', 'amount', 'description', 'transaction_date'])]
class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
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
     * Pengguna yang menginput transaksi ini.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Wallet, $this>
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isIncome(): bool
    {
        return $this->type === Category::TYPE_INCOME;
    }

    public function isShared(): bool
    {
        return (bool) ($this->wallet?->is_shared ?? true);
    }

    /**
     * Nominal bertanda: positif untuk pemasukan, negatif untuk pengeluaran.
     */
    public function signedAmount(): float
    {
        return $this->isIncome() ? (float) $this->amount : -1 * (float) $this->amount;
    }

    public function formattedAmount(): string
    {
        return 'Rp '.number_format((float) $this->amount, 0, ',', '.');
    }

    /**
     * @param  Builder<Transaction>  $query
     */
    public function scopeOfType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }
}
