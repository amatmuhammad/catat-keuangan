<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use App\Services\AccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(private readonly AccountService $accountService) {}

    /**
     * Halaman anggota akun aktif dan form penambahan anggota.
     */
    public function members(Request $request): View
    {
        $account = $this->currentAccount($request);

        $members = $account->members()
            ->withCount([
                'transactions as account_transactions_count' => fn ($query) => $query->where('account_id', $account->id),
            ])
            ->orderByRaw("CASE WHEN account_user.role = 'owner' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        return view('account.members', [
            'members' => $members,
            'isOwner' => $account->isOwner($request->user()),
            'maxMembers' => Account::MAX_MEMBERS,
        ]);
    }

    /**
     * Tambahkan pengguna terdaftar ke akun aktif. Hanya pemilik akun.
     */
    public function addMember(Request $request): RedirectResponse
    {
        $account = $this->currentAccount($request);
        $this->ensureOwner($account, $request);

        $attributes = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::exists('users', 'email'),
            ],
        ], [
            'email.required' => 'Email anggota wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tersebut belum terdaftar. Minta anggota mendaftar terlebih dahulu.',
            'email.max' => 'Email terlalu panjang.',
        ]);

        $member = User::query()->where('email', $attributes['email'])->firstOrFail();

        try {
            $this->accountService->addMember($account, $member, $request->user());
        } catch (ValidationException $exception) {
            return back()->withInput()->withErrors($exception->errors());
        }

        return back()->with('status', $member->name.' berhasil ditambahkan ke akun '.$account->name.' dan akun bersama ini otomatis menjadi akun aktifnya.');
    }

    public function removeMember(Request $request, User $user): RedirectResponse
    {
        $account = $this->currentAccount($request);
        $this->ensureOwner($account, $request);

        try {
            $this->accountService->removeMember($account, $user, $request->user());
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return back()->with('status', $user->name.' berhasil dikeluarkan dari akun '.$account->name.'.');
    }

    /**
     * Pindah akun aktif, hanya untuk akun yang memang diikuti pengguna.
     */
    public function switchAccount(Request $request): RedirectResponse
    {
        $attributes = $request->validate([
            'account_id' => ['required', 'integer'],
        ]);

        $account = $request->user()->accounts()
            ->whereKey($attributes['account_id'])
            ->firstOrFail();

        $this->accountService->switchTo($request->user(), $account);

        return back()->with('status', 'Sekarang menggunakan '.$account->name.'.');
    }

    private function ensureOwner(Account $account, Request $request): void
    {
        abort_unless($account->isOwner($request->user()), 403, 'Hanya pemilik akun yang dapat mengelola anggota.');
    }
}
