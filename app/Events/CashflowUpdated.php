<?php

namespace App\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class CashflowUpdated implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    /**
     * Data transaksi dibawa sebagai skalar agar event tetap aman dikirim ke queue
     * walaupun transaksi aslinya sudah dihapus.
     */
    public function __construct(
        public int $accountId,
        public string $accountName,
        public bool $isShared,
        public int $walletOwnerId,
        public int $userId,
        public string $userName,
        public int $actorId,
        public string $actorName,
        public int $transactionId,
        public string $action,
        public string $type,
        public string $amount,
        public string $walletName,
        public ?string $categoryName,
        public ?string $description,
        public string $transactionDate,
    ) {}
}
