<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'account_id' => fn (array $attributes) => User::find($attributes['user_id'])?->current_account_id,
            'wallet_id' => fn (array $attributes) => Wallet::factory()->create([
                'user_id' => $attributes['user_id'],
                'account_id' => $attributes['account_id'],
            ])->id,
            'category_id' => null,
            'type' => Category::TYPE_EXPENSE,
            'amount' => fake()->randomFloat(2, 10000, 1000000),
            'description' => fake()->sentence(4),
            'transaction_date' => fake()->dateTimeBetween('-30 days')->format('Y-m-d'),
        ];
    }

    public function income(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Category::TYPE_INCOME,
        ]);
    }

    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Category::TYPE_EXPENSE,
        ]);
    }
}
