@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="d-none" data-live-summary data-account-id="{{ $currentAccount->id }}"
        data-live-url="{{ route('live.summary') }}"></div>

    <div class="row mb-6">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
                <div>
                    <p class="text-secondary mb-1 small">{{ now()->translatedFormat('l, d F Y') }}</p>
                    <h1 class="page-title mb-1">Halo, {{ auth()->user()->name }}</h1>
                    <p class="mb-0 text-secondary">
                        Saldo bersama dan saldo pribadi Anda di {{ $currentAccount->name }} · {{ $currentAccount->memberCount() }} anggota.
                        <span class="badge bg-success-subtle text-success-emphasis ms-1" data-live-status
                            title="Saldo diperbarui otomatis setiap 10 detik">Sinkron otomatis</span>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('calendar') }}" class="btn btn-white">Kalender</a>
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="me-1">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Catat Transaksi
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('partials.account-scope-notice')

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-6 mb-6">
        <div class="col">
            <div class="card card-lg balance-card dashboard-stat-card h-100">
                <div class="card-body d-flex flex-column gap-5">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-white bg-opacity-25 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                                <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" />
                            </svg>
                        </div>
                        <div>
                            <div class="stat-label text-white">Saldo Bersama</div>
                            <div class="small text-white-50">Kas utama + dompet bersama</div>
                        </div>
                    </div>
                    <div class="stat-amount-wrap">
                        <div class="stat-amount-row text-white">
                            <span class="stat-currency">Rp</span>
                            <span class="stat-amount" data-live-field="shared_balance"
                                title="Rp {{ number_format($sharedBalance, 0, ',', '.') }}">
                                {{ number_format($sharedBalance, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="text-white-50 small mt-2">
                            <span data-live-field="shared_wallet_count">{{ $wallets->filter(fn ($wallet) => $wallet->isShared())->count() }}</span>
                            dompet · terlihat semua anggota
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card card-lg dashboard-stat-card stat-card-personal h-100">
                <div class="card-body d-flex flex-column gap-5">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-primary-subtle text-primary">
                            <i class="ti ti-user fs-3"></i>
                        </div>
                        <div>
                            <div class="stat-label text-primary">Saldo Pribadi</div>
                            <div class="small text-secondary">Hanya terlihat oleh Anda</div>
                        </div>
                    </div>
                    <div>
                        <div class="stat-amount-row">
                            <span class="stat-currency text-primary">Rp</span>
                            <span class="stat-amount text-primary" data-live-field="personal_balance"
                                title="Rp {{ number_format($personalBalance, 0, ',', '.') }}">
                                {{ number_format($personalBalance, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="text-secondary small mt-2">
                            <span data-live-field="personal_wallet_count">{{ $wallets->reject(fn ($wallet) => $wallet->isShared())->count() }}</span>
                            dompet pribadi
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card card-lg dashboard-stat-card stat-card-income h-100">
                <div class="card-body d-flex flex-column gap-5">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-success-subtle text-success-emphasis">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 17l6 -6l4 4l8 -8" />
                                <path d="M14 7l7 0l0 7" />
                            </svg>
                        </div>
                        <div>
                            <div class="stat-label text-success-emphasis">Pemasukan</div>
                            <div class="small text-secondary">Bulan ini · bersama + pribadi</div>
                        </div>
                    </div>
                    <div>
                        <div class="stat-amount-row">
                            <span class="stat-currency money-income">Rp</span>
                            <span class="stat-amount money-income" title="Rp {{ number_format($monthlyIncome, 0, ',', '.') }}">
                                {{ number_format($monthlyIncome, 0, ',', '.') }}
                            </span>
                        </div>
                        @if ($incomeGrowth !== null)
                            <span class="badge {{ $incomeGrowth >= 0 ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                                {{ $incomeGrowth >= 0 ? '+' : '' }}{{ number_format($incomeGrowth, 1, ',', '.') }}%
                            </span>
                            <span class="text-secondary small">vs bulan lalu</span>
                        @else
                            <span class="text-secondary small">Belum ada data bulan lalu</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card card-lg dashboard-stat-card stat-card-expense h-100">
                <div class="card-body d-flex flex-column gap-5">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-danger-subtle text-danger-emphasis">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 7l6 6l4 -4l8 8" />
                                <path d="M21 10l0 7l-7 0" />
                            </svg>
                        </div>
                        <div>
                            <div class="stat-label text-danger-emphasis">Pengeluaran</div>
                            <div class="small text-secondary">Bulan ini · bersama + pribadi</div>
                        </div>
                    </div>
                    <div>
                        <div class="stat-amount-row">
                            <span class="stat-currency money-expense">Rp</span>
                            <span class="stat-amount money-expense" title="Rp {{ number_format($monthlyExpense, 0, ',', '.') }}">
                                {{ number_format($monthlyExpense, 0, ',', '.') }}
                            </span>
                        </div>
                        @if ($expenseGrowth !== null)
                            <span class="badge {{ $expenseGrowth <= 0 ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                                {{ $expenseGrowth >= 0 ? '+' : '' }}{{ number_format($expenseGrowth, 1, ',', '.') }}%
                            </span>
                            <span class="text-secondary small">vs bulan lalu</span>
                        @else
                            <span class="text-secondary small">Belum ada data bulan lalu</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-xl-8 col-12">
            <div class="card card-lg dashboard-card h-100">
                <div class="card-body d-flex flex-column gap-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h5 class="mb-0">Arus Kas 7 Hari Terakhir</h5>
                        <div class="d-flex align-items-center gap-3 small">
                            <span class="d-inline-flex align-items-center gap-1">
                                <span class="d-inline-block rounded-circle" style="width:8px;height:8px;background:#1a6be0"></span>
                                Pemasukan
                            </span>
                            <span class="d-inline-flex align-items-center gap-1">
                                <span class="d-inline-block rounded-circle" style="width:8px;height:8px;background:#f5a623"></span>
                                Pengeluaran
                            </span>
                        </div>
                    </div>
                    <div id="cashflowChart"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-12">
            <div class="card card-lg dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Saldo Dompet</h5>
                        <a href="{{ route('wallets.index') }}" class="btn btn-white btn-sm">Kelola</a>
                    </div>

                    @forelse ($wallets as $wallet)
                        <div class="transaction-row">
                            <div class="transaction-icon bg-primary-subtle text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                                    <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" />
                                </svg>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold text-truncate">{{ $wallet->name }}</div>
                                <div class="small text-secondary">
                                    <span data-live-wallet-count="{{ $wallet->id }}">{{ $wallet->transactions_count ?? $wallet->transactions()->count() }}</span>
                                    transaksi
                                    <span class="badge {{ $wallet->isShared() ? 'bg-success-subtle text-success-emphasis' : 'bg-primary-subtle text-primary' }} ms-1">
                                        {{ $wallet->isShared() ? 'Bersama' : 'Pribadi' }}
                                    </span>
                                </div>
                            </div>
                            <div class="fw-bold money" data-live-wallet-balance="{{ $wallet->id }}">{{ $wallet->formattedBalance() }}</div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <p class="text-secondary mb-3">Belum ada dompet.</p>
                            <a href="{{ route('wallets.index') }}" class="btn btn-primary btn-sm">Tambah Dompet</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-xl-8 col-12">
            <div class="card card-lg dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Transaksi Terakhir</h5>
                        <a href="{{ route('transactions.index') }}" class="btn btn-white btn-sm">Lihat semua</a>
                    </div>

                    @forelse ($recentTransactions as $transaction)
                        <div class="transaction-row">
                            <div class="transaction-icon {{ $transaction->isIncome() ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    @if ($transaction->isIncome())
                                        <path d="M12 5l0 14" />
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
                                </div>
                                <div class="small text-secondary text-truncate">
                                    {{ $transaction->category?->name ?? 'Tanpa kategori' }}
                                    · {{ $transaction->wallet?->name }}
                                    · {{ $transaction->transaction_date->translatedFormat('d M Y') }}
                                    · oleh {{ $transaction->user?->name ?? 'Pengguna' }}
                                    <span class="badge {{ $transaction->isShared() ? 'bg-success-subtle text-success-emphasis' : 'bg-primary-subtle text-primary' }} ms-1">
                                        {{ $transaction->isShared() ? 'Bersama' : 'Pribadi' }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold money {{ $transaction->isIncome() ? 'money-income' : 'money-expense' }}">
                                    {{ $transaction->isIncome() ? '+' : '-' }}{{ $transaction->formattedAmount() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <p class="text-secondary mb-3">Belum ada transaksi. Mulai catat arus kas pertama Anda.</p>
                            <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm">Catat Transaksi</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-12">
            <div class="card card-lg dashboard-card h-100" id="notifikasi">
                <div class="card-body d-flex flex-column gap-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-primary-subtle text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                                <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                            </svg>
                        </div>
                        <div>
                            <h5 class="mb-0">Notifikasi Arus Kas</h5>
                            <div class="notification-state text-secondary" id="pushStatus">
                                @if ($hasSubscriptions)
                                    <span class="text-success">Aktif di perangkat ini</span>
                                @else
                                    Belum aktif
                                @endif
                            </div>
                        </div>
                    </div>

                    <p class="text-secondary mb-0 small">
                        Dapatkan pemberitahuan langsung di layar HP setiap kali ada pemasukan atau pengeluaran baru,
                        bahkan saat aplikasi ditutup.
                    </p>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" id="enablePushBtn">
                            Aktifkan Notifikasi
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm d-none" id="disablePushBtn">
                            Matikan di perangkat ini
                        </button>
                    </div>

                    <div class="border-top pt-3 small text-secondary">
                        <div class="fw-semibold text-body mb-1">Pasang sebagai aplikasi</div>
                        Buka Dashboard di browser HP, lalu pilih tombol <span class="fw-semibold">Pasang Aplikasi</span>
                        pada banner yang muncul.
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.install-prompt-banner')
@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartElement = document.querySelector('#cashflowChart');

            if (!chartElement || typeof ApexCharts === 'undefined') {
                return;
            }

            const palette = window.themePalette();
            const chartData = @json($chart);
            const formatCurrency = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value);

            const chart = new ApexCharts(chartElement, {
                chart: {
                    type: 'bar',
                    height: 310,
                    toolbar: { show: false },
                    fontFamily: 'Public Sans, sans-serif'
                },
                series: [
                    { name: 'Pemasukan', data: chartData.income },
                    { name: 'Pengeluaran', data: chartData.expense }
                ],
                colors: ['#1a6be0', '#f5a623'],
                plotOptions: {
                    bar: { borderRadius: 5, columnWidth: '46%' }
                },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 0 },
                xaxis: {
                    categories: chartData.labels,
                    labels: { style: { colors: palette.labelColor } },
                    axisBorder: { show: false }
                },
                yaxis: {
                    labels: {
                        style: { colors: palette.labelColor },
                        formatter: (value) => value >= 1000000
                            ? (value / 1000000).toFixed(1).replace('.', ',') + ' jt'
                            : (value >= 1000 ? Math.round(value / 1000) + ' rb' : value)
                    }
                },
                grid: { borderColor: palette.gridColor },
                tooltip: {
                    theme: palette.tooltipTheme,
                    y: { formatter: formatCurrency }
                },
                legend: { show: false }
            });

            chart.render();

            window.addEventListener('themechange', function () {
                const palette = window.themePalette();

                chart.updateOptions({
                    xaxis: { labels: { style: { colors: palette.labelColor } } },
                    yaxis: { labels: { style: { colors: palette.labelColor } } },
                    grid: { borderColor: palette.gridColor },
                    tooltip: { theme: palette.tooltipTheme }
                });
            });
        });
    </script>
@endpush
