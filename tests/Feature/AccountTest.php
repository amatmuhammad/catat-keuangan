<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\User;
use App\Models\Wallet;
use App\Services\CashflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_add_registered_user_as_member_and_it_is_logged(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $account = $owner->currentAccount;

        $this->actingAs($owner)
            ->post(route('account.members.store'), ['email' => $member->email])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('account_user', [
            'account_id' => $account->id,
            'user_id' => $member->id,
            'role' => Account::ROLE_MEMBER,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'account_id' => $account->id,
            'user_id' => $owner->id,
            'action' => ActivityLog::MEMBER_ADDED,
            'subject_id' => $member->id,
        ]);

        $this->assertSame($account->id, $member->fresh()->current_account_id);

        $this->actingAs($owner)
            ->get(route('account.members'))
            ->assertOk()
            ->assertSee($member->name)
            ->assertSee($member->email)
            ->assertSee('2 / 5 anggota');
    }

    public function test_account_cannot_have_more_than_five_members_including_owner(): void
    {
        $owner = User::factory()->create();
        $account = $owner->currentAccount;

        $members = User::factory()->count(4)->create();
        foreach ($members as $member) {
            $account->members()->attach($member->id, [
                'role' => Account::ROLE_MEMBER,
                'joined_at' => now(),
            ]);
        }

        $this->assertSame(5, $account->memberCount());

        $extra = User::factory()->create();

        $this->actingAs($owner)
            ->post(route('account.members.store'), ['email' => $extra->email])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertSame(5, $account->fresh()->memberCount());
        $this->assertDatabaseMissing('account_user', [
            'account_id' => $account->id,
            'user_id' => $extra->id,
        ]);
    }

    public function test_only_the_owner_can_add_or_remove_members(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $candidate = User::factory()->create();
        $account = $owner->currentAccount;

        $account->members()->attach($member->id, [
            'role' => Account::ROLE_MEMBER,
            'joined_at' => now(),
        ]);
        $member->forceFill(['current_account_id' => $account->id])->save();

        $this->actingAs($member)
            ->post(route('account.members.store'), ['email' => $candidate->email])
            ->assertForbidden();

        $this->actingAs($member)
            ->delete(route('account.members.destroy', $owner))
            ->assertForbidden();

        $this->assertDatabaseHas('account_user', ['account_id' => $account->id, 'user_id' => $owner->id]);
        $this->assertDatabaseMissing('account_user', ['account_id' => $account->id, 'user_id' => $candidate->id]);
    }

    public function test_member_viewing_personal_account_gets_shortcut_to_shared_account(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $shared = $owner->currentAccount;
        $personal = $member->currentAccount;

        $this->actingAs($owner)
            ->post(route('account.members.store'), ['email' => $member->email])
            ->assertSessionHasNoErrors();

        $this->assertSame($shared->id, $member->fresh()->current_account_id);

        $this->actingAs($member)
            ->post(route('account.switch'), ['account_id' => $personal->id])
            ->assertRedirect();

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Anda sedang melihat akun pribadi '.$personal->name)
            ->assertSee('Buka '.$shared->name);
    }

    public function test_member_can_switch_to_shared_account_and_back(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $shared = $owner->currentAccount;
        $personal = $member->currentAccount;

        $shared->members()->attach($member->id, ['role' => Account::ROLE_MEMBER, 'joined_at' => now()]);

        $this->actingAs($member)
            ->post(route('account.switch'), ['account_id' => $shared->id])
            ->assertRedirect();

        $this->assertSame($shared->id, $member->fresh()->current_account_id);

        $this->actingAs($member)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertSee($shared->name);

        $this->actingAs($member)
            ->post(route('account.switch'), ['account_id' => $personal->id])
            ->assertRedirect();

        $this->assertSame($personal->id, $member->fresh()->current_account_id);
    }

    public function test_member_cannot_switch_to_an_account_they_do_not_belong_to(): void
    {
        $member = User::factory()->create();
        $outsider = User::factory()->create();

        $this->actingAs($member)
            ->post(route('account.switch'), ['account_id' => $outsider->currentAccount->id])
            ->assertNotFound();
    }

    public function test_shared_transaction_is_attributed_to_inputter_and_appears_in_account_activity_log(): void
    {
        $owner = User::factory()->create(['name' => 'Pemilik']);
        $member = User::factory()->create(['name' => 'Siti']);
        $outsider = User::factory()->create();
        $account = $owner->currentAccount;

        $account->members()->attach($member->id, ['role' => Account::ROLE_MEMBER, 'joined_at' => now()]);
        $member->forceFill(['current_account_id' => $account->id])->save();

        $wallet = Wallet::factory()->create([
            'account_id' => $account->id,
            'user_id' => $owner->id,
        ]);

        $this->actingAs($member)->post('/transactions', [
            'wallet_id' => $wallet->id,
            'type' => Category::TYPE_EXPENSE,
            'amount' => 75000,
            'description' => 'Belanja bersama',
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'user_id' => $member->id,
            'description' => 'Belanja bersama',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'account_id' => $account->id,
            'user_id' => $member->id,
            'action' => ActivityLog::TRANSACTION_CREATED,
        ]);

        $this->actingAs($owner)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertSee('Belanja bersama')
            ->assertSee('oleh Siti');

        $this->actingAs($owner)
            ->get(route('account.activity'))
            ->assertOk()
            ->assertSee('Belanja bersama')
            ->assertSee('Siti');

        // Transaksi akun lain tidak boleh masuk log akun bersama.
        $outsiderWallet = Wallet::factory()->create([
            'account_id' => $outsider->currentAccount->id,
            'user_id' => $outsider->id,
        ]);

        app(CashflowService::class)->create($outsider->currentAccount, $outsider, [
            'wallet_id' => $outsiderWallet->id,
            'type' => Category::TYPE_INCOME,
            'amount' => 999000,
            'description' => 'Milik akun lain',
            'transaction_date' => now()->toDateString(),
        ]);

        $this->actingAs($owner)
            ->get(route('account.activity'))
            ->assertOk()
            ->assertDontSee('Milik akun lain');
    }

    public function test_each_member_has_private_wallet_and_shared_balance_is_visible_to_all(): void
    {
        $owner = User::factory()->create(['name' => 'Pemilik']);
        $member = User::factory()->create(['name' => 'Siti']);
        $account = $owner->currentAccount;

        $this->actingAs($owner)
            ->post(route('account.members.store'), ['email' => $member->email])
            ->assertSessionHasNoErrors();

        $this->actingAs($member)
            ->post(route('account.switch'), ['account_id' => $account->id])
            ->assertRedirect();

        $sharedWallet = Wallet::factory()->create([
            'account_id' => $account->id,
            'user_id' => $owner->id,
            'name' => 'Kas Bersama',
        ]);

        $ownerPersonal = $account->personalWalletsFor($owner)->firstOrFail();
        $memberPersonal = $account->personalWalletsFor($member)->firstOrFail();

        $this->assertFalse($ownerPersonal->isShared());
        $this->assertFalse($memberPersonal->isShared());
        $this->assertNotSame($ownerPersonal->id, $memberPersonal->id);

        $this->actingAs($member)->post('/transactions', [
            'wallet_id' => $sharedWallet->id,
            'type' => Category::TYPE_INCOME,
            'amount' => 100000,
            'description' => 'Iuran bersama',
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('transactions.index'));

        $this->actingAs($owner)->post('/transactions', [
            'wallet_id' => $sharedWallet->id,
            'type' => Category::TYPE_EXPENSE,
            'amount' => 20000,
            'description' => 'Beli galon',
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('transactions.index'));

        $this->assertSame(80000.0, (float) $sharedWallet->fresh()->balance);
        $this->assertSame(0.0, (float) $ownerPersonal->fresh()->balance);
        $this->assertSame(0.0, (float) $memberPersonal->fresh()->balance);

        $this->actingAs($member)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Saldo Bersama')
            ->assertSee('Saldo Pribadi')
            ->assertSee('80.000');

        $this->actingAs($owner)
            ->get(route('account.activity'))
            ->assertOk()
            ->assertSee('Iuran bersama')
            ->assertSee('Siti');

        $this->assertDatabaseHas('activity_logs', [
            'account_id' => $account->id,
            'user_id' => $member->id,
            'action' => ActivityLog::TRANSACTION_CREATED,
            'is_shared' => true,
        ]);
    }

    public function test_personal_transaction_and_wallet_only_visible_to_its_owner(): void
    {
        $owner = User::factory()->create(['name' => 'Pemilik']);
        $member = User::factory()->create(['name' => 'Siti']);
        $account = $owner->currentAccount;

        $this->actingAs($owner)
            ->post(route('account.members.store'), ['email' => $member->email])
            ->assertSessionHasNoErrors();

        $this->actingAs($member)
            ->post(route('account.switch'), ['account_id' => $account->id])
            ->assertRedirect();

        $sharedWallet = Wallet::factory()->create([
            'account_id' => $account->id,
            'user_id' => $owner->id,
            'name' => 'Kas Bersama',
        ]);
        $memberPersonal = $account->personalWalletsFor($member)->firstOrFail();
        $memberPersonal->update(['name' => 'Dompet Rahasia Siti']);

        $this->actingAs($member)->post('/transactions', [
            'wallet_id' => $memberPersonal->id,
            'type' => Category::TYPE_EXPENSE,
            'amount' => 50000,
            'description' => 'Jajan pribadi Siti',
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('transactions.index'));

        $this->actingAs($owner)->post('/transactions', [
            'wallet_id' => $sharedWallet->id,
            'type' => Category::TYPE_EXPENSE,
            'amount' => 100000,
            'description' => 'Bayar listrik',
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('transactions.index'));

        $personalTransaction = $member->transactions()->where('description', 'Jajan pribadi Siti')->firstOrFail();

        $this->assertSame(-50000.0, (float) $memberPersonal->fresh()->balance);

        $this->actingAs($member)->get(route('transactions.index'))
            ->assertOk()
            ->assertSee('Jajan pribadi Siti')
            ->assertSee('Bayar listrik');

        $this->actingAs($member)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Jajan pribadi Siti')
            ->assertSee('Bayar listrik');

        $this->actingAs($owner)->get(route('transactions.index'))
            ->assertOk()
            ->assertSee('Bayar listrik')
            ->assertDontSee('Jajan pribadi Siti');

        $this->actingAs($owner)->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Jajan pribadi Siti');

        $this->actingAs($owner)->get(route('calendar'))
            ->assertOk()
            ->assertDontSee('Jajan pribadi Siti');

        $this->actingAs($owner)->get(route('account.activity'))
            ->assertOk()
            ->assertDontSee('Jajan pribadi Siti');

        $this->actingAs($owner)->get(route('account.activity', ['scope' => 'personal']))
            ->assertOk()
            ->assertDontSee('Jajan pribadi Siti');

        $this->actingAs($member)->get(route('account.activity', ['scope' => 'personal']))
            ->assertOk()
            ->assertSee('Jajan pribadi Siti')
            ->assertDontSee('Bayar listrik');

        $this->actingAs($owner)->get(route('wallets.index'))
            ->assertOk()
            ->assertDontSee('Dompet Rahasia Siti');

        $this->actingAs($member)->get(route('wallets.index'))
            ->assertOk()
            ->assertSee('Dompet Rahasia Siti');

        $this->actingAs($owner)
            ->get(route('transactions.edit', $personalTransaction))
            ->assertNotFound();

        $this->actingAs($owner)
            ->delete(route('transactions.destroy', $personalTransaction))
            ->assertNotFound();

        $this->actingAs($member)
            ->get(route('transactions.edit', $personalTransaction))
            ->assertOk();
    }

    public function test_live_summary_returns_shared_balance_and_only_the_current_users_personal_balance(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $account = $owner->currentAccount;

        $this->actingAs($owner)
            ->post(route('account.members.store'), ['email' => $member->email])
            ->assertSessionHasNoErrors();

        $sharedWallet = Wallet::factory()->create([
            'account_id' => $account->id,
            'user_id' => $owner->id,
            'name' => 'Kas Utama',
            'is_shared' => true,
        ]);
        $ownerPersonal = $account->personalWalletsFor($owner)->firstOrFail();
        $memberPersonal = $account->personalWalletsFor($member)->firstOrFail();

        $cashflow = app(CashflowService::class);

        $cashflow->create($account, $member, [
            'wallet_id' => $sharedWallet->id,
            'type' => Category::TYPE_INCOME,
            'amount' => 100000,
            'transaction_date' => now()->toDateString(),
        ]);

        $cashflow->create($account, $owner, [
            'wallet_id' => $ownerPersonal->id,
            'type' => Category::TYPE_INCOME,
            'amount' => 25000,
            'transaction_date' => now()->toDateString(),
        ]);

        $cashflow->create($account, $member, [
            'wallet_id' => $memberPersonal->id,
            'type' => Category::TYPE_INCOME,
            'amount' => 90000,
            'transaction_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($owner)->getJson(route('live.summary'));

        $response->assertOk()
            ->assertJsonPath('account_id', $account->id)
            ->assertJsonPath('shared_wallet_count', 1)
            ->assertJsonPath('personal_wallet_count', 1);

        $this->assertSame(100000.0, (float) $response->json('shared_balance'));
        $this->assertSame(25000.0, (float) $response->json('personal_balance'));

        $walletIds = collect($response->json('wallets'))->pluck('id');

        $this->assertTrue($walletIds->contains($sharedWallet->id));
        $this->assertTrue($walletIds->contains($ownerPersonal->id));
        $this->assertFalse($walletIds->contains($memberPersonal->id));
    }

    public function test_member_can_create_shared_and_personal_wallets(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $account = $owner->currentAccount;

        $this->actingAs($owner)
            ->post(route('account.members.store'), ['email' => $member->email])
            ->assertSessionHasNoErrors();

        $this->actingAs($member)
            ->post(route('account.switch'), ['account_id' => $account->id]);

        $this->actingAs($member)->post(route('wallets.store'), [
            'name' => 'Dompet Bersama Baru',
            'is_shared' => '1',
        ])->assertRedirect(route('wallets.index'));

        $this->actingAs($member)->post(route('wallets.store'), [
            'name' => 'Dompet Pribadi Baru',
            'is_shared' => '0',
        ])->assertRedirect(route('wallets.index'));

        $this->assertDatabaseHas('wallets', [
            'account_id' => $account->id,
            'user_id' => $member->id,
            'name' => 'Dompet Bersama Baru',
            'is_shared' => true,
        ]);
        $this->assertDatabaseHas('wallets', [
            'account_id' => $account->id,
            'user_id' => $member->id,
            'name' => 'Dompet Pribadi Baru',
            'is_shared' => false,
        ]);

        $this->actingAs($owner)->get(route('wallets.index'))
            ->assertOk()
            ->assertSee('Dompet Bersama Baru')
            ->assertDontSee('Dompet Pribadi Baru');
    }

    public function test_owner_can_remove_member_and_member_falls_back_to_personal_account(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $shared = $owner->currentAccount;
        $personal = $member->currentAccount;

        $shared->members()->attach($member->id, ['role' => Account::ROLE_MEMBER, 'joined_at' => now()]);
        $member->forceFill(['current_account_id' => $shared->id])->save();

        $this->actingAs($owner)
            ->delete(route('account.members.destroy', $member))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('account_user', [
            'account_id' => $shared->id,
            'user_id' => $member->id,
        ]);
        $this->assertSame($personal->id, $member->fresh()->current_account_id);
        $this->assertDatabaseHas('activity_logs', [
            'account_id' => $shared->id,
            'user_id' => $owner->id,
            'action' => ActivityLog::MEMBER_REMOVED,
            'subject_id' => $member->id,
        ]);
    }

    public function test_owner_cannot_be_removed_from_own_account(): void
    {
        $owner = User::factory()->create();
        $account = $owner->currentAccount;

        $this->actingAs($owner)
            ->delete(route('account.members.destroy', $owner))
            ->assertRedirect()
            ->assertSessionHasErrors('member');

        $this->assertDatabaseHas('account_user', [
            'account_id' => $account->id,
            'user_id' => $owner->id,
            'role' => Account::ROLE_OWNER,
        ]);
    }
}
