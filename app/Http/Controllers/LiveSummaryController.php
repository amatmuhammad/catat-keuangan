<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LiveSummaryController extends Controller
{
    /**
     * Ringkasan saldo bersama dan saldo pribadi untuk sinkronisasi otomatis halaman.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $account = $this->currentAccount($request);
        $user = $request->user();

        $sharedWallets = $account->sharedWallets()
            ->withCount('transactions')
            ->orderBy('name')
            ->get();

        $personalWallets = $account->personalWalletsFor($user)
            ->withCount('transactions')
            ->orderBy('name')
            ->get();

        $wallets = $sharedWallets->concat($personalWallets);

        return response()->json([
            'account_id' => $account->id,
            'shared_balance' => $sharedWallets->sum(fn (Wallet $wallet) => (float) $wallet->balance),
            'personal_balance' => $personalWallets->sum(fn (Wallet $wallet) => (float) $wallet->balance),
            'shared_wallet_count' => $sharedWallets->count(),
            'personal_wallet_count' => $personalWallets->count(),
            'wallets' => $wallets->map(fn (Wallet $wallet) => [
                'id' => $wallet->id,
                'name' => $wallet->name,
                'is_shared' => $wallet->isShared(),
                'balance' => (float) $wallet->balance,
                'transactions_count' => $wallet->transactions_count,
            ])->values(),
        ]);
    }
}
