<?php

namespace App\Listeners;

use App\Events\CashflowUpdated;
use App\Models\User;
use App\Notifications\CashflowChangedNotification;
use Throwable;

/**
 * Transaksi bersama dikirim ke semua anggota, transaksi pribadi hanya ke pemilik dompet.
 *
 * Pengiriman dilakukan langsung (sinkron) saat transaksi disimpan agar notifikasi tampil
 * seketika di perangkat tanpa menunggu queue worker atau cron. Kegagalan layanan push dicatat,
 * tetapi tidak pernah menggagalkan pencatatan transaksi.
 */
class SendCashflowPushNotification
{
    public function handle(CashflowUpdated $event): void
    {
        $members = $event->isShared
            ? User::query()
                ->whereHas('accounts', fn ($query) => $query->whereKey($event->accountId))
                ->whereHas('pushSubscriptions')
                ->get()
            : User::query()
                ->whereKey($event->walletOwnerId)
                ->whereHas('pushSubscriptions')
                ->get();

        foreach ($members as $member) {
            try {
                $member->notify(new CashflowChangedNotification($event));
            } catch (Throwable $exception) {
                report($exception);
            }
        }
    }
}
