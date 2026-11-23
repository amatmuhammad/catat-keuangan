@php
    $otherSharedAccounts = collect($userAccounts ?? [])
        ->filter(fn ($accountOption) => (int) $accountOption->id !== (int) $currentAccount->id
            && $accountOption->members_count > 1);
@endphp

@if ($currentAccount->memberCount() <= 1 && $otherSharedAccounts->isNotEmpty())
    <div class="alert alert-warning border d-flex gap-3 align-items-start mb-6" role="alert">
        <i class="ti ti-users fs-4 flex-shrink-0"></i>
        <div class="flex-grow-1">
            <div class="fw-semibold">Anda sedang melihat akun pribadi {{ $currentAccount->name }}</div>
            <p class="mb-3 small">
                Saldo bersama hanya sama jika semua anggota membuka akun bersama yang sama.
                Pilih akun bersama berikut untuk melihat saldo yang dipakai bersama.
            </p>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($otherSharedAccounts as $accountOption)
                    <form method="POST" action="{{ route('account.switch') }}">
                        @csrf
                        <input type="hidden" name="account_id" value="{{ $accountOption->id }}">
                        <button type="submit" class="btn btn-primary btn-sm">
                            Buka {{ $accountOption->name }}
                            <span class="badge bg-white bg-opacity-25 ms-1">{{ $accountOption->members_count }} anggota</span>
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    </div>
@endif
