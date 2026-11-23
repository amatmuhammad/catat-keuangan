<?php

namespace App\Notifications;

use App\Events\CashflowUpdated;
use App\Models\Category;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class CashflowChangedNotification extends Notification
{
    public function __construct(public CashflowUpdated $cashflow) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    /**
     * Format pesan Web Push yang muncul di bilah notifikasi perangkat.
     */
    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        $isIncome = $this->cashflow->type === Category::TYPE_INCOME;
        $amount = ($isIncome ? '+' : '-').'Rp '.number_format((float) $this->cashflow->amount, 0, ',', '.');

        $title = match ($this->cashflow->action) {
            'created' => $isIncome ? 'Arus Kas Masuk' : 'Arus Kas Keluar',
            'updated' => $isIncome ? 'Pemasukan Diperbarui' : 'Pengeluaran Diperbarui',
            'deleted' => $isIncome ? 'Pemasukan Dihapus' : 'Pengeluaran Dihapus',
            default => 'Perubahan Arus Kas',
        };

        $body = $this->cashflow->walletName.' · '.$amount;

        if ($this->cashflow->categoryName) {
            $body .= ' · '.$this->cashflow->categoryName;
        }

        if ($this->cashflow->description) {
            $body .= "\n".$this->cashflow->description;
        }

        $scope = $this->cashflow->isShared ? 'Dompet bersama' : 'Dompet pribadi';
        $body .= "\n".$scope.' · '.$this->cashflow->accountName.' · oleh '.$this->cashflow->actorName;

        if ($this->cashflow->actorName !== $this->cashflow->userName) {
            $body .= ' (dicatat oleh '.$this->cashflow->userName.')';
        }

        return (new WebPushMessage)
            ->title($title)
            ->body($body)
            ->icon('/icons/icon-192x192.png')
            ->badge('/icons/icon-192x192.png')
            ->tag('cashflow-'.$this->cashflow->transactionId)
            ->renotify()
            ->data([
                'url' => route('transactions.index', [], false),
                'transaction_id' => $this->cashflow->transactionId,
                'account_id' => $this->cashflow->accountId,
                'is_shared' => $this->cashflow->isShared,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id' => $this->cashflow->transactionId,
            'account_id' => $this->cashflow->accountId,
            'account_name' => $this->cashflow->accountName,
            'is_shared' => $this->cashflow->isShared,
            'input_by' => $this->cashflow->userName,
            'actor' => $this->cashflow->actorName,
            'action' => $this->cashflow->action,
            'type' => $this->cashflow->type,
            'amount' => $this->cashflow->amount,
            'wallet' => $this->cashflow->walletName,
            'category' => $this->cashflow->categoryName,
        ];
    }
}
