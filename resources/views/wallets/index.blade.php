@extends('layouts.app')

@section('title', 'Dompet')

@section('content')
    <div class="d-none" data-live-summary data-account-id="{{ $currentAccount->id }}"
        data-live-url="{{ route('live.summary') }}"></div>

    <div class="row mb-6">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
                <div>
                    <h1 class="page-title mb-1">Dompet</h1>
                    <p class="mb-0 text-secondary">
                        Kelola saldo bersama akun dan saldo pribadi Anda.
                        <span class="badge bg-success-subtle text-success-emphasis ms-1" data-live-status
                            title="Saldo diperbarui otomatis setiap 10 detik">Sinkron otomatis</span>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('categories.index') }}" class="btn btn-white">Kelola Kategori</a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#walletCreateModal">
                        Tambah Dompet
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('partials.account-scope-notice')

    <div class="row g-6">
        <div class="col-xl-8 col-12">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="mb-0">Kas Bersama & Pribadi</h5>
                            <p class="small text-secondary mb-0">Dompet pribadi hanya terlihat oleh pemiliknya.</p>
                        </div>
                        <span class="small text-secondary">{{ $wallets->count() }} dompet</span>
                    </div>

                    @forelse ($wallets as $wallet)
                        @php
                            $isShared = $wallet->isShared();
                            $cannotDelete = $wallet->transactions_count > 0
                                || ($isShared ? $sharedCount <= 1 : $personalCount <= 1);
                        @endphp
                        <div class="transaction-row">
                            <div class="transaction-icon {{ $isShared ? 'bg-success-subtle text-success-emphasis' : 'bg-primary-subtle text-primary' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                                    <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" />
                                </svg>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold text-truncate">
                                    {{ $wallet->name }}
                                    <span class="badge {{ $isShared ? 'bg-success-subtle text-success-emphasis' : 'bg-primary-subtle text-primary' }} ms-1">
                                        {{ $isShared ? 'Bersama' : 'Pribadi' }}
                                    </span>
                                </div>
                                <div class="small text-secondary">
                                    <span data-live-wallet-count="{{ $wallet->id }}">{{ $wallet->transactions_count }}</span>
                                    transaksi
                                    · {{ $isShared ? 'terlihat semua anggota' : 'hanya terlihat oleh Anda' }}
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold money" data-live-wallet-balance="{{ $wallet->id }}">{{ $wallet->formattedBalance() }}</div>
                                <div class="transaction-actions mt-1">
                                    <button type="button" class="btn btn-white" data-bs-toggle="modal"
                                        data-bs-target="#walletEditModal" data-name="{{ $wallet->name }}"
                                        data-action="{{ route('wallets.update', $wallet) }}">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('wallets.destroy', $wallet) }}"
                                        onsubmit="return confirm('Hapus dompet ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" @disabled($cannotDelete)
                                            title="{{ $wallet->transactions_count > 0 ? 'Dompet masih memiliki transaksi' : 'Minimal satu dompet jenis ini harus ada' }}">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <p class="text-secondary mb-3">Belum ada dompet. Tambahkan dompet pertama Anda.</p>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#walletCreateModal">Tambah Dompet</button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-12">
            <div class="card card-lg balance-card h-100">
                <div class="card-body d-flex flex-column justify-content-between gap-5">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-white bg-opacity-25 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1" />
                                <path d="M12 7v10" />
                            </svg>
                        </div>
                        <span class="fw-semibold">Ringkasan Saldo</span>
                    </div>

                    <div>
                        <div class="text-white-50 small">Saldo Bersama</div>
                        <div class="fs-2 fw-bold money" data-live-field="shared_balance" data-live-prefix="Rp ">Rp {{ number_format($sharedBalance, 0, ',', '.') }}</div>
                        <div class="text-white-50 small mt-1">
                            <span data-live-field="shared_wallet_count">{{ $sharedCount }}</span> dompet bersama
                        </div>
                    </div>

                    <div class="border-top border-white border-opacity-25 pt-3">
                        <div class="text-white-50 small">Saldo Pribadi Anda</div>
                        <div class="fs-3 fw-bold money" data-live-field="personal_balance" data-live-prefix="Rp ">Rp {{ number_format($personalBalance, 0, ',', '.') }}</div>
                        <div class="text-white-50 small mt-1">
                            <span data-live-field="personal_wallet_count">{{ $personalCount }}</span> dompet pribadi · hanya Anda yang bisa melihat
                        </div>
                    </div>

                    <a href="{{ route('transactions.create') }}" class="btn btn-light text-primary fw-semibold">
                        Catat Transaksi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal tambah dompet -->
    <div class="modal fade" id="walletCreateModal" tabindex="-1" aria-labelledby="walletCreateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('wallets.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="walletCreateModalLabel">Tambah Dompet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span class="form-label d-block">Jenis dompet</span>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="is_shared" id="wallet_scope_shared"
                            value="1" @checked(old('is_shared', '1') === '1')>
                        <label class="form-check-label" for="wallet_scope_shared">
                            <span class="fw-semibold">Kas Bersama</span>
                            <span class="d-block small text-secondary">Terlihat dan bisa dipakai semua anggota akun.</span>
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="is_shared" id="wallet_scope_personal"
                            value="0" @checked(old('is_shared') === '0')>
                        <label class="form-check-label" for="wallet_scope_personal">
                            <span class="fw-semibold">Saldo Pribadi</span>
                            <span class="d-block small text-secondary">Hanya terlihat dan bisa dipakai oleh Anda.</span>
                        </label>
                    </div>

                    <label for="wallet_name" class="form-label">Nama dompet</label>
                    <input type="text" id="wallet_name" name="name" class="form-control" maxlength="50" required
                        value="{{ old('name') }}" placeholder="Misal: Kas Tunai, BCA, OVO">
                    <div class="form-text">Nama harus unik sesuai jenis dompet.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Dompet</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal edit dompet -->
    <div class="modal fade" id="walletEditModal" tabindex="-1" aria-labelledby="walletEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="walletEditForm" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="walletEditModalLabel">Edit Dompet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="wallet_edit_name" class="form-label">Nama dompet</label>
                    <input type="text" id="wallet_edit_name" name="name" class="form-control" maxlength="50" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const walletEditModal = document.getElementById('walletEditModal');
            const walletEditForm = document.getElementById('walletEditForm');
            const walletEditName = document.getElementById('wallet_edit_name');

            if (!walletEditModal) {
                return;
            }

            walletEditModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) return;

                walletEditForm.action = button.dataset.action;
                walletEditName.value = button.dataset.name;
            });
        });
    </script>
@endpush
