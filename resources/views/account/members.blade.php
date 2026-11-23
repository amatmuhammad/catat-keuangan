@extends('layouts.app')

@section('title', 'Anggota Akun')

@section('content')
    <div class="row mb-6">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
                <div>
                    <h1 class="page-title mb-1">Anggota Akun</h1>
                    <p class="mb-0 text-secondary">
                        Kelola siapa saja yang bisa mencatat arus kas di <span class="fw-semibold">{{ $currentAccount->name }}</span>.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('account.activity') }}" class="btn btn-white">Log Aktivitas</a>
                    <span class="btn btn-primary disabled">
                        {{ $members->count() }} / {{ $maxMembers }} anggota
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-xl-8 col-12">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                        <div>
                            <h5 class="mb-0">Daftar Anggota</h5>
                            <p class="small text-secondary mb-0">Setiap transaksi tercatat atas nama pengguna yang menginput.</p>
                        </div>
                        <span class="badge {{ $members->count() >= $maxMembers ? 'bg-danger-subtle text-danger-emphasis' : 'bg-success-subtle text-success-emphasis' }}">
                            {{ $members->count() }} dari {{ $maxMembers }} slot terpakai
                        </span>
                    </div>

                    @foreach ($members as $member)
                        <div class="transaction-row">
                            <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary avatar-initial flex-shrink-0">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </span>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold text-truncate">
                                    {{ $member->name }}
                                    @if ((int) $member->id === (int) auth()->id())
                                        <span class="badge bg-light text-secondary border ms-1">Anda</span>
                                    @endif
                                </div>
                                <div class="small text-secondary text-truncate">{{ $member->email }}</div>
                                <div class="small text-secondary mt-1">
                                    {{ $member->account_transactions_count }} transaksi dicatat
                                    @if ($member->pivot?->joined_at)
                                        · bergabung {{ \Illuminate\Support\Carbon::parse($member->pivot->joined_at)->translatedFormat('d M Y') }}
                                    @endif
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <span class="badge {{ $member->pivot?->role === 'owner' ? 'bg-primary-subtle text-primary' : 'bg-light text-secondary border' }}">
                                    {{ $member->pivot?->role === 'owner' ? 'Pemilik' : 'Anggota' }}
                                </span>
                                @if ($isOwner && $member->pivot?->role !== 'owner')
                                    <form method="POST" action="{{ route('account.members.destroy', $member) }}" class="mt-2"
                                        onsubmit="return confirm('Keluarkan {{ $member->name }} dari akun ini? Data transaksi yang pernah dicatat tetap tersimpan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Keluarkan</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-12">
            <div class="card card-lg mb-6">
                <div class="card-body">
                    <h5 class="mb-1">Tambah Anggota</h5>
                    <p class="small text-secondary">
                        Masukkan email pengguna yang sudah terdaftar. Satu akun maksimal {{ $maxMembers }} pengguna.
                    </p>

                    @if ($isOwner)
                        <form method="POST" action="{{ route('account.members.store') }}">
                            @csrf
                            <label for="member_email" class="form-label">Email pengguna</label>
                            <input type="email" id="member_email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror" placeholder="nama@email.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <button type="submit" class="btn btn-primary w-100 mt-3"
                                @disabled($members->count() >= $maxMembers)>
                                Tambahkan ke Akun
                            </button>

                            @if ($members->count() >= $maxMembers)
                                <div class="form-text text-danger mt-2">
                                    Akun sudah penuh. Keluarkan salah satu anggota untuk menambah pengguna lain.
                                </div>
                            @endif
                        </form>
                    @else
                        <div class="alert alert-info mb-0 small">
                            Hanya pemilik akun yang dapat menambah atau mengeluarkan anggota.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card card-lg">
                <div class="card-body">
                    <h5 class="mb-2">Cara kerja akun bersama</h5>
                    <ul class="small text-secondary mb-0 ps-3 d-grid gap-2">
                        <li>Semua anggota melihat saldo dan transaksi dari dompet bersama.</li>
                        <li>Setiap anggota punya saldo pribadi yang hanya terlihat olehnya.</li>
                        <li>Nama penginput tampil di riwayat transaksi dan rincian kalender.</li>
                        <li>Aktivitas bersama terlihat semua anggota; aktivitas pribadi hanya untuk pemiliknya.</li>
                        <li>Notifikasi transaksi bersama dikirim ke seluruh anggota.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
