@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
    <div class="row mb-6">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
                <div>
                    <h1 class="page-title mb-1">Riwayat Transaksi</h1>
                    <p class="mb-0 text-secondary">
                        Arus kas bersama dan pribadi Anda di {{ $currentAccount->name }}. Setiap baris menampilkan penginput.
                    </p>
                </div>
                <a href="{{ route('transactions.create') }}" class="btn btn-primary">Tambah Transaksi</a>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 g-6 mb-6">
        <div class="col">
            <div class="card card-lg">
                <div class="card-body d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <div class="small text-secondary mb-1">Pemasukan (sesuai filter)</div>
                        <div class="fs-4 fw-bold money money-income">
                            +Rp {{ number_format($filteredIncome, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="icon-shape icon-lg rounded-circle bg-success-subtle text-success-emphasis">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 19l0 -14" />
                            <path d="M16 9l-4 -4l-4 4" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg">
                <div class="card-body d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <div class="small text-secondary mb-1">Pengeluaran (sesuai filter)</div>
                        <div class="fs-4 fw-bold money money-expense">
                            -Rp {{ number_format($filteredExpense, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="icon-shape icon-lg rounded-circle bg-danger-subtle text-danger-emphasis">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M16 15l-4 4l-4 -4" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-lg mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('transactions.index') }}" class="filter-grid align-items-end">
                <div class="filter-grid-item">
                    <label for="q" class="form-label">Cari catatan</label>
                    <input type="search" id="q" name="q" class="form-control" value="{{ $filters['q'] ?? '' }}"
                        placeholder="Keterangan...">
                </div>
                <div>
                    <label for="type" class="form-label">Jenis</label>
                    <select id="type" name="type" class="form-select">
                        <option value="">Semua</option>
                        <option value="income" @selected(($filters['type'] ?? '') === 'income')>Pemasukan</option>
                        <option value="expense" @selected(($filters['type'] ?? '') === 'expense')>Pengeluaran</option>
                    </select>
                </div>
                <div>
                    <label for="wallet_id" class="form-label">Dompet</label>
                    <select id="wallet_id" name="wallet_id" class="form-select">
                        <option value="">Semua</option>
                        @foreach ($wallets as $wallet)
                            <option value="{{ $wallet->id }}" @selected((string) ($filters['wallet_id'] ?? '') === (string) $wallet->id)>
                                {{ $wallet->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="category_id" class="form-label">Kategori</label>
                    <select id="category_id" name="category_id" class="form-select">
                        <option value="">Semua</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $category->id)>
                                {{ $category->name }} ({{ $category->type === 'income' ? 'masuk' : 'keluar' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date_from" class="form-label">Dari tanggal</label>
                    <input type="date" id="date_from" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                </div>
                <div>
                    <label for="date_to" class="form-label">Sampai tanggal</label>
                    <input type="date" id="date_to" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Terapkan</button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-white">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-lg">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Daftar Transaksi</h5>
                <span class="small text-secondary">{{ $transactions->total() }} transaksi</span>
            </div>

            @forelse ($transactions as $transaction)
                <div class="transaction-row">
                    <div class="transaction-icon {{ $transaction->isIncome() ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            @if ($transaction->isIncome())
                                <path d="M12 19l0 -14" />
                                <path d="M16 9l-4 -4l-4 4" />
                            @else
                                <path d="M12 5l0 14" />
                                <path d="M16 15l-4 4l-4 -4" />
                            @endif
                        </svg>
                    </div>

                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold text-truncate">
                            {{ $transaction->description ?: ($transaction->category?->name ?? 'Transaksi') }}
                            <span class="badge {{ $transaction->isShared() ? 'bg-success-subtle text-success-emphasis' : 'bg-primary-subtle text-primary' }} ms-1">
                                {{ $transaction->isShared() ? 'Bersama' : 'Pribadi' }}
                            </span>
                        </div>
                        <div class="small text-secondary text-truncate">
                            {{ $transaction->category?->name ?? 'Tanpa kategori' }}
                            · {{ $transaction->wallet?->name }}
                            · {{ $transaction->transaction_date->translatedFormat('d M Y') }}
                            · oleh {{ $transaction->user?->name ?? 'Pengguna' }}
                        </div>
                    </div>

                    <div class="text-end">
                        <div class="fw-bold money {{ $transaction->isIncome() ? 'money-income' : 'money-expense' }}">
                            {{ $transaction->isIncome() ? '+' : '-' }}{{ $transaction->formattedAmount() }}
                        </div>
                        <div class="transaction-actions mt-1">
                            <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-white">Edit</a>
                            <form method="POST" action="{{ route('transactions.destroy', $transaction) }}"
                                onsubmit="return confirm('Hapus transaksi ini? Saldo dompet akan ikut disesuaikan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="icon-shape icon-xl rounded-circle bg-primary-subtle text-primary mx-auto mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                            <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                        </svg>
                    </div>
                    <p class="text-secondary mb-3">
                        {{ request()->hasAny(['type', 'wallet_id', 'category_id', 'date_from', 'date_to', 'q']) ? 'Tidak ada transaksi yang cocok dengan filter.' : 'Belum ada transaksi yang dicatat.' }}
                    </p>
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm">Catat Transaksi</a>
                </div>
            @endforelse

            @if ($transactions->hasPages())
                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
