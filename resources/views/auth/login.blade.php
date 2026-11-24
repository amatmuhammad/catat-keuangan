@extends('layouts.guest')

@section('title', 'Masuk')

@push('styles')
    @include('auth.partials.styles')
@endpush

@section('content')
    @php
        $visual = [
            'icon' => 'ti-sparkles',
            'eyebrow' => 'Cashflow Tracker',
            'headline' => 'Catat setiap rupiah,<br><span class="auth-accent-text">kendalikan arus kas.</span>',
            'lead' => 'Pantau saldo, catat pemasukan dan pengeluaran, lalu lihat arus kas harian dalam satu tampilan.',
            'points' => [
                ['icon' => 'ti-pencil', 'text' => 'Catat pemasukan & pengeluaran dalam hitungan detik.'],
                ['icon' => 'ti-bell-ringing', 'text' => 'Notifikasi arus kas langsung ke perangkat.'],
                ['icon' => 'ti-device-mobile', 'text' => 'Pasang ke layar utama seperti aplikasi.'],
            ],
            'footnote' => 'Data transaksi tersimpan aman dan terpisah untuk setiap akun.',
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
                    <span class="auth-eyebrow">Selamat datang kembali</span>
                    <h1>Masuk ke akun Anda</h1>
                    <p>Cukup login sekali. Sesi tetap tersimpan di perangkat ini sampai Anda logout.</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success auth-alert" role="alert">
                        <i class="ti ti-circle-check"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" data-auth-form novalidate>
                    @csrf

                    <div class="auth-field">
                        <label for="email" class="form-label">Email</label>
                        <div class="auth-control">
                            <i class="ti ti-mail auth-control-icon"></i>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror" required autofocus
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
                                autocomplete="current-password" placeholder="Masukkan password"
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
                    </div>

                    {{-- Login sekali jalan: remember selalu aktif di server. --}}
                    <input type="hidden" name="remember" value="1">

                    <div class="auth-note">
                        <i class="ti ti-shield-check"></i>
                        <span>
                            <strong class="text-body">Login sekali jalan.</strong>
                            Anda tidak perlu memasukkan password lagi saat membuka aplikasi dari layar utama.
                        </span>
                    </div>

                    <button type="submit" data-submit-button data-loading-text="Memproses..."
                        class="btn btn-primary auth-submit w-100 d-inline-flex align-items-center justify-content-center gap-2">
                        <span>Masuk ke Dashboard</span>
                        <i class="ti ti-arrow-right"></i>
                    </button>
                </form>

                <div class="auth-divider"><span>atau</span></div>

                <a href="{{ route('register') }}"
                    class="btn auth-alt-btn w-100 d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="ti ti-user-plus"></i>
                    <span>Buat akun baru</span>
                </a>

                <p class="auth-footnote">
                    Dengan masuk, Anda dapat langsung mencatat arus kas pertama Anda.
                </p>
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    @include('auth.partials.scripts')
@endpush
