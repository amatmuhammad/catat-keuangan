@php
    $user = auth()->user();
    $is = fn (string ...$patterns) => request()->routeIs(...$patterns) ? ' active' : '';
@endphp

<ul class="navbar-nav flex-column">
    <li class="nav-item">
        <div class="nav-heading">Menu Utama</div>
        <hr class="mx-5 nav-line mb-1" />
    </li>

    <li class="nav-item">
        <a class="nav-link{{ $is('dashboard') }}" href="{{ route('dashboard') }}">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                    <path d="M10 12h4v4h-4z" />
                </svg>
            </span>
            <span class="text">Dashboard</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link{{ $is('transactions.*') }}" href="{{ route('transactions.index') }}">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                    <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                    <path d="M10 12l4 0" />
                    <path d="M10 16l4 0" />
                </svg>
            </span>
            <span class="text">Transaksi</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link{{ $is('calendar') }}" href="{{ route('calendar') }}">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                    <path d="M16 3v4" />
                    <path d="M8 3v4" />
                    <path d="M4 11h16" />
                    <path d="M8 15h2v2h-2z" fill="currentColor" />
                </svg>
            </span>
            <span class="text">Kalender</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link{{ $is('wallets.*') }}" href="{{ route('wallets.index') }}">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                    <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" />
                </svg>
            </span>
            <span class="text">Dompet</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link{{ $is('categories.*') }}" href="{{ route('categories.index') }}">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M7.5 7.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" fill="currentColor" />
                    <path d="M3 6v5.172a2 2 0 0 0 .586 1.414l7.71 7.71a2.41 2.41 0 0 0 3.408 0l5.592 -5.592a2.41 2.41 0 0 0 0 -3.408l-7.71 -7.71a2 2 0 0 0 -1.414 -.586h-5.172a3 3 0 0 0 -3 3z" />
                </svg>
            </span>
            <span class="text">Kategori</span>
        </a>
    </li>

    <li class="nav-item">
        <div class="nav-heading">Akun & Kolaborasi</div>
        <hr class="mx-5 nav-line mb-1" />
    </li>

    @if (isset($currentAccount))
        <li class="nav-item px-5 pb-2">
            <div class="d-flex align-items-center gap-2 rounded-3 border bg-white p-2">
                <span class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary avatar-initial flex-shrink-0">
                    <i class="ti ti-users"></i>
                </span>
                <div class="min-w-0">
                    <div class="small fw-semibold text-truncate">{{ $currentAccount->name }}</div>
                    <div class="text-secondary" style="font-size: .72rem">
                        {{ $currentAccount->memberCount() }} dari {{ \App\Models\Account::MAX_MEMBERS }} anggota
                    </div>
                </div>
            </div>
        </li>
    @endif

    <li class="nav-item">
        <a class="nav-link{{ $is('account.members') }}" href="{{ route('account.members') }}">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                </svg>
            </span>
            <span class="text">Anggota Akun</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link{{ $is('account.activity') }}" href="{{ route('account.activity') }}">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 8l0 4l2 2" />
                    <path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" />
                </svg>
            </span>
            <span class="text">Log Aktivitas</span>
        </a>
    </li>

    <li class="nav-item">
        <div class="nav-heading">Perangkat</div>
        <hr class="mx-5 nav-line mb-1" />
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard') }}#notifikasi">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                    <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                </svg>
            </span>
            <span class="text">Notifikasi Arus Kas</span>
        </a>
    </li>

    <li>
        <div class="text-center py-5 upgrade-ui">
            <div>
                <span class="avatar avatar-md rounded-circle bg-primary text-white avatar-initial">
                    {{ strtoupper(substr($user->name ?? 'G', 0, 1)) }}
                </span>
                <div class="my-3">
                    <h5 class="mb-1 fs-6">{{ $user->name ?? 'Guest' }}</h5>
                    <span class="text-secondary small">{{ $user->email ?? '' }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </li>
</ul>
