<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultCategorySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Account::with('owner')->get()->each(function (Account $account): void {
            if ($account->owner) {
                self::seedFor($account, $account->owner);
            }
        });
    }

    /**
     * Berikan dompet dan kategori awal untuk akun baru.
     */
    public static function seedFor(Account $account, User $creator): void
    {
        $account->sharedWallets()->firstOrCreate(
            ['name' => 'Kas Utama'],
            ['balance' => 0, 'user_id' => $creator->id],
        );

        $categories = [
            ['name' => 'Gaji', 'type' => Category::TYPE_INCOME, 'icon' => 'ti-briefcase'],
            ['name' => 'Penjualan', 'type' => Category::TYPE_INCOME, 'icon' => 'ti-shopping-cart'],
            ['name' => 'Bonus', 'type' => Category::TYPE_INCOME, 'icon' => 'ti-gift'],
            ['name' => 'Operasional', 'type' => Category::TYPE_EXPENSE, 'icon' => 'ti-settings'],
            ['name' => 'Konsumsi', 'type' => Category::TYPE_EXPENSE, 'icon' => 'ti-coffee'],
            ['name' => 'Transportasi', 'type' => Category::TYPE_EXPENSE, 'icon' => 'ti-car'],
            ['name' => 'Tagihan', 'type' => Category::TYPE_EXPENSE, 'icon' => 'ti-receipt'],
        ];

        foreach ($categories as $category) {
            $account->categories()->firstOrCreate(
                ['name' => $category['name'], 'type' => $category['type']],
                ['icon' => $category['icon'], 'user_id' => $creator->id],
            );
        }
    }
}
