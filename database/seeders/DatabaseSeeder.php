<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\AccountService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => hash('sha256', 'password'),
                'email_verified_at' => now(),
            ],
        );

        // Pastikan akun demo selalu bisa dipakai walaupun hash lama tidak cocok.
        if (! Hash::check('password', $user->password)) {
            $user->update(['password' => 'password']);
        }

        $account = app(AccountService::class)->ensureCurrentAccount($user);

        DefaultCategorySeeder::seedFor($account, $user);
    }
}
