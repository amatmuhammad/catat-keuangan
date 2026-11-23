@extends('layouts.app')

@section('title', 'Kalender')

@section('content')
    @php
        $shortAmount = function (float $amount): string {
            if ($amount >= 1000000000) {
                return number_format($amount / 1000000000, 1, ',', '.').'M';
            }

            if ($amount >= 1000000) {
                return number_format($amount / 1000000, 1, ',', '.').'jt';
            }

            if ($amount >= 1000) {
                return number_format($amount / 1000, 0, ',', '.').'rb';
            }

            return number_format($amount, 0, ',', '.');
        };
    @endphp

    <div class="row mb-6">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
                <div>
                    <h1 class="page-title mb-1">Kalender Arus Kas</h1>
                    <p class="mb-0 text-secondary">
                        Arus kas bersama dan pribadi Anda di {{ $currentAccount->name }}, lengkap dengan nama penginput.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ $transactionsUrl }}" class="btn btn-white">Riwayat Bulan Ini</a>
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary">Catat Transaksi</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-6 mb-6">
        <div class="col">
            <div class="card card-lg">
                <div class="card-body d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <div class="small text-secondary mb-1">Pemasukan {{ $monthCursor->translatedFormat('F Y') }}</div>
                        <div class="fs-4 fw-bold money money-income">+Rp {{ number_format($monthIncome, 0, ',', '.') }}</div>
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
                        <div class="small text-secondary mb-1">Pengeluaran {{ $monthCursor->translatedFormat('F Y') }}</div>
                        <div class="fs-4 fw-bold money money-expense">-Rp {{ number_format($monthExpense, 0, ',', '.') }}</div>
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

        <div class="col">
            <div class="card card-lg">
                <div class="card-body d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <div class="small text-secondary mb-1">Arus Kas Bersih</div>
                        <div class="fs-4 fw-bold money {{ $monthNet >= 0 ? 'money-income' : 'money-expense' }}">
                            {{ $monthNet >= 0 ? '+' : '-' }}Rp {{ number_format(abs($monthNet), 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="icon-shape icon-lg rounded-circle bg-primary-subtle text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M3 17l6 -6l4 4l8 -8" />
                            <path d="M14 7l7 0l0 7" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-lg">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('calendar', ['month' => $prevMonth]) }}" class="btn btn-white btn-icon"
                        aria-label="Bulan sebelumnya">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M15 6l-6 6l6 6" />
                        </svg>
                    </a>
                    <h5 class="mb-0 px-1">{{ $monthCursor->translatedFormat('F Y') }}</h5>
                    <a href="{{ route('calendar', ['month' => $nextMonth]) }}" class="btn btn-white btn-icon"
                        aria-label="Bulan berikutnya">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 6l6 6l-6 6" />
                        </svg>
                    </a>
                    <a href="{{ route('calendar') }}" class="btn btn-white btn-sm ms-1">Bulan Ini</a>
                </div>

                <div class="d-flex align-items-center gap-3 small">
                    <span class="d-inline-flex align-items-center gap-1">
                        <span class="d-inline-block rounded-circle" style="width:8px;height:8px;background:#16a34a"></span>
                        Pemasukan
                    </span>
                    <span class="d-inline-flex align-items-center gap-1">
                        <span class="d-inline-block rounded-circle" style="width:8px;height:8px;background:#dc2626"></span>
                        Pengeluaran
                    </span>
                    <span class="text-secondary">Klik tanggal untuk melihat rincian</span>
                </div>
            </div>

            <div class="calendar-grid mb-2">
                @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $weekday)
                    <div class="calendar-weekday">{{ $weekday }}</div>
                @endforeach
            </div>

            <div class="calendar-grid">
                @foreach ($days as $day)
                    <button type="button"
                        class="calendar-day{{ $day['in_month'] ? '' : ' is-outside' }}{{ $day['is_today'] ? ' is-today' : '' }}"
                        data-bs-toggle="modal" data-bs-target="#calendarDayModal" data-date="{{ $day['date'] }}"
                        aria-label="Lihat transaksi tanggal {{ $day['date'] }}">
                        <span class="calendar-date">{{ $day['day'] }}</span>

                        @if ($day['income'] > 0)
                            <span class="calendar-amount calendar-income">+Rp {{ $shortAmount($day['income']) }}</span>
                        @endif

                        @if ($day['expense'] > 0)
                            <span class="calendar-amount calendar-expense">-Rp {{ $shortAmount($day['expense']) }}</span>
                        @endif

                        @if ($day['count'] > 0)
                            <span class="calendar-note text-truncate">{{ $day['details'][0]['description'] }}</span>
                            <span class="calendar-count">{{ $day['count'] }} transaksi</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal rincian tanggal -->
    <div class="modal fade" id="calendarDayModal" tabindex="-1" aria-labelledby="calendarDayTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="calendarDayTitle">Rincian Arus Kas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="calendarDayList"></div>
                </div>
                <div class="modal-footer">
                    <a href="{{ $transactionsUrl }}" id="calendarDayHistoryLink" class="btn btn-white btn-sm">
                        Lihat riwayat
                    </a>
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm">Catat Transaksi</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('calendarDayModal');

            if (!modal) {
                return;
            }

            const details = @json($dayDetails);
            const title = document.getElementById('calendarDayTitle');
            const list = document.getElementById('calendarDayList');
            const historyLink = document.getElementById('calendarDayHistoryLink');
            const historyBase = @json(route('transactions.index'));
            const numberFormat = new Intl.NumberFormat('id-ID');

            function escapeHtml(value) {
                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            modal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) return;

                const date = button.dataset.date;
                const items = details[date] || [];
                const dateObject = new Date(date + 'T00:00:00');

                title.textContent = dateObject.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });

                historyLink.href = historyBase + '?date_from=' + date + '&date_to=' + date;

                if (!items.length) {
                    list.innerHTML = '<div class="text-center py-4 text-secondary">' +
                        '<div class="mb-2">Belum ada transaksi pada tanggal ini.</div>' +
                        '<a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm">Catat sekarang</a>' +
                        '</div>';
                    return;
                }

                list.innerHTML = items.map(function (item) {
                    const isIncome = item.type === 'income';
                    const sign = isIncome ? '+' : '-';
                    const colorClass = isIncome ? 'money-income' : 'money-expense';
                    const badgeClass = isIncome
                        ? 'bg-success-subtle text-success-emphasis'
                        : 'bg-danger-subtle text-danger-emphasis';

                    return '<div class="transaction-row">' +
                        '<div class="transaction-icon ' + badgeClass + '">' +
                        '<span class="fw-bold">' + (isIncome ? '↑' : '↓') + '</span>' +
                        '</div>' +
                        '<div class="flex-grow-1 min-w-0">' +
                        '<div class="fw-semibold text-truncate">' + escapeHtml(item.description) + '</div>' +
                        '<div class="small text-secondary text-truncate">' +
                        escapeHtml(item.category || 'Tanpa kategori') + ' · ' + escapeHtml(item.wallet) +
                        ' · ' + escapeHtml(item.scope) + ' · oleh ' + escapeHtml(item.user) +
                        '</div>' +
                        '</div>' +
                        '<div class="text-end">' +
                        '<div class="fw-bold money ' + colorClass + '">' + sign + 'Rp ' +
                        numberFormat.format(item.amount) + '</div>' +
                        '<a href="' + item.edit_url + '" class="small">Edit</a>' +
                        '</div>' +
                        '</div>';
                }).join('');
            });
        });
    </script>
@endpush
