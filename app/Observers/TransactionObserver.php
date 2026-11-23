<?php

namespace App\Observers;

use App\Events\CashflowUpdated;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        $this->dispatchCashflowEvent($transaction, 'created');
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        $this->dispatchCashflowEvent($transaction, 'updated');
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        $this->dispatchCashflowEvent($transaction, 'deleted');
    }

    private function dispatchCashflowEvent(Transaction $transaction, string $action): void
    {
        $transaction->loadMissing(['wallet', 'category', 'account', 'user']);

        $actor = auth()->user();
        $wallet = $transaction->wallet;

        $payload = [
            (int) $transaction->account_id,
            $transaction->account?->name ?? 'Akun',
            (bool) ($wallet?->is_shared ?? true),
            (int) ($wallet?->user_id ?? $transaction->user_id),
            (int) $transaction->user_id,
            $transaction->user?->name ?? 'Pengguna',
            (int) ($actor?->id ?? $transaction->user_id),
            $actor?->name ?? $transaction->user?->name ?? 'Pengguna',
            $transaction->id,
            $action,
            $transaction->type,
            (string) $transaction->amount,
            $wallet?->name ?? 'Dompet',
            $transaction->category?->name,
            $transaction->description,
            $transaction->transaction_date?->toDateString() ?? now()->toDateString(),
        ];

        // Pengiriman notifikasi ditunda sampai transaksi database commit, sehingga
        // perangkat tidak menerima pemberitahuan palsu bila pencatatan gagal di tengah jalan.
        DB::afterCommit(fn (): mixed => CashflowUpdated::dispatch(...$payload));
    }
}
