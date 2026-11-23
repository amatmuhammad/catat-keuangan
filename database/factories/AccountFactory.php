<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use App\Services\AccountService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Akun '.fake()->unique()->company(),
            'owner_id' => User::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Account $account): void {
            if (! $account->members()->whereKey($account->owner_id)->exists()) {
                $account->members()->attach($account->owner_id, [
                    'role' => Account::ROLE_OWNER,
                    'joined_at' => now(),
                ]);
            }

            app(AccountService::class)->ensurePersonalWallet($account, $account->owner);
        });
    }
}
