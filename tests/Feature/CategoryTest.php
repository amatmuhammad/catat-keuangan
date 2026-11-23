<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_income_and_expense_categories(): void
    {
        $user = User::factory()->create();
        $account = $user->currentAccount;

        $this->actingAs($user)->post('/categories', [
            'name' => 'Freelance',
            'type' => Category::TYPE_INCOME,
        ])->assertRedirect(route('categories.index'));

        $this->actingAs($user)->post('/categories', [
            'name' => 'Langganan',
            'type' => Category::TYPE_EXPENSE,
        ])->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'account_id' => $account->id,
            'user_id' => $user->id,
            'name' => 'Freelance',
            'type' => Category::TYPE_INCOME,
        ]);

        $this->assertDatabaseHas('categories', [
            'account_id' => $account->id,
            'name' => 'Langganan',
            'type' => Category::TYPE_EXPENSE,
        ]);
    }

    public function test_category_name_must_be_unique_for_the_same_type(): void
    {
        $user = User::factory()->create();

        Category::factory()->income()->create([
            'account_id' => $user->currentAccount->id,
            'user_id' => $user->id,
            'name' => 'Gaji',
        ]);

        $this->actingAs($user)->post('/categories', [
            'name' => 'Gaji',
            'type' => Category::TYPE_INCOME,
        ])->assertSessionHasErrors('name');

        $this->actingAs($user)->post('/categories', [
            'name' => 'Gaji',
            'type' => Category::TYPE_EXPENSE,
        ])->assertRedirect(route('categories.index'));
    }

    public function test_category_type_cannot_change_once_used_by_a_transaction(): void
    {
        $user = User::factory()->create();
        $account = $user->currentAccount;

        $category = Category::factory()->expense()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'name' => 'Operasional',
        ]);

        $this->createTransaction($user, $category);

        $this->actingAs($user)->put(route('categories.update', $category), [
            'name' => 'Operasional',
            'type' => Category::TYPE_INCOME,
        ])->assertSessionHas('error');

        $this->assertSame(Category::TYPE_EXPENSE, $category->fresh()->type);
    }

    public function test_category_used_by_a_transaction_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $account = $user->currentAccount;

        $category = Category::factory()->expense()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'name' => 'Konsumsi',
        ]);

        $this->createTransaction($user, $category);

        $this->actingAs($user)
            ->delete(route('categories.destroy', $category))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_unused_category_can_be_deleted(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->income()->create([
            'account_id' => $user->currentAccount->id,
            'user_id' => $user->id,
            'name' => 'Bonus Lama',
        ]);

        $this->actingAs($user)
            ->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_user_cannot_touch_a_category_from_another_account(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $category = Category::factory()->income()->create([
            'account_id' => $owner->currentAccount->id,
            'user_id' => $owner->id,
            'name' => 'Kategori Pemilik',
        ]);

        $this->actingAs($intruder)
            ->put(route('categories.update', $category), [
                'name' => 'Dicuri',
                'type' => Category::TYPE_INCOME,
            ])
            ->assertNotFound();

        $this->actingAs($intruder)
            ->delete(route('categories.destroy', $category))
            ->assertNotFound();

        $this->assertSame('Kategori Pemilik', $category->fresh()->name);
    }

    public function test_category_page_lists_account_categories_with_usage_count(): void
    {
        $user = User::factory()->create();
        $account = $user->currentAccount;

        $category = Category::factory()->income()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'name' => 'Penjualan Toko',
        ]);

        $this->createTransaction($user, $category);

        $this->actingAs($user)->get(route('categories.index'))
            ->assertOk()
            ->assertSee('Penjualan Toko');
    }

    private function createTransaction(User $user, Category $category): Transaction
    {
        $wallet = Wallet::factory()->create([
            'account_id' => $user->currentAccount->id,
            'user_id' => $user->id,
            'name' => 'Kas Utama '.uniqid(),
            'is_shared' => true,
        ]);

        return Transaction::factory()->create([
            'account_id' => $user->currentAccount->id,
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'type' => $category->type,
        ]);
    }
}
