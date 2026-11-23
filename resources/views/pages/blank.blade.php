@extends('layouts.app')

@section('title', 'Blank Page')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="mb-6">
                <h1 class="mb-3 h2">Blank Page</h1>
                <p class="mb-0 text-secondary">Halaman contoh untuk mulai membangun fitur baru.</p>
            </div>

            <div class="card card-lg">
                <div class="card-body">
                    <h2 class="h4">Mulai dari sini</h2>
                    <p class="mb-4">
                        File ini ada di <code>resources/views/pages/blank.blade.php</code>. Isi konten di dalam
                        <code>&#64;section('content')</code> dan tambahkan script per halaman dengan
                        <code>&#64;push('scripts')</code>.
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Dashboard</a>
                        <button type="button" class="btn btn-white" data-bs-toggle="modal" data-bs-target="#searchModal">
                            Buka Search Modal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
