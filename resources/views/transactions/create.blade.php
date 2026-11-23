@extends('layouts.app')

@section('title', 'Catat Transaksi')

@section('content')
    <div class="row mb-6">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="page-title mb-1">Catat Transaksi</h1>
                    <p class="mb-0 text-secondary">Saldo dompet akan langsung disesuaikan setelah disimpan.</p>
                </div>
                <a href="{{ route('transactions.index') }}" class="btn btn-white">Kembali</a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8 col-12">
            <div class="card card-lg">
                <div class="card-body p-4 p-md-5">
                    @include('transactions._form', [
                        'action' => route('transactions.store'),
                        'method' => 'POST',
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection
