<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class CashflowService
{
    /**
     * Simpan transaksi baru pada akun aktif dan sesuaikan saldo dompet secara atomik.
     *
     * @param  array{wallet_id: int, category_id?: int|null, type: string, amount: numeric-string|float, description?: string|null, transaction_date: string}  $attributes
     */
    public function create(Account $account, User $user, array $attributes): Transaction
    {
        return DB::transaction(function () use ($account, $user, $attributes) {
            $wallet = $account->visibleWallets($user)->lockForUpdate()->findOrFail($attributes['wallet_id']);

            $transaction = $account->transactions()->create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'category_id' => $attributes['category_id'] ?? null,
                'type' => $attributes['type'],
                'amount' => $attributes['amount'],
                'description' => $attributes['description'] ?? null,
                'transaction_date' => $attributes['transaction_date'],
            ]);

            $this->applyToWallet($wallet, $transaction->type, $transaction->amount, 1);

            return $transaction->load(['wallet', 'category', 'user', 'account']);
        });
    }

    /**
     * Ubah transaksi: kembalikan dampak lama, lalu terapkan nilai baru.
     * Penginput awal tetap dicatat pada transaksi.
     *
     * @param  array{wallet_id: int, category_id?: int|null, type: string, amount: numeric-string|float, description?: string|null, transaction_date: string}  $attributes
     */
    public function update(Transaction $transaction, array $attributes): Transaction
    {
        return DB::transaction(function () use ($transaction, $attributes) {
            $walletIds = collect([$transaction->wallet_id, $attributes['wallet_id']])->unique()->values();

            $wallets = Wallet::query()
                ->where('account_id', $transaction->account_id)
                ->whereIn('id', $walletIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $oldWallet = $wallets->get($transaction->wallet_id);

            if ($oldWallet) {
                $this->applyToWallet($oldWallet, $transaction->type, $transaction->amount, -1);
            }

            $transaction->update([
                'wallet_id' => $attributes['wallet_id'],
                'category_id' => $attributes['category_id'] ?? null,
                'type' => $attributes['type'],
                'amount' => $attributes['amount'],
                'description' => $attributes['description'] ?? null,
                'transaction_date' => $attributes['transaction_date'],
            ]);

            $newWallet = $wallets->get($transaction->wallet_id) ?? $oldWallet;

            if ($newWallet) {
                $this->applyToWallet($newWallet, $transaction->type, $transaction->amount, 1);
            }

            return $transaction->load(['wallet', 'category', 'user', 'account']);
        });
    }

    /**
     * Hapus transaksi dan kembalikan saldo dompet.
     */
    public function delete(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $wallet = Wallet::query()
                ->whereKey($transaction->wallet_id)
                ->lockForUpdate()
                ->first();

            if ($wallet) {
                $this->applyToWallet($wallet, $transaction->type, $transaction->amount, -1);
            }

            $transaction->delete();
        });
    }

    /**
     * Terapkan nominal ke saldo dompet memakai bcmath supaya tidak ada selisih pembulatan.
     */
    private function applyToWallet(Wallet $wallet, string $type, string|float $amount, int $direction): void
    {
        $sign = $type === Category::TYPE_INCOME ? 1 : -1;
        $delta = bcmul((string) $amount, (string) ($direction * $sign), 2);

        $wallet->balance = bcadd((string) $wallet->balance, $delta, 2);
        $wallet->save();
    }
}
