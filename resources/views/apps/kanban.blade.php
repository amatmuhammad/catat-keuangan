@extends('layouts.app')

@section('title', 'Kanban')

@section('content')
    <div class="row mb-6">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="mb-1 h2">Kanban</h1>
                    <p class="mb-0 text-secondary">Pantau progres pekerjaan tim.</p>
                </div>
                <button type="button" class="btn btn-primary">Add Task</button>
            </div>
        </div>
    </div>

    <div class="row g-6">
        {{-- To Do --}}
        <div class="col-xl-4 col-12">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">To Do</h5>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis">3</span>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <div class="border rounded-3 p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-danger-subtle text-danger-emphasis">High</span>
                                <button type="button" class="btn btn-icon btn-ghost btn-sm" aria-label="Menu">•••</button>
                            </div>
                            <h6 class="mb-1">Desain halaman checkout</h6>
                            <p class="text-secondary small mb-3">Buat wireframe dan UI final untuk halaman checkout.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex">
                                    <img src="{{ asset('assets/images/avatar/avatar-1.jpg') }}" alt=""
                                        class="avatar avatar-xs rounded-circle" />
                                    <img src="{{ asset('assets/images/avatar/avatar-2.jpg') }}" alt=""
                                        class="avatar avatar-xs rounded-circle ms-n2" />
                                </div>
                                <small class="text-secondary">28 Okt</small>
                            </div>
                        </div>

                        <div class="border rounded-3 p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-warning-subtle text-warning-emphasis">Medium</span>
                                <button type="button" class="btn btn-icon btn-ghost btn-sm" aria-label="Menu">•••</button>
                            </div>
                            <h6 class="mb-1">Riset kompetitor</h6>
                            <p class="text-secondary small mb-3">Kumpulkan data harga dan fitur kompetitor utama.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <img src="{{ asset('assets/images/avatar/avatar-3.jpg') }}" alt=""
                                    class="avatar avatar-xs rounded-circle" />
                                <small class="text-secondary">30 Okt</small>
                            </div>
                        </div>

                        <div class="border rounded-3 p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-info-subtle text-info-emphasis">Low</span>
                                <button type="button" class="btn btn-icon btn-ghost btn-sm" aria-label="Menu">•••</button>
                            </div>
                            <h6 class="mb-1">Update dokumentasi API</h6>
                            <p class="text-secondary small mb-3">Tambahkan endpoint baru ke dokumentasi.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <img src="{{ asset('assets/images/avatar/avatar-4.jpg') }}" alt=""
                                    class="avatar avatar-xs rounded-circle" />
                                <small class="text-secondary">2 Nov</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- In Progress --}}
        <div class="col-xl-4 col-12">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">In Progress</h5>
                        <span class="badge bg-primary-subtle text-primary-emphasis">2</span>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <div class="border rounded-3 p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-danger-subtle text-danger-emphasis">High</span>
                                <button type="button" class="btn btn-icon btn-ghost btn-sm" aria-label="Menu">•••</button>
                            </div>
                            <h6 class="mb-1">Integrasi payment gateway</h6>
                            <p class="text-secondary small mb-3">Implementasi Midtrans sandbox untuk pembayaran.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex">
                                    <img src="{{ asset('assets/images/avatar/avatar-5.jpg') }}" alt=""
                                        class="avatar avatar-xs rounded-circle" />
                                    <img src="{{ asset('assets/images/avatar/avatar-6.jpg') }}" alt=""
                                        class="avatar avatar-xs rounded-circle ms-n2" />
                                </div>
                                <small class="text-secondary">27 Okt</small>
                            </div>
                        </div>

                        <div class="border rounded-3 p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-warning-subtle text-warning-emphasis">Medium</span>
                                <button type="button" class="btn btn-icon btn-ghost btn-sm" aria-label="Menu">•••</button>
                            </div>
                            <h6 class="mb-1">Halaman profil pengguna</h6>
                            <p class="text-secondary small mb-3">Slicing UI profil dan form edit data.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <img src="{{ asset('assets/images/avatar/avatar-7.jpg') }}" alt=""
                                    class="avatar avatar-xs rounded-circle" />
                                <small class="text-secondary">29 Okt</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Done --}}
        <div class="col-xl-4 col-12">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Done</h5>
                        <span class="badge bg-success-subtle text-success-emphasis">3</span>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <div class="border rounded-3 p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-success-subtle text-success-emphasis">Done</span>
                                <button type="button" class="btn btn-icon btn-ghost btn-sm" aria-label="Menu">•••</button>
                            </div>
                            <h6 class="mb-1">Setup repository</h6>
                            <p class="text-secondary small mb-3">Inisialisasi project dan konfigurasi CI.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <img src="{{ asset('assets/images/avatar/avatar-8.jpg') }}" alt=""
                                    class="avatar avatar-xs rounded-circle" />
                                <small class="text-secondary">20 Okt</small>
                            </div>
                        </div>

                        <div class="border rounded-3 p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-success-subtle text-success-emphasis">Done</span>
                                <button type="button" class="btn btn-icon btn-ghost btn-sm" aria-label="Menu">•••</button>
                            </div>
                            <h6 class="mb-1">Desain landing page</h6>
                            <p class="text-secondary small mb-3">Finalisasi hero, fitur, dan CTA.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <img src="{{ asset('assets/images/avatar/avatar-1.jpg') }}" alt=""
                                    class="avatar avatar-xs rounded-circle" />
                                <small class="text-secondary">18 Okt</small>
                            </div>
                        </div>

                        <div class="border rounded-3 p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-success-subtle text-success-emphasis">Done</span>
                                <button type="button" class="btn btn-icon btn-ghost btn-sm" aria-label="Menu">•••</button>
                            </div>
                            <h6 class="mb-1">Migrasi database</h6>
                            <p class="text-secondary small mb-3">Pindahkan data dari server lama.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <img src="{{ asset('assets/images/avatar/avatar-2.jpg') }}" alt=""
                                    class="avatar avatar-xs rounded-circle" />
                                <small class="text-secondary">15 Okt</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
