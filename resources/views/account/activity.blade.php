@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
    <div class="row mb-6">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
                <div>
                    <h1 class="page-title mb-1">Log Aktivitas</h1>
                    <p class="mb-0 text-secondary">
                        Aktivitas bersama dan aktivitas pribadi Anda di <span class="fw-semibold">{{ $currentAccount->name }}</span>.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('account.members') }}" class="btn btn-white">Anggota Akun</a>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-6 mb-6">
        <div class="col">
            <div class="card card-lg">
                <div class="card-body">
                    <div class="small text-secondary mb-1">Total aktivitas</div>
                    <div class="fs-3 fw-bold">{{ number_format($totalLogs, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg">
                <div class="card-body">
                    <div class="small text-secondary mb-1">Aktivitas hari ini</div>
                    <div class="fs-3 fw-bold">{{ number_format($todayLogs, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg">
                <div class="card-body">
                    <div class="small text-secondary mb-1">Anggota akun</div>
                    <div class="fs-3 fw-bold">{{ $members->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-lg mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('account.activity') }}" class="filter-grid align-items-end">
                <div>
                    <label for="user_id" class="form-label">Anggota</label>
                    <select id="user_id" name="user_id" class="form-select">
                        <option value="">Semua anggota</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}" @selected((string) ($filters['user_id'] ?? '') === (string) $member->id)>
                                {{ $member->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="scope" class="form-label">Jenis data</label>
                    <select id="scope" name="scope" class="form-select">
                        <option value="">Bersama & pribadi</option>
                        <option value="shared" @selected(($filters['scope'] ?? '') === 'shared')>Bersama</option>
                        <option value="personal" @selected(($filters['scope'] ?? '') === 'personal')>Pribadi saya</option>
                    </select>
                </div>
                <div>
                    <label for="action" class="form-label">Jenis aktivitas</label>
                    <select id="action" name="action" class="form-select">
                        <option value="">Semua aktivitas</option>
                        @foreach ($availableActions as $action)
                            <option value="{{ $action }}" @selected(($filters['action'] ?? '') === $action)>
                                {{ \App\Models\ActivityLog::labelFor($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date_from" class="form-label">Dari tanggal</label>
                    <input type="date" id="date_from" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                </div>
                <div>
                    <label for="date_to" class="form-label">Sampai tanggal</label>
                    <input type="date" id="date_to" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Terapkan</button>
                    <a href="{{ route('account.activity') }}" class="btn btn-white">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-lg">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Aktivitas Terbaru</h5>
                <span class="small text-secondary">{{ $logs->total() }} catatan</span>
            </div>

            @forelse ($logs as $log)
                <div class="transaction-row align-items-start">
                    <div class="transaction-icon bg-{{ $log->tone() }}-subtle text-{{ $log->tone() }}-emphasis flex-shrink-0">
                        <i class="ti {{ $log->icon() }} fs-5"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="fw-semibold">{{ $log->user?->name ?? 'Sistem' }}</span>
                            <span class="badge bg-{{ $log->tone() }}-subtle text-{{ $log->tone() }}-emphasis">
                                {{ $log->actionLabel() }}
                            </span>
                            <span class="badge {{ $log->is_shared ? 'bg-success-subtle text-success-emphasis' : 'bg-primary-subtle text-primary' }}">
                                {{ $log->scopeLabel() }}
                            </span>
                        </div>
                        <div class="mt-1">{{ $log->description }}</div>
                        <div class="small text-secondary mt-1">
                            {{ $log->created_at->translatedFormat('d M Y, H:i') }}
                            @if ($log->ip_address)
                                · IP {{ $log->ip_address }}
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="icon-shape icon-xl rounded-circle bg-primary-subtle text-primary mx-auto mb-3">
                        <i class="ti ti-history fs-3"></i>
                    </div>
                    <p class="text-secondary mb-0">Belum ada aktivitas bersama atau aktivitas pribadi Anda yang cocok dengan filter.</p>
                </div>
            @endforelse

            @if ($logs->hasPages())
                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
