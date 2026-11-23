@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <div class="auth-shell">
        <div class="auth-visual">
            <a class="auth-brand" href="{{ url('/') }}">
                <img src="{{ asset('assets/images/brand/logo/logo-icon.svg') }}" alt="{{ config('app.name') }}" />
                <span>{{ config('app.name', 'Catat Keuangan') }}</span>
            </a>

            <div class="auth-visual-copy">
                <span class="auth-eyebrow">
                    <i class="ti ti-sparkles"></i>
                    Cashflow Tracker
                </span>
                <h2>Catat setiap rupiah,<br>kendalikan arus kas.</h2>
                <p>
                    Satu tempat untuk memantau saldo, mencatat pemasukan dan pengeluaran,
                    serta melihat arus kas harian dalam tampilan kalender.
                </p>
                <ul class="auth-feature-list">
                    <li>
                        <span class="auth-feature-icon"><i class="ti ti-check"></i></span>
                        Pencatatan pemasukan & pengeluaran dalam hitungan detik.
                    </li>
                    <li>
                        <span class="auth-feature-icon"><i class="ti ti-check"></i></span>
                        Notifikasi arus kas langsung ke perangkat Anda.
                    </li>
                    <li>
                        <span class="auth-feature-icon"><i class="ti ti-check"></i></span>
                        Bisa dipasang ke layar utama seperti aplikasi mobile.
                    </li>
                </ul>
            </div>

            <img class="auth-illustration" src="{{ asset('assets/images/login-illustration.svg') }}"
                alt="Ilustrasi aplikasi pencatatan keuangan dengan grafik arus kas dan dompet digital">

            <div class="auth-visual-footer">
                <i class="ti ti-lock"></i>
                <span>Data transaksi tersimpan aman dan terpisah untuk setiap akun.</span>
            </div>
        </div>

        <section class="auth-form-side">
            <div class="auth-form-wrap">
                <div class="auth-form-heading">
                    <span class="auth-eyebrow">Selamat datang kembali</span>
                    <h1>Masuk ke akun Anda</h1>
                    <p>Cukup login sekali. Sesi tetap tersimpan di perangkat ini sampai Anda logout.</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf

                    <div class="auth-field">
                        <label for="email" class="form-label">Email</label>
                        <div class="auth-input">
                            <i class="ti ti-mail auth-input-icon"></i>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror" required autofocus
                                autocomplete="email" placeholder="nama@email.com">
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="auth-field">
                        <label for="password" class="form-label">Password</label>
                        <div class="auth-input">
                            <i class="ti ti-lock auth-input-icon"></i>
                            <input type="password" id="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required
                                autocomplete="current-password" placeholder="Masukkan password">
                            <button type="button" class="auth-password-toggle" data-toggle-password="password"
                                aria-label="Tampilkan password">
                                <i class="ti ti-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Login sekali jalan: remember selalu aktif di server. --}}
                    <input type="hidden" name="remember" value="1">

                    <div class="auth-session-note mb-3">
                        <i class="ti ti-shield-check"></i>
                        <span>
                            <span class="fw-semibold text-body">Login sekali jalan.</span>
                            Anda tidak perlu memasukkan password lagi saat membuka aplikasi dari layar utama.
                        </span>
                    </div>

                    <button type="submit" class="btn btn-primary auth-submit w-100 d-inline-flex align-items-center justify-content-center gap-2">
                        <span>Masuk ke Dashboard</span>
                        <i class="ti ti-arrow-right"></i>
                    </button>
                </form>

                <div class="auth-divider"><span>atau</span></div>

                <a href="{{ route('register') }}" class="btn btn-outline-secondary auth-register-link w-100 d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="ti ti-user-plus"></i>
                    <span>Buat akun baru</span>
                </a>

                <p class="text-center text-secondary small mt-4 mb-0">
                    Dengan masuk, Anda dapat langsung mencatat arus kas pertama Anda.
                </p>
            </div>
        </section>
    </div>

@push('scripts')
    <script>
        document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = document.getElementById(button.dataset.togglePassword);
                if (!input) return;

                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                button.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
                button.querySelector('i').className = show ? 'ti ti-eye-off' : 'ti ti-eye';
            });
        });
    </script>
@endpush