@extends('layouts.guest')

@section('title', 'Daftar')

@section('content')
    <div class="auth-page">
        <div class="auth-card">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-5">
                    <div class="auth-logo mb-3" style="width: 48px; height: 48px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #2563eb, #4338ca); color: #fff; font-weight: 800; font-size: 1.15rem; letter-spacing: -0.02em;">
                        CK
                    </div>
                    <h1 class="h3 mb-2">Buat akun baru</h1>
                    <p class="text-secondary mb-0">
                        Dompet dan kategori standar akan otomatis disiapkan untuk Anda.
                    </p>
                </div>

                <form method="POST" action="{{ route('register') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror" required autofocus
                            autocomplete="name" placeholder="Nama lengkap"
                            style="border-radius: 8px;">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" required
                            autocomplete="email" placeholder="nama@email.com"
                            style="border-radius: 8px;">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" required
                            autocomplete="new-password" placeholder="Minimal 8 karakter"
                            style="border-radius: 8px;">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4" style="border-radius: 8px;">
                        <label for="password_confirmation" class="form-label">Ulangi password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="form-control" required autocomplete="new-password" placeholder="Ulangi password"
                            style="border-radius: 8px;">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2" style="border-radius: 8px; padding: 0.75rem;">
                        Daftar & Tetap Terhubung
                    </button>
                </form>

                <p class="text-center text-secondary small mt-4 mb-0" style="font-size: 0.82rem;">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="fw-semibold">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
@endsection