<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan ringkasan saldo bersama, saldo pribadi, dan arus kas akun aktif.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $account = $this->currentAccount($request);

        $sharedWallets = $account->sharedWallets()
            ->withCount('transactions')
            ->orderBy('name')
            ->get();

        $personalWallets = $account->personalWalletsFor($user)
            ->withCount('transactions')
            ->orderBy('name')
            ->get();

        $sharedBalance = $sharedWallets->sum(fn ($wallet) => (float) $wallet->balance);
        $personalBalance = $personalWallets->sum(fn ($wallet) => (float) $wallet->balance);
        $visibleTransactions = $account->visibleTransactions($user);

        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $previousStart = now()->subMonthNoOverflow()->startOfMonth();
        $previousEnd = now()->subMonthNoOverflow()->endOfMonth();

        $monthly = (clone $visibleTransactions)
            ->whereBetween('transaction_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $previousMonthly = (clone $visibleTransactions)
            ->whereBetween('transaction_date', [$previousStart->toDateString(), $previousEnd->toDateString()])
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $monthlyIncome = (float) ($monthly[Category::TYPE_INCOME] ?? 0);
        $monthlyExpense = (float) ($monthly[Category::TYPE_EXPENSE] ?? 0);

        $recentTransactions = (clone $visibleTransactions)
            ->with(['wallet', 'category', 'user'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        $chartDays = collect(range(6, 0))->map(fn (int $daysAgo) => now()->subDays($daysAgo)->startOfDay());

        $chartTransactions = (clone $visibleTransactions)
            ->whereBetween('transaction_date', [
                $chartDays->first()->toDateString(),
                $chartDays->last()->toDateString(),
            ])
            ->get(['type', 'amount', 'transaction_date'])
            ->groupBy(fn ($transaction) => $transaction->transaction_date->toDateString());

        $chart = [
            'labels' => $chartDays->map(fn (Carbon $day) => $day->translatedFormat('D'))->all(),
            'income' => $chartDays->map(fn (Carbon $day) => (float) $chartTransactions
                ->get($day->toDateString(), collect())
                ->where('type', Category::TYPE_INCOME)
                ->sum('amount'))->all(),
            'expense' => $chartDays->map(fn (Carbon $day) => (float) $chartTransactions
                ->get($day->toDateString(), collect())
                ->where('type', Category::TYPE_EXPENSE)
                ->sum('amount'))->all(),
        ];

        return view('dashboard.index', [
            'wallets' => $sharedWallets->concat($personalWallets),
            'sharedBalance' => $sharedBalance,
            'personalBalance' => $personalBalance,
            'monthlyIncome' => $monthlyIncome,
            'monthlyExpense' => $monthlyExpense,
            'incomeGrowth' => $this->growth($monthlyIncome, (float) ($previousMonthly[Category::TYPE_INCOME] ?? 0)),
            'expenseGrowth' => $this->growth($monthlyExpense, (float) ($previousMonthly[Category::TYPE_EXPENSE] ?? 0)),
            'recentTransactions' => $recentTransactions,
            'chart' => $chart,
            'hasSubscriptions' => $user->pushSubscriptions()->exists(),
        ]);
    }

    /**
     * Persentase perubahan dibanding bulan sebelumnya.
     */
    private function growth(float $current, float $previous): ?float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100.0 : null;
        }

        return (($current - $previous) / $previous) * 100;
    }
}
