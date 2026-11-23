<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\AccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_shared_and_personal_wallets(): void
    {
        $user = User::factory()->create();
        $account = $user->currentAccount;

        $this->actingAs($user)->post('/wallets', [
            'name' => 'Rekening Bank',
            'is_shared' => 1,
        ])->assertRedirect(route('wallets.index'));

        $this->actingAs($user)->post('/wallets', [
            'name' => 'Uang Jajan',
            'is_shared' => 0,
        ])->assertRedirect(route('wallets.index'));

        $this->assertDatabaseHas('wallets', [
            'account_id' => $account->id,
            'name' => 'Rekening Bank',
            'is_shared' => true,
        ]);

        $this->assertDatabaseHas('wallets', [
            'account_id' => $account->id,
            'name' => 'Uang Jajan',
            'is_shared' => false,
        ]);
    }

    public function test_wallet_name_must_be_unique_within_the_same_scope(): void
    {
        $user = User::factory()->create();

        Wallet::factory()->create([
            'account_id' => $user->currentAccount->id,
            'user_id' => $user->id,
            'name' => 'Kas Utama',
            'is_shared' => true,
        ]);

        $this->actingAs($user)->post('/wallets', [
            'name' => 'Kas Utama',
            'is_shared' => 1,
        ])->assertSessionHasErrors('name');

        $this->assertDatabaseCount('wallets', 2);
    }

    public function test_owner_can_rename_a_wallet(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'account_id' => $user->currentAccount->id,
            'user_id' => $user->id,
            'name' => 'Kas Lama',
            'is_shared' => true,
        ]);

        $this->actingAs($user)->put(route('wallets.update', $wallet), [
            'name' => 'Kas Baru',
        ])->assertRedirect(route('wallets.index'));

        $this->assertSame('Kas Baru', $wallet->fresh()->name);
    }

    public function test_wallet_with_transactions_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $account = $user->currentAccount;
        $wallet = Wallet::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'name' => 'Kas Utama',
            'is_shared' => true,
        ]);

        Transaction::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
        ]);

        $this->actingAs($user)
            ->delete(route('wallets.destroy', $wallet))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('wallets', ['id' => $wallet->id]);
    }

    public function test_the_only_wallet_of_a_scope_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $personalWallet = $user->currentAccount->personalWalletsFor($user)->firstOrFail();

        $this->actingAs($user)
            ->delete(route('wallets.destroy', $personalWallet))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('wallets', ['id' => $personalWallet->id]);
    }

    public function test_user_cannot_update_or_delete_a_wallet_from_another_account(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $wallet = Wallet::factory()->create([
            'account_id' => $owner->currentAccount->id,
            'user_id' => $owner->id,
            'name' => 'Dompet Pemilik',
            'is_shared' => true,
        ]);

        $this->actingAs($intruder)
            ->put(route('wallets.update', $wallet), ['name' => 'Dicuri'])
            ->assertNotFound();

        $this->actingAs($intruder)
            ->delete(route('wallets.destroy', $wallet))
            ->assertNotFound();

        $this->assertSame('Dompet Pemilik', $wallet->fresh()->name);
    }

    public function test_member_cannot_touch_another_members_personal_wallet(): void
    {
        $owner = User::factory()->create();
        $account = $owner->currentAccount;
        $member = User::factory()->create();

        app(AccountService::class)->addMember($account, $member, $owner);

        $personalWallet = $account->personalWalletsFor($member)->firstOrFail();

        $this->actingAs($owner)
            ->delete(route('wallets.destroy', $personalWallet))
            ->assertNotFound();

        $this->assertDatabaseHas('wallets', ['id' => $personalWallet->id]);
    }

    public function test_wallet_page_shows_shared_and_personal_totals(): void
    {
        $user = User::factory()->create();
        $account = $user->currentAccount;

        Wallet::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'name' => 'Kas Utama',
            'balance' => 750000,
            'is_shared' => true,
        ]);

        Wallet::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'name' => 'Uang Jajan',
            'balance' => 150000,
            'is_shared' => false,
        ]);

        $this->actingAs($user)->get(route('wallets.index'))
            ->assertOk()
            ->assertSee('750.000')
            ->assertSee('150.000');
    }
}
