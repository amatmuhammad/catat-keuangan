<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Models\Wallet;
use App\Services\CashflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_calendar_shows_monthly_totals_and_daily_details(): void
    {
        $user = User::factory()->create(['name' => 'Budi']);
        $account = $user->currentAccount;
        $wallet = Wallet::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'balance' => 0,
        ]);
        $incomeCategory = Category::factory()->income()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'name' => 'Gaji',
        ]);
        $expenseCategory = Category::factory()->expense()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'name' => 'Konsumsi',
        ]);

        $date = now()->startOfMonth()->addDays(4);
        $cashflow = app(CashflowService::class);

        $cashflow->create($account, $user, [
            'wallet_id' => $wallet->id,
            'category_id' => $incomeCategory->id,
            'type' => Category::TYPE_INCOME,
            'amount' => 500000,
            'description' => 'Gaji bulan ini',
            'transaction_date' => $date->toDateString(),
        ]);

        $cashflow->create($account, $user, [
            'wallet_id' => $wallet->id,
            'category_id' => $expenseCategory->id,
            'type' => Category::TYPE_EXPENSE,
            'amount' => 45000,
            'description' => 'Makan siang',
            'transaction_date' => $date->toDateString(),
        ]);

        $response = $this->actingAs($user)->get('/calendar?month='.$date->format('Y-m'));

        $response->assertOk()
            ->assertSee('Kalender Arus Kas')
            ->assertSee('Gaji bulan ini')
            ->assertSee('Makan siang')
            ->assertSee('2 transaksi');

        $response->assertViewHas('monthIncome', 500000.0);
        $response->assertViewHas('monthExpense', 45000.0);
        $response->assertViewHas('monthNet', 455000.0);

        $details = $response->viewData('dayDetails')[$date->toDateString()];
        $this->assertSame('Budi', $details[0]['user']);
        $this->assertSame('Budi', $details[1]['user']);
    }

    public function test_calendar_only_uses_the_current_accounts_transactions(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        app(CashflowService::class)->create($other->currentAccount, $other, [
            'wallet_id' => Wallet::factory()->create([
                'account_id' => $other->currentAccount->id,
                'user_id' => $other->id,
            ])->id,
            'type' => Category::TYPE_INCOME,
            'amount' => 999999,
            'description' => 'Milik orang lain',
            'transaction_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->get('/calendar');
        $dates = $response->viewData('days');

        $this->assertSame(0.0, collect($dates)->sum('income'));
        $response->assertDontSee('Milik orang lain');
    }

    public function test_member_can_see_shared_transaction_with_inputter_name_in_calendar(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create(['name' => 'Siti Anggota']);
        $account = $owner->currentAccount;

        $account->members()->attach($member->id, ['role' => 'member', 'joined_at' => now()]);
        $member->forceFill(['current_account_id' => $account->id])->save();

        $wallet = Wallet::factory()->create([
            'account_id' => $account->id,
            'user_id' => $owner->id,
        ]);

        app(CashflowService::class)->create($account, $member, [
            'wallet_id' => $wallet->id,
            'type' => Category::TYPE_EXPENSE,
            'amount' => 25000,
            'description' => 'Belanja bersama',
            'transaction_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($member)->get('/calendar');

        $response->assertOk()
            ->assertSee('Belanja bersama')
            ->assertSee('Siti Anggota');

        $details = collect($response->viewData('dayDetails'))->flatten(1);
        $this->assertSame('Siti Anggota', $details->firstWhere('description', 'Belanja bersama')['user']);
    }
}
