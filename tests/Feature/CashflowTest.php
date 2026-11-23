<?php

namespace Tests\Feature;

use App\Events\CashflowUpdated;
use App\Listeners\SendCashflowPushNotification;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\CashflowChangedNotification;
use App\Services\AccountService;
use App\Services\CashflowService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use NotificationChannels\WebPush\WebPushChannel;
use RuntimeException;
use Tests\TestCase;

class CashflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_income_transaction_increases_wallet_balance_and_records_inputter(): void
    {
        [$user, $wallet] = $this->userWithWallet();
        $account = $user->currentAccount;

        $this->actingAs($user)->post('/transactions', [
            'wallet_id' => $wallet->id,
            'type' => Category::TYPE_INCOME,
            'amount' => 500000,
            'description' => 'Gaji bulan ini',
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('transactions.index'));

        $this->assertSame(500000.0, (float) $wallet->fresh()->balance);
        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => Category::TYPE_INCOME,
            'amount' => 500000,
        ]);
    }

    public function test_expense_transaction_decreases_wallet_balance(): void
    {
        [$user, $wallet] = $this->userWithWallet(balance: 200000);

        $this->actingAs($user)->post('/transactions', [
            'wallet_id' => $wallet->id,
            'type' => Category::TYPE_EXPENSE,
            'amount' => 45000.50,
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('transactions.index'));

        $this->assertSame(154999.5, (float) $wallet->fresh()->balance);
    }

    public function test_updating_transaction_recalculates_both_wallets(): void
    {
        [$user, $walletA] = $this->userWithWallet();
        $account = $user->currentAccount;
        $walletB = Wallet::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'balance' => 0,
        ]);

        $transaction = $this->cashflow()->create($account, $user, [
            'wallet_id' => $walletA->id,
            'type' => Category::TYPE_INCOME,
            'amount' => 100000,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->assertSame(100000.0, (float) $walletA->fresh()->balance);

        $this->actingAs($user)->put(route('transactions.update', $transaction), [
            'wallet_id' => $walletB->id,
            'type' => Category::TYPE_INCOME,
            'amount' => 125000,
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('transactions.index'));

        $this->assertSame(0.0, (float) $walletA->fresh()->balance);
        $this->assertSame(125000.0, (float) $walletB->fresh()->balance);
        $this->assertSame($user->id, $transaction->fresh()->user_id);
    }

    public function test_transaction_scope_cannot_be_changed_when_editing(): void
    {
        [$user, $sharedWallet] = $this->userWithWallet();
        $personalWallet = $user->currentAccount->personalWalletsFor($user)->firstOrFail();

        $transaction = $this->cashflow()->create($user->currentAccount, $user, [
            'wallet_id' => $sharedWallet->id,
            'type' => Category::TYPE_EXPENSE,
            'amount' => 20000,
            'description' => 'Transaksi bersama',
            'transaction_date' => now()->toDateString(),
        ]);

        $this->actingAs($user)->put(route('transactions.update', $transaction), [
            'wallet_id' => $personalWallet->id,
            'type' => Category::TYPE_EXPENSE,
            'amount' => 20000,
            'description' => 'Transaksi bersama',
            'transaction_date' => now()->toDateString(),
        ])->assertSessionHasErrors('wallet_id');

        $this->assertSame($sharedWallet->id, $transaction->fresh()->wallet_id);
        $this->assertSame(-20000.0, (float) $sharedWallet->fresh()->balance);
        $this->assertSame(0.0, (float) $personalWallet->fresh()->balance);
    }

    public function test_deleting_transaction_restores_wallet_balance(): void
    {
        [$user, $wallet] = $this->userWithWallet();

        $transaction = $this->cashflow()->create($user->currentAccount, $user, [
            'wallet_id' => $wallet->id,
            'type' => Category::TYPE_EXPENSE,
            'amount' => 30000,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->assertSame(-30000.0, (float) $wallet->fresh()->balance);

        $this->actingAs($user)
            ->delete(route('transactions.destroy', $transaction))
            ->assertRedirect(route('transactions.index'));

        $this->assertSame(0.0, (float) $wallet->fresh()->balance);
        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    }

    public function test_category_must_match_transaction_type(): void
    {
        [$user, $wallet] = $this->userWithWallet();
        $account = $user->currentAccount;
        $incomeCategory = Category::factory()->income()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)->post('/transactions', [
            'wallet_id' => $wallet->id,
            'category_id' => $incomeCategory->id,
            'type' => Category::TYPE_EXPENSE,
            'amount' => 10000,
            'transaction_date' => now()->toDateString(),
        ])->assertSessionHasErrors('category_id');

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_user_cannot_edit_another_accounts_transaction(): void
    {
        [$owner, $wallet] = $this->userWithWallet();
        $intruder = User::factory()->create();

        $transaction = $this->cashflow()->create($owner->currentAccount, $owner, [
            'wallet_id' => $wallet->id,
            'type' => Category::TYPE_INCOME,
            'amount' => 10000,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->actingAs($intruder)
            ->get(route('transactions.edit', $transaction))
            ->assertNotFound();

        $this->actingAs($intruder)
            ->delete(route('transactions.destroy', $transaction))
            ->assertNotFound();

        $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
    }

    public function test_dashboard_cards_show_formatted_nominal_amounts(): void
    {
        [$user, $wallet] = $this->userWithWallet();
        $account = $user->currentAccount;

        $this->cashflow()->create($account, $user, [
            'wallet_id' => $wallet->id,
            'type' => Category::TYPE_INCOME,
            'amount' => 500000,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->cashflow()->create($account, $user, [
            'wallet_id' => $wallet->id,
            'type' => Category::TYPE_EXPENSE,
            'amount' => 45000,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('Saldo Bersama')
            ->assertSee('Saldo Pribadi')
            ->assertSee('455.000')
            ->assertSee('500.000')
            ->assertSee('45.000');
    }

    public function test_cashflow_push_notification_is_sent_to_all_subscribed_account_members(): void
    {
        $owner = User::factory()->create(['name' => 'Pemilik Akun']);
        $account = $owner->currentAccount;
        $member = User::factory()->create(['name' => 'Anggota Satu']);
        $outsider = User::factory()->create();

        $account->members()->attach($member->id, [
            'role' => Account::ROLE_MEMBER,
            'joined_at' => now(),
        ]);

        $owner->updatePushSubscription('https://push.example.com/device-1', 'public-key', 'auth-token', 'aes128gcm');
        $member->updatePushSubscription('https://push.example.com/device-2', 'public-key', 'auth-token', 'aes128gcm');
        $outsider->updatePushSubscription('https://push.example.com/device-3', 'public-key', 'auth-token', 'aes128gcm');

        $event = new CashflowUpdated(
            $account->id,
            $account->name,
            true,
            $owner->id,
            $member->id,
            $member->name,
            $member->id,
            $member->name,
            99,
            'created',
            Category::TYPE_INCOME,
            '500000.00',
            'Kas Utama',
            'Gaji',
            'Gaji bulan ini',
            now()->toDateString(),
        );

        Notification::fake();

        (new SendCashflowPushNotification)->handle($event);

        Notification::assertSentTo($owner, CashflowChangedNotification::class);
        Notification::assertSentTo($member, CashflowChangedNotification::class);
        Notification::assertNotSentTo($outsider, CashflowChangedNotification::class);

        $notification = new CashflowChangedNotification($event);
        $this->assertContains(WebPushChannel::class, $notification->via($member));
        $this->assertNotInstanceOf(ShouldQueue::class, new SendCashflowPushNotification);

        $message = $notification->toWebPush($member, $notification);

        $this->assertSame('Arus Kas Masuk', $message->toArray()['title']);
        $this->assertStringContainsString('+Rp 500.000', $message->toArray()['body']);
        $this->assertStringContainsString('Dompet bersama', $message->toArray()['body']);
        $this->assertStringContainsString($account->name, $message->toArray()['body']);
        $this->assertStringContainsString('oleh '.$member->name, $message->toArray()['body']);
    }

    public function test_personal_cashflow_notification_only_goes_to_the_wallet_owner(): void
    {
        $owner = User::factory()->create();
        $account = $owner->currentAccount;
        $member = User::factory()->create(['name' => 'Pemilik Pribadi']);

        $account->members()->attach($member->id, [
            'role' => Account::ROLE_MEMBER,
            'joined_at' => now(),
        ]);

        $owner->updatePushSubscription('https://push.example.com/device-1', 'public-key', 'auth-token', 'aes128gcm');
        $member->updatePushSubscription('https://push.example.com/device-2', 'public-key', 'auth-token', 'aes128gcm');

        $event = new CashflowUpdated(
            $account->id,
            $account->name,
            false,
            $member->id,
            $member->id,
            $member->name,
            $member->id,
            $member->name,
            100,
            'created',
            Category::TYPE_EXPENSE,
            '25000.00',
            'Saldo Pribadi',
            'Konsumsi',
            'Jajan pribadi',
            now()->toDateString(),
        );

        Notification::fake();

        (new SendCashflowPushNotification)->handle($event);

        Notification::assertSentTo($member, CashflowChangedNotification::class);
        Notification::assertNotSentTo($owner, CashflowChangedNotification::class);

        $notification = new CashflowChangedNotification($event);
        $message = $notification->toWebPush($member, $notification);

        $this->assertStringContainsString('Dompet pribadi', $message->toArray()['body']);
    }

    public function test_cashflow_event_sends_notification_immediately_without_queue(): void
    {
        $owner = User::factory()->create();
        $account = $owner->currentAccount;

        $owner->updatePushSubscription('https://push.example.com/device-1', 'public-key', 'auth-token', 'aes128gcm');

        Queue::fake();
        Notification::fake();

        CashflowUpdated::dispatch(
            $account->id,
            $account->name,
            true,
            $owner->id,
            $owner->id,
            $owner->name,
            $owner->id,
            $owner->name,
            1,
            'created',
            Category::TYPE_INCOME,
            '500000.00',
            'Kas Utama',
            'Gaji',
            'Gaji bulan ini',
            now()->toDateString(),
        );

        Queue::assertNothingPushed();
        Notification::assertSentTo($owner, CashflowChangedNotification::class);
    }

    public function test_member_recording_a_shared_transaction_notifies_every_member(): void
    {
        $owner = User::factory()->create(['name' => 'Pemilik']);
        $account = $owner->currentAccount;
        $member = User::factory()->create(['name' => 'Anggota']);

        app(AccountService::class)->addMember($account, $member, $owner);

        $owner->updatePushSubscription('https://push.example.com/device-owner', 'key', 'auth', 'aes128gcm');
        $member->updatePushSubscription('https://push.example.com/device-member', 'key', 'auth', 'aes128gcm');

        $sharedWallet = Wallet::factory()->create([
            'account_id' => $account->id,
            'user_id' => $owner->id,
            'name' => 'Kas Utama',
            'is_shared' => true,
        ]);

        Notification::fake();

        $this->actingAs($member)->post('/transactions', [
            'wallet_id' => $sharedWallet->id,
            'type' => Category::TYPE_INCOME,
            'amount' => 150000,
            'description' => 'Dicatat oleh anggota',
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('transactions.index'));

        Notification::assertSentTo($owner, CashflowChangedNotification::class);
        Notification::assertSentTo($member, CashflowChangedNotification::class);
    }

    public function test_saving_a_transaction_sends_the_push_notification_right_away(): void
    {
        [$user, $wallet] = $this->userWithWallet();
        $user->updatePushSubscription('https://push.example.com/device-1', 'public-key', 'auth-token', 'aes128gcm');

        Notification::fake();

        $this->actingAs($user)->post('/transactions', [
            'wallet_id' => $wallet->id,
            'type' => Category::TYPE_INCOME,
            'amount' => 500000,
            'description' => 'Gaji bulan ini',
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('transactions.index'));

        Notification::assertSentTo($user, CashflowChangedNotification::class);
    }

    public function test_no_notification_is_sent_when_the_transaction_is_rolled_back(): void
    {
        [$user, $wallet] = $this->userWithWallet();
        $user->updatePushSubscription('https://push.example.com/device-1', 'public-key', 'auth-token', 'aes128gcm');

        Notification::fake();

        try {
            DB::transaction(function () use ($user, $wallet): void {
                Transaction::factory()->create([
                    'account_id' => $user->currentAccount->id,
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'type' => Category::TYPE_INCOME,
                    'amount' => 500000,
                    'transaction_date' => now()->toDateString(),
                ]);

                throw new RuntimeException('Simulasi kegagalan pencatatan.');
            });
        } catch (RuntimeException) {
            // Transaksi dibatalkan, notifikasi tidak boleh terkirim.
        }

        Notification::assertNothingSent();
        $this->assertDatabaseCount('transactions', 0);
    }

    /**
     * @return array{0: User, 1: Wallet}
     */
    private function userWithWallet(float $balance = 0): array
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'account_id' => $user->currentAccount->id,
            'user_id' => $user->id,
            'balance' => $balance,
        ]);

        return [$user, $wallet];
    }

    private function cashflow(): CashflowService
    {
        return app(CashflowService::class);
    }
}
