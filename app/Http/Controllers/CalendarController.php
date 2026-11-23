<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    /**
     * Tampilkan arus kas bulanan akun aktif dalam bentuk kalender beserta rincian per hari.
     */
    public function __invoke(Request $request): View
    {
        $account = $this->currentAccount($request);
        $month = (string) $request->query('month', '');

        $cursor = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month) === 1
            ? Carbon::createFromFormat('Y-m-d', $month.'-01')->startOfMonth()
            : now()->startOfMonth();

        $gridStart = $cursor->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $cursor->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $transactions = $account->visibleTransactions($request->user())
            ->with(['wallet', 'category', 'user'])
            ->whereBetween('transaction_date', [$gridStart->toDateString(), $gridEnd->toDateString()])
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get()
            ->groupBy(fn ($transaction) => $transaction->transaction_date->toDateString());

        $days = [];

        foreach (CarbonPeriod::create($gridStart, $gridEnd) as $date) {
            $key = $date->toDateString();
            $items = $transactions->get($key, collect());

            $days[] = [
                'date' => $key,
                'day' => (int) $date->format('j'),
                'in_month' => $date->year === $cursor->year && $date->month === $cursor->month,
                'is_today' => $date->isToday(),
                'income' => (float) $items->where('type', Category::TYPE_INCOME)->sum('amount'),
                'expense' => (float) $items->where('type', Category::TYPE_EXPENSE)->sum('amount'),
                'count' => $items->count(),
                'details' => $items->map(fn ($transaction) => [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'amount' => (float) $transaction->amount,
                    'description' => $transaction->description
                        ?: ($transaction->category?->name ?? 'Transaksi'),
                    'category' => $transaction->category?->name,
                    'wallet' => $transaction->wallet?->name ?? 'Dompet',
                    'scope' => $transaction->isShared() ? 'Bersama' : 'Pribadi',
                    'user' => $transaction->user?->name ?? 'Pengguna',
                    'edit_url' => route('transactions.edit', $transaction),
                ])->values()->all(),
            ];
        }

        $monthDays = collect($days)->filter(fn (array $day) => $day['in_month']);
        $monthIncome = (float) $monthDays->sum('income');
        $monthExpense = (float) $monthDays->sum('expense');

        return view('calendar.index', [
            'days' => $days,
            'dayDetails' => collect($days)->pluck('details', 'date')->all(),
            'monthCursor' => $cursor,
            'monthIncome' => $monthIncome,
            'monthExpense' => $monthExpense,
            'monthNet' => $monthIncome - $monthExpense,
            'prevMonth' => $cursor->copy()->subMonthNoOverflow()->format('Y-m'),
            'nextMonth' => $cursor->copy()->addMonthNoOverflow()->format('Y-m'),
            'transactionsUrl' => route('transactions.index', [
                'date_from' => $cursor->toDateString(),
                'date_to' => $cursor->copy()->endOfMonth()->toDateString(),
            ]),
        ]);
    }
}
