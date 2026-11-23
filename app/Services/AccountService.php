<?php

namespace App\Services;

use App\Models\Account;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Wallet;
use Database\Seeders\DefaultCategorySeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountService
{
    /**
     * Buat akun pribadi untuk pengguna, atau aktifkan akun miliknya yang sudah ada.
     */
    public function createPersonalAccount(User $user, bool $seedDefaults = false): Account
    {
        $existing = $user->ownedAccounts()->orderBy('id')->first();

        if ($existing) {
            $this->activate($user, $existing);
            $this->ensurePersonalWallet($existing, $user);

            if ($seedDefaults) {
                DefaultCategorySeeder::seedFor($existing, $user);
            }

            return $existing;
        }

        return DB::transaction(function () use ($user, $seedDefaults): Account {
            $account = Account::create([
                'name' => 'Akun '.$user->name,
                'owner_id' => $user->id,
            ]);

            $account->members()->attach($user->id, [
                'role' => Account::ROLE_OWNER,
                'joined_at' => now(),
            ]);

            $this->ensurePersonalWallet($account, $user);
            $this->activate($user, $account);

            ActivityLog::record(
                $account,
                $user,
                ActivityLog::ACCOUNT_CREATED,
                'Akun "'.$account->name.'" dibuat.',
                $account,
                ['account_name' => $account->name],
            );

            if ($seedDefaults) {
                DefaultCategorySeeder::seedFor($account, $user);
            }

            return $account;
        });
    }

    /**
     * Pastikan pengguna memiliki akun aktif yang masih menjadi anggotanya.
     */
    public function ensureCurrentAccount(User $user): Account
    {
        $current = $user->currentAccount()->first();

        if ($current && $current->hasMember($user)) {
            return $current;
        }

        $fallback = $user->accounts()->orderBy('accounts.id')->first();

        if ($fallback) {
            $this->activate($user, $fallback);

            return $fallback;
        }

        return $this->createPersonalAccount($user);
    }

    public function activate(User $user, Account $account): void
    {
        if (! $account->hasMember($user)) {
            throw new AuthorizationException('Anda bukan anggota akun ini.');
        }

        $user->forceFill(['current_account_id' => $account->id])->save();
        $user->setRelation('currentAccount', $account);
    }

    public function switchTo(User $user, Account $account): void
    {
        $this->activate($user, $account);
    }

    public function addMember(Account $account, User $member, User $actor): void
    {
        $this->ensureOwner($account, $actor);

        DB::transaction(function () use ($account, $member, $actor): void {
            $lockedAccount = Account::query()->whereKey($account->id)->lockForUpdate()->firstOrFail();

            if ($lockedAccount->hasMember($member)) {
                throw ValidationException::withMessages([
                    'email' => $member->name.' sudah menjadi anggota akun ini.',
                ]);
            }

            if ($lockedAccount->memberCount() >= Account::MAX_MEMBERS) {
                throw ValidationException::withMessages([
                    'email' => 'Akun sudah memiliki '.Account::MAX_MEMBERS.' anggota. Keluarkan salah satu anggota terlebih dahulu.',
                ]);
            }

            $lockedAccount->members()->attach($member->id, [
                'role' => Account::ROLE_MEMBER,
                'joined_at' => now(),
            ]);

            $this->ensurePersonalWallet($lockedAccount, $member);

            // Anggota baru langsung memakai akun bersama sebagai akun aktif agar
            // melihat saldo bersama yang sama dengan anggota lainnya.
            $this->activate($member, $lockedAccount);

            ActivityLog::record(
                $lockedAccount,
                $actor,
                ActivityLog::MEMBER_ADDED,
                'Menambahkan '.$member->name.' ('.$member->email.') sebagai anggota.',
                $member,
                ['member_name' => $member->name, 'member_email' => $member->email],
            );
        });
    }

    public function removeMember(Account $account, User $member, User $actor): void
    {
        $this->ensureOwner($account, $actor);

        if ($account->isOwner($member)) {
            throw ValidationException::withMessages([
                'member' => 'Pemilik akun tidak dapat dikeluarkan.',
            ]);
        }

        if (! $account->hasMember($member)) {
            throw ValidationException::withMessages([
                'member' => 'Pengguna ini bukan anggota akun.',
            ]);
        }

        DB::transaction(function () use ($account, $member, $actor): void {
            $account->members()->detach($member->id);

            ActivityLog::record(
                $account,
                $actor,
                ActivityLog::MEMBER_REMOVED,
                'Mengeluarkan '.$member->name.' ('.$member->email.') dari akun.',
                $member,
                ['member_name' => $member->name, 'member_email' => $member->email],
            );

            $member->refresh();

            if ((int) $member->current_account_id === (int) $account->id) {
                $replacement = $member->accounts()->orderBy('accounts.id')->first();

                if ($replacement) {
                    $this->activate($member, $replacement);
                } else {
                    $this->createPersonalAccount($member);
                }
            }
        });
    }

    /**
     * Dompet pribadi selalu tersedia untuk setiap anggota akun.
     */
    public function ensurePersonalWallet(Account $account, User $user): Wallet
    {
        $wallet = $account->personalWalletsFor($user)->orderBy('id')->first();

        if ($wallet) {
            return $wallet;
        }

        return $account->wallets()->create([
            'user_id' => $user->id,
            'name' => 'Saldo Pribadi',
            'balance' => 0,
            'is_shared' => false,
        ]);
    }

    private function ensureOwner(Account $account, User $user): void
    {
        if (! $account->isOwner($user)) {
            throw new AuthorizationException('Hanya pemilik akun yang dapat mengelola anggota.');
        }
    }
}
