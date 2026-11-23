<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_transactions_can_be_filtered_by_type(): void
    {
        $user = User::factory()->create();
        [$income, $expense] = $this->sampleTransactions($user);

        $this->actingAs($user)->get(route('transactions.index', ['type' => Category::TYPE_INCOME]))
            ->assertOk()
            ->assertSee($income->description)
            ->assertDontSee($expense->description);

        $this->actingAs($user)->get(route('transactions.index', ['type' => Category::TYPE_EXPENSE]))
            ->assertOk()
            ->assertSee($expense->description)
            ->assertDontSee($income->description);
    }

    public function test_transactions_can_be_filtered_by_date_range_and_keyword(): void
    {
        $user = User::factory()->create();
        [$income, $expense] = $this->sampleTransactions($user);

        $this->actingAs($user)->get(route('transactions.index', [
            'date_from' => now()->toDateString(),
            'date_to' => now()->toDateString(),
        ]))
            ->assertOk()
            ->assertSee($income->description)
            ->assertDontSee($expense->description);

        $this->actingAs($user)->get(route('transactions.index', ['q' => 'kata kunci']))
            ->assertOk()
            ->assertSee($expense->description)
            ->assertDontSee($income->description);
    }

    public function test_filter_shows_summary_of_filtered_transactions(): void
    {
        $user = User::factory()->create();
        [$income] = $this->sampleTransactions($user);

        $this->actingAs($user)->get(route('transactions.index', ['type' => Category::TYPE_INCOME]))
            ->assertOk()
            ->assertSee('Pemasukan (sesuai filter)')
            ->assertSee('Pengeluaran (sesuai filter)')
            ->assertSee('+Rp '.number_format((float) $income->amount, 0, ',', '.'))
            ->assertSee('-Rp 0');
    }

    public function test_wallet_from_another_account_is_rejected_as_filter(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $foreignWallet = Wallet::factory()->create([
            'account_id' => $owner->currentAccount->id,
            'user_id' => $owner->id,
            'name' => 'Dompet Asing',
            'is_shared' => true,
        ]);

        $this->actingAs($intruder)
            ->get(route('transactions.index', ['wallet_id' => $foreignWallet->id]))
            ->assertSessionHasErrors('wallet_id');
    }

    public function test_transaction_from_another_account_cannot_be_created(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $foreignWallet = Wallet::factory()->create([
            'account_id' => $owner->currentAccount->id,
            'user_id' => $owner->id,
            'name' => 'Dompet Pemilik',
            'is_shared' => true,
        ]);

        $this->actingAs($intruder)->post('/transactions', [
            'wallet_id' => $foreignWallet->id,
            'type' => Category::TYPE_INCOME,
            'amount' => 1000,
            'transaction_date' => now()->toDateString(),
        ])->assertSessionHasErrors('wallet_id');

        $this->assertDatabaseCount('transactions', 0);
    }

    /**
     * @return array{0: Transaction, 1: Transaction}
     */
    private function sampleTransactions(User $user): array
    {
        $account = $user->currentAccount;

        $wallet = Wallet::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'name' => 'Kas Utama '.uniqid(),
            'is_shared' => true,
        ]);

        $income = Transaction::factory()->income()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'description' => 'Pemasukan unik hari ini',
            'amount' => 500000,
            'transaction_date' => now()->toDateString(),
        ]);

        $expense = Transaction::factory()->expense()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'description' => 'kata kunci pengeluaran',
            'amount' => 45000,
            'transaction_date' => now()->subDays(10)->toDateString(),
        ]);

        return [$income, $expense];
    }
}
