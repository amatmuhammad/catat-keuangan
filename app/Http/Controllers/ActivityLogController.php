<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Log aktivitas bersama dan aktivitas pribadi pengguna pada akun aktif.
     */
    public function __invoke(Request $request): View
    {
        $account = $this->currentAccount($request);
        $user = $request->user();

        $memberIds = $account->members()->pluck('users.id')->all();

        $filters = $request->validate([
            'user_id' => ['nullable', 'integer', Rule::in($memberIds)],
            'action' => ['nullable', 'string', 'max:50'],
            'scope' => ['nullable', Rule::in(['shared', 'personal'])],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $visibleLogs = fn () => $account->activityLogs()
            ->where(function (Builder $query) use ($user): void {
                $query->where('is_shared', true)
                    ->orWhere('user_id', $user->id);
            });

        $logs = $visibleLogs()
            ->with('user')
            ->when($filters['user_id'] ?? null, fn ($query, $userId) => $query->where('user_id', $userId))
            ->when($filters['action'] ?? null, fn ($query, $action) => $query->where('action', $action))
            ->when(
                ($filters['scope'] ?? null) === 'shared',
                fn ($query) => $query->where('is_shared', true),
            )
            ->when(
                ($filters['scope'] ?? null) === 'personal',
                fn ($query) => $query->where('is_shared', false),
            )
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $actions = $visibleLogs()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('account.activity', [
            'logs' => $logs,
            'members' => $account->members()->orderBy('name')->get(),
            'filters' => $filters,
            'availableActions' => $actions,
            'totalLogs' => $visibleLogs()->count(),
            'todayLogs' => $visibleLogs()->whereDate('created_at', today())->count(),
        ]);
    }
}
