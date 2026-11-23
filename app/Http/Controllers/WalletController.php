<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WalletController extends Controller
{
    /**
     * Halaman manajemen dompet akun aktif.
     */
    public function index(Request $request): View
    {
        $account = $this->currentAccount($request);

        $wallets = $account->visibleWallets($request->user())
            ->withCount('transactions')
            ->orderByDesc('is_shared')
            ->orderBy('name')
            ->get();

        return view('wallets.index', [
            'wallets' => $wallets,
            'sharedBalance' => $wallets->filter(fn (Wallet $wallet) => $wallet->isShared())
                ->sum(fn (Wallet $wallet) => (float) $wallet->balance),
            'personalBalance' => $wallets->reject(fn (Wallet $wallet) => $wallet->isShared())
                ->sum(fn (Wallet $wallet) => (float) $wallet->balance),
            'sharedCount' => $wallets->filter(fn (Wallet $wallet) => $wallet->isShared())->count(),
            'personalCount' => $wallets->reject(fn (Wallet $wallet) => $wallet->isShared())->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $account = $this->currentAccount($request);
        $isShared = $request->boolean('is_shared');

        $uniqueName = Rule::unique('wallets', 'name')
            ->where('account_id', $account->id)
            ->where('is_shared', $isShared);

        if (! $isShared) {
            $uniqueName->where('user_id', $request->user()->id);
        }

        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:50', $uniqueName],
            'is_shared' => ['required', 'boolean'],
        ], [
            'name.unique' => 'Nama dompet tersebut sudah ada.',
            'is_shared.required' => 'Pilih jenis dompet.',
        ]);

        $wallet = $account->wallets()->create([
            'user_id' => $request->user()->id,
            'name' => $attributes['name'],
            'balance' => 0,
            'is_shared' => $isShared,
        ]);

        $scope = $wallet->isShared() ? 'bersama' : 'pribadi';

        ActivityLog::record(
            $account,
            $request->user(),
            ActivityLog::WALLET_CREATED,
            'Membuat dompet '.$scope.' "'.$wallet->name.'".',
            $wallet,
            ['wallet_name' => $wallet->name, 'is_shared' => $wallet->isShared()],
            isShared: $wallet->isShared(),
        );

        return redirect()->route('wallets.index')->with('status', 'Dompet '.$scope.' berhasil ditambahkan.');
    }

    public function update(Request $request, Wallet $wallet): RedirectResponse
    {
        $account = $this->currentAccount($request);
        $this->ensureVisible($request, $wallet);

        $uniqueName = Rule::unique('wallets', 'name')
            ->where('account_id', $account->id)
            ->where('is_shared', $wallet->isShared())
            ->ignore($wallet->id);

        if (! $wallet->isShared()) {
            $uniqueName->where('user_id', $wallet->user_id);
        }

        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:50', $uniqueName],
        ], [
            'name.unique' => 'Nama dompet tersebut sudah ada.',
        ]);

        $oldName = $wallet->name;
        $wallet->update(['name' => $attributes['name']]);

        $scope = $wallet->isShared() ? 'bersama' : 'pribadi';

        ActivityLog::record(
            $account,
            $request->user(),
            ActivityLog::WALLET_UPDATED,
            'Mengubah nama dompet '.$scope.' "'.$oldName.'" menjadi "'.$wallet->name.'".',
            $wallet,
            ['old_name' => $oldName, 'new_name' => $wallet->name, 'is_shared' => $wallet->isShared()],
            isShared: $wallet->isShared(),
        );

        return redirect()->route('wallets.index')->with('status', 'Nama dompet berhasil diperbarui.');
    }

    public function destroy(Request $request, Wallet $wallet): RedirectResponse
    {
        $account = $this->currentAccount($request);
        $this->ensureVisible($request, $wallet);

        if ($wallet->transactions()->exists()) {
            return back()->with('error', 'Dompet masih memiliki transaksi. Pindahkan atau hapus transaksinya terlebih dahulu.');
        }

        $scope = $wallet->isShared() ? 'bersama' : 'pribadi';
        $remaining = $wallet->isShared()
            ? $account->sharedWallets()->count()
            : $account->personalWalletsFor($request->user())->count();

        if ($remaining <= 1) {
            return back()->with('error', 'Minimal satu dompet '.$scope.' harus tetap ada.');
        }

        $name = $wallet->name;
        $isShared = $wallet->isShared();
        $wallet->delete();

        ActivityLog::record(
            $account,
            $request->user(),
            ActivityLog::WALLET_DELETED,
            'Menghapus dompet '.$scope.' "'.$name.'".',
            null,
            ['wallet_name' => $name, 'is_shared' => $isShared],
            isShared: $isShared,
        );

        return redirect()->route('wallets.index')->with('status', 'Dompet berhasil dihapus.');
    }

    private function ensureVisible(Request $request, Wallet $wallet): void
    {
        $belongsToAccount = (int) $wallet->account_id === (int) $this->currentAccount($request)->id;
        $isVisible = $wallet->isShared() || (int) $wallet->user_id === (int) $request->user()->id;

        abort_unless($belongsToAccount && $isVisible, 404);
    }
}
