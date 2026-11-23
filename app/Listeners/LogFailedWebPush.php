<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use NotificationChannels\WebPush\Events\NotificationFailed;

/**
 * Catat kegagalan pengiriman Web Push ke log aplikasi.
 *
 * Tanpa pencatatan ini, perangkat anggota yang tidak menerima notifikasi arus kas
 * (langganan kedaluwarsa, izin dicabut, atau layanan push bermasalah) tidak memiliki
 * jejak apa pun sehingga sulit ditelusuri.
 */
class LogFailedWebPush
{
    public function handle(NotificationFailed $event): void
    {
        Log::warning('Web push gagal dikirim ke perangkat.', [
            'endpoint' => $event->report->getEndpoint(),
            'reason' => $event->report->getReason(),
            'response' => $event->report->getResponseContent(),
            'subscription_expired' => $event->report->isSubscriptionExpired(),
            'subscription_id' => $event->subscription->id,
            'subscribable_id' => $event->subscription->subscribable_id,
            'subscribable_type' => $event->subscription->subscribable_type,
        ]);
    }
}
