@extends('layouts.guest')

@section('title', 'Daftar')

@push('styles')
    @include('auth.partials.styles')
@endpush

@section('content')
    @php
        $visual = [
            'icon' => 'ti-rocket',
            'eyebrow' => 'Mulai dengan mudah',
            'headline' => 'Atur keuangan,<br><span class="auth-accent-text">tanpa ribet.</span>',
            'lead' => 'Kelola pemasukan, pengeluaran, dompet, dan tujuan keuangan dalam satu tempat yang rapi.',
            'points' => [
                ['icon' => 'ti-wallet', 'text' => 'Dompet & kategori standar langsung tersedia.'],
                ['icon' => 'ti-calendar-month', 'text' => 'Pantau transaksi lewat kalender keuangan.'],
                ['icon' => 'ti-shield-lock', 'text' => 'Data akun tetap aman dan terpisah.'],
            ],
            'footnote' => 'Pendaftaran cepat dan aman untuk mulai mencatat keuangan.',
        ];
    @endphp

    <div class="auth-layout">
        @include('auth.partials.visual')

        <main class="auth-main">
            <button type="button" class="auth-theme-toggle" data-auth-theme-toggle aria-label="Aktifkan mode gelap"
                aria-pressed="false">
                <i class="ti ti-moon-stars"></i>
            </button>

            <div class="auth-panel">
                <a class="auth-mobile-brand" href="{{ url('/') }}" aria-label="Saku Kas">
                    <img src="{{ asset('assets/images/favicon .png') }}" alt="Saku Kas" width="640"
                        height="531">
                </a>

                <div class="auth-heading">
                    <span class="auth-eyebrow">Mulai gratis</span>
                    <h1>Buat akun baru</h1>
                    <p>Siapkan dompet dan kategori standar dalam satu langkah.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" data-auth-form novalidate>
                    @csrf

                    <div class="auth-field">
                        <label for="name" class="form-label">Nama</label>
                        <div class="auth-control">
                            <i class="ti ti-user auth-control-icon"></i>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror" required autofocus
                                autocomplete="name" placeholder="Nama lengkap"
                                @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                        </div>
                        @error('name')
                            <div class="auth-error" id="name-error">
                                <i class="ti ti-alert-circle"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="auth-field">
                        <label for="email" class="form-label">Email</label>
                        <div class="auth-control">
                            <i class="ti ti-mail auth-control-icon"></i>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror" required
                                autocomplete="email" placeholder="nama@email.com"
                                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                        </div>
                        @error('email')
                            <div class="auth-error" id="email-error">
                                <i class="ti ti-alert-circle"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="auth-field">
                        <label for="password" class="form-label">Password</label>
                        <div class="auth-control">
                            <i class="ti ti-lock auth-control-icon"></i>
                            <input type="password" id="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required
                                autocomplete="new-password" placeholder="Minimal 8 karakter"
                                @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                            <button type="button" class="auth-password-toggle" data-toggle-password="password"
                                aria-label="Tampilkan password" aria-pressed="false">
                                <i class="ti ti-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="auth-error" id="password-error">
                                <i class="ti ti-alert-circle"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                        <div class="auth-strength" data-password-strength>
                            <div class="auth-strength-track">
                                <span class="auth-strength-bar" data-strength-bar></span>
                            </div>
                            <small class="auth-strength-text" data-strength-text>
                                Minimal 8 karakter, kombinasikan huruf dan angka.
                            </small>
                        </div>
                    </div>

                    <div class="auth-field">
                        <label for="password_confirmation" class="form-label">Ulangi password</label>
                        <div class="auth-control">
                            <i class="ti ti-lock-check auth-control-icon"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control" required autocomplete="new-password" placeholder="Ulangi password">
                            <button type="button" class="auth-password-toggle" data-toggle-password="password_confirmation"
                                aria-label="Tampilkan pengulangan password" aria-pressed="false">
                                <i class="ti ti-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="auth-note">
                        <i class="ti ti-shield-check"></i>
                        <span>
                            <strong class="text-body">Siap digunakan.</strong>
                            Setelah daftar, Anda langsung masuk dan dompet pribadi sudah disiapkan.
                        </span>
                    </div>

                    <button type="submit" data-submit-button data-loading-text="Menyiapkan akun..."
                        class="btn btn-primary auth-submit w-100 d-inline-flex align-items-center justify-content-center gap-2">
                        <span>Buat Akun Sekarang</span>
                        <i class="ti ti-arrow-right"></i>
                    </button>
                </form>

                <div class="auth-divider"><span>atau</span></div>

                <a href="{{ route('login') }}"
                    class="btn auth-alt-btn w-100 d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="ti ti-login"></i>
                    <span>Masuk ke akun yang sudah ada</span>
                </a>
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    @include('auth.partials.scripts')
@endpush
