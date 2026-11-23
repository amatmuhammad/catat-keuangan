<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Account;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\CashflowService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function __construct(private readonly CashflowService $cashflowService) {}

    /**
     * Riwayat transaksi akun aktif beserta filter dompet, kategori, jenis, dan tanggal.
     */
    public function index(Request $request): View
    {
        $account = $this->currentAccount($request);
        $user = $request->user();

        $filters = $request->validate([
            'type' => ['nullable', Rule::in(Category::types())],
            'wallet_id' => [
                'nullable',
                Rule::exists('wallets', 'id')
                    ->where('account_id', $account->id)
                    ->where(fn ($query) => $query
                        ->where('is_shared', true)
                        ->orWhere('user_id', $user->id)),
            ],
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('account_id', $account->id)],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $query = $account->visibleTransactions($user)->with(['wallet', 'category', 'user']);

        $query
            ->when($filters['type'] ?? null, fn ($builder, $type) => $builder->where('type', $type))
            ->when($filters['wallet_id'] ?? null, fn ($builder, $walletId) => $builder->where('wallet_id', $walletId))
            ->when($filters['category_id'] ?? null, fn ($builder, $categoryId) => $builder->where('category_id', $categoryId))
            ->when($filters['date_from'] ?? null, fn ($builder, $date) => $builder->whereDate('transaction_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($builder, $date) => $builder->whereDate('transaction_date', '<=', $date))
            ->when($filters['q'] ?? null, fn ($builder, $keyword) => $builder->where('description', 'like', '%'.$keyword.'%'));

        $summary = (clone $query)
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return view('transactions.index', [
            'transactions' => $query->orderByDesc('transaction_date')->orderByDesc('id')->paginate(15)->withQueryString(),
            'wallets' => $account->visibleWallets($user)->orderByDesc('is_shared')->orderBy('name')->get(),
            'categories' => $account->categories()->orderBy('type')->orderBy('name')->get(),
            'filters' => $filters,
            'filteredIncome' => (float) ($summary[Category::TYPE_INCOME] ?? 0),
            'filteredExpense' => (float) ($summary[Category::TYPE_EXPENSE] ?? 0),
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $account = $this->currentAccount($request);

        if ($account->visibleWallets($request->user())->doesntExist()) {
            return redirect()->route('wallets.index')
                ->with('error', 'Tambahkan dompet terlebih dahulu sebelum mencatat transaksi.');
        }

        return view('transactions.create', $this->formData($request));
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $account = $this->currentAccount($request);
        $transaction = $this->cashflowService->create($account, $request->user(), $request->validated());

        $this->log($account, $request->user(), ActivityLog::TRANSACTION_CREATED, $transaction);

        return redirect()->route('transactions.index')
            ->with('status', 'Transaksi "'.$this->label($transaction).'" berhasil dicatat oleh '.$request->user()->name.'.');
    }

    public function edit(Request $request, Transaction $transaction): View|RedirectResponse
    {
        $this->ensureVisible($request, $transaction);
        $transaction->loadMissing('wallet');

        return view('transactions.edit', [
            ...$this->formData($request, $transaction),
            'transaction' => $transaction->load(['wallet', 'user']),
        ]);
    }

    public function update(StoreTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->ensureVisible($request, $transaction);

        $validated = $request->validated();
        $targetWallet = $this->currentAccount($request)
            ->visibleWallets($request->user())
            ->find($validated['wallet_id']);

        if ($targetWallet && $transaction->wallet && $targetWallet->isShared() !== $transaction->isShared()) {
            return back()->withInput()->withErrors([
                'wallet_id' => 'Transaksi tidak dapat dipindahkan antara saldo bersama dan saldo pribadi.',
            ]);
        }

        $updated = $this->cashflowService->update($transaction, $validated);

        $this->log($this->currentAccount($request), $request->user(), ActivityLog::TRANSACTION_UPDATED, $updated);

        return redirect()->route('transactions.index')
            ->with('status', 'Transaksi "'.$this->label($updated).'" berhasil diperbarui.');
    }

    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->ensureVisible($request, $transaction);

        $account = $this->currentAccount($request);
        $label = $this->label($transaction->load(['wallet', 'category']));
        $scope = $transaction->isShared() ? 'bersama' : 'pribadi';
        $isShared = $transaction->isShared();

        $this->cashflowService->delete($transaction);

        ActivityLog::record(
            $account,
            $request->user(),
            ActivityLog::TRANSACTION_DELETED,
            'Menghapus transaksi '.$scope.' "'.$label.'" sebesar '.$transaction->formattedAmount().'.',
            null,
            [
                'transaction_id' => $transaction->id,
                'type' => $transaction->type,
                'amount' => (string) $transaction->amount,
                'wallet' => $transaction->wallet?->name,
                'category' => $transaction->category?->name,
                'description' => $transaction->description,
                'transaction_date' => $transaction->transaction_date?->toDateString(),
                'is_shared' => $isShared,
            ],
            isShared: $isShared,
        );

        return redirect()->route('transactions.index')
            ->with('status', 'Transaksi berhasil dihapus dan saldo dompet telah disesuaikan.');
    }

    /**
     * @return array{wallets: Collection<int, Wallet>, categories: Collection<int, Category>}
     */
    private function formData(Request $request, ?Transaction $transaction = null): array
    {
        $account = $this->currentAccount($request);

        $wallets = $account->visibleWallets($request->user());

        if ($transaction?->wallet) {
            $wallets->where('is_shared', $transaction->wallet->is_shared);
        }

        return [
            'wallets' => $wallets->orderByDesc('is_shared')->orderBy('name')->get(),
            'categories' => $account->categories()->orderBy('type')->orderBy('name')->get(),
        ];
    }

    private function ensureVisible(Request $request, Transaction $transaction): void
    {
        $transaction->loadMissing('wallet');

        $belongsToAccount = (int) $transaction->account_id === (int) $this->currentAccount($request)->id;
        $isVisible = $transaction->isShared()
            || (int) $transaction->wallet?->user_id === (int) $request->user()->id;

        abort_unless($belongsToAccount && $isVisible, 404);
    }

    private function label(Transaction $transaction): string
    {
        return $transaction->description ?: ($transaction->category?->name ?? 'tanpa keterangan');
    }

    private function log(Account $account, User $actor, string $action, Transaction $transaction): void
    {
        $verb = $action === ActivityLog::TRANSACTION_CREATED ? 'Mencatat' : 'Mengubah';
        $scope = $transaction->isShared() ? 'bersama' : 'pribadi';

        ActivityLog::record(
            $account,
            $actor,
            $action,
            $verb.' transaksi '.$scope.' "'.$this->label($transaction).'" sebesar '.$transaction->formattedAmount().' pada dompet '.($transaction->wallet?->name ?? 'Dompet').'.',
            $transaction,
            [
                'transaction_id' => $transaction->id,
                'type' => $transaction->type,
                'amount' => (string) $transaction->amount,
                'wallet' => $transaction->wallet?->name,
                'category' => $transaction->category?->name,
                'description' => $transaction->description,
                'transaction_date' => $transaction->transaction_date?->toDateString(),
                'input_by' => $transaction->user?->name,
                'is_shared' => $transaction->isShared(),
            ],
            isShared: $transaction->isShared(),
        );
    }
}
