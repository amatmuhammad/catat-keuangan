@php
    $user = auth()->user();
    $userName = $user->name ?? 'Guest';
    $userEmail = $user->email ?? '';
    $initial = strtoupper(substr($userName, 0, 1));
@endphp

<!-- navbar -->
<div class="navbar-glass navbar navbar-expand-lg px-0 px-lg-4">
    <div class="container-fluid px-lg-0">
        <div class="d-flex align-items-center gap-4">
            <!-- Toggle sidebar di mobile -->
            <div class="d-block d-lg-none">
                <a class="text-inherit" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button"
                    aria-controls="offcanvasExample" aria-label="Buka menu">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 6l16 0" />
                        <path d="M4 12l16 0" />
                        <path d="M4 18l16 0" />
                    </svg>
                </a>
            </div>

            <!-- Collapse / expand sidebar di desktop -->
            <div class="d-none d-lg-block">
                <a class="sidebar-toggle d-flex texttooltip p-3" href="javascript:void(0)" data-template="collapseMessage">
                    <span class="collapse-mini">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-bar-left text-secondary">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 12l10 0" />
                            <path d="M4 12l4 4" />
                            <path d="M4 12l4 -4" />
                            <path d="M20 4l0 16" />
                        </svg>
                    </span>
                    <span class="collapse-expanded">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-bar-right text-secondary">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M20 12l-10 0" />
                            <path d="M20 12l-4 4" />
                            <path d="M20 12l-4 -4" />
                            <path d="M4 4l0 16" />
                        </svg>
                    </span>
                </a>
            </div>
        </div>

        <!-- Navbar nav -->
        <ul class="list-unstyled d-flex align-items-center mb-0 gap-2">
            <li>
                <a class="btn btn-white btn-sm d-none d-sm-inline-flex align-items-center gap-2"
                    href="{{ route('transactions.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    <span>Catat Transaksi</span>
                </a>
            </li>

            <!-- Pengalih akun bersama -->
            @if (isset($currentAccount, $userAccounts))
                @php
                    $activeAccount = $userAccounts->firstWhere('id', $currentAccount->id);
                    $activeMemberCount = $activeAccount?->members_count ?? $currentAccount->memberCount();
                @endphp
                <li class="dropdown">
                    <button class="btn btn-white btn-sm d-flex align-items-center gap-2" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false" aria-label="Pilih akun">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                            <path d="M3 10h18" />
                            <path d="M7 15h4" />
                        </svg>
                        <span class="d-none d-md-inline text-truncate" style="max-width: 140px">{{ $currentAccount->name }}</span>
                        <span class="badge text-bg-light border">{{ $activeMemberCount }}/5</span>
                        <i class="ti ti-chevron-down small"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow p-2" style="min-width: 290px">
                        <li class="px-3 pt-2 pb-1 small text-secondary">
                            Akun aktif · {{ $activeMemberCount }} dari 5 anggota
                        </li>
                        @foreach ($userAccounts as $accountOption)
                            <li>
                                <form method="POST" action="{{ route('account.switch') }}">
                                    @csrf
                                    <input type="hidden" name="account_id" value="{{ $accountOption->id }}">
                                    <button type="submit"
                                        class="dropdown-item d-flex align-items-center justify-content-between gap-2 py-2 {{ (int) $currentAccount->id === (int) $accountOption->id ? 'active' : '' }}">
                                        <span class="text-truncate">{{ $accountOption->name }}</span>
                                        <span class="badge text-bg-light border flex-shrink-0">{{ $accountOption->members_count }} anggota</span>
                                    </button>
                                </form>
                            </li>
                        @endforeach
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a href="{{ route('account.members') }}" class="dropdown-item d-flex align-items-center gap-2 py-2">
                                <i class="ti ti-users"></i> Kelola Anggota
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('account.activity') }}" class="dropdown-item d-flex align-items-center gap-2 py-2">
                                <i class="ti ti-history"></i> Log Aktivitas
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            <!-- Light / dark mode -->
            <li>
                <div class="dropdown">
                    <button class="btn btn-ghost btn-icon rounded-circle d-flex align-items-center" type="button"
                        aria-expanded="false" data-bs-toggle="dropdown" aria-label="Toggle theme (auto)">
                        <i class="ti theme-icon-active lh-1 fs-5"><i class="ti theme-icon ti-sun"></i></i>
                        <span class="visually-hidden bs-theme-text">Toggle theme</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center active"
                                data-bs-theme-value="light" aria-pressed="true">
                                <i class="ti theme-icon ti-sun"></i>
                                <span class="ms-2">Light</span>
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center"
                                data-bs-theme-value="dark" aria-pressed="false">
                                <i class="ti theme-icon ti-moon-stars"></i>
                                <span class="ms-2">Dark</span>
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center"
                                data-bs-theme-value="auto" aria-pressed="false">
                                <i class="ti theme-icon ti-circle-half-2"></i>
                                <span class="ms-2">Auto</span>
                            </button>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Profil -->
            <li class="ms-2 dropdown">
                <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu profil">
                    <span class="avatar avatar-sm rounded-circle bg-primary text-white avatar-initial">{{ $initial }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-md p-0">
                    <div>
                        <div class="d-flex gap-3 align-items-center border-dashed border-bottom px-4 py-4">
                            <span class="avatar avatar-md rounded-circle bg-primary text-white avatar-initial">{{ $initial }}</span>
                            <div>
                                <h4 class="mb-0 fs-5">{{ $userName }}</h4>
                                <p class="mb-0 text-secondary small">{{ $userEmail }}</p>
                            </div>
                        </div>
                        <div class="p-3 d-flex flex-column gap-1">
                            <a href="{{ route('calendar') }}" class="dropdown-item d-flex align-items-center gap-2">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                        <path d="M16 3v4" />
                                        <path d="M8 3v4" />
                                        <path d="M4 11h16" />
                                    </svg>
                                </span>
                                <span>Kalender Arus Kas</span>
                            </a>
                            <a href="{{ route('wallets.index') }}" class="dropdown-item d-flex align-items-center gap-2">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                                        <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" />
                                    </svg>
                                </span>
                                <span>Dompet</span>
                            </a>
                            <a href="{{ route('categories.index') }}" class="dropdown-item d-flex align-items-center gap-2">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 6v5.172a2 2 0 0 0 .586 1.414l7.71 7.71a2.41 2.41 0 0 0 3.408 0l5.592 -5.592a2.41 2.41 0 0 0 0 -3.408l-7.71 -7.71a2 2 0 0 0 -1.414 -.586h-5.172a3 3 0 0 0 -3 3z" />
                                        <path d="M7.5 7.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" fill="currentColor" />
                                    </svg>
                                </span>
                                <span>Kategori</span>
                            </a>
                            <a href="{{ route('account.members') }}" class="dropdown-item d-flex align-items-center gap-2">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                        <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                    </svg>
                                </span>
                                <span>Anggota Akun</span>
                            </a>
                            <a href="{{ route('account.activity') }}" class="dropdown-item d-flex align-items-center gap-2">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 8l0 4l2 2" />
                                        <path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" />
                                    </svg>
                                </span>
                                <span>Log Aktivitas</span>
                            </a>
                            <a href="{{ route('dashboard') }}#notifikasi" class="dropdown-item d-flex align-items-center gap-2">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                                        <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                                    </svg>
                                </span>
                                <span>Notifikasi Arus Kas</span>
                            </a>
                        </div>
                        <div class="border-dashed border-top mb-4 pt-4 px-6">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-secondary d-flex align-items-center gap-2 border-0 bg-transparent p-0">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M9 8v-2a2 2 0 0 1 2 -2h7a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2" />
                                            <path d="M3 12h13l-3 -3" />
                                            <path d="M13 15l3 -3" />
                                        </svg>
                                    </span>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
