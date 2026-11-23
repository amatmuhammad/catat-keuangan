@extends('layouts.app')

@section('title', 'Chat')

@section('content')
    <div class="row mb-6">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="mb-1 h2">Chat</h1>
                    <p class="mb-0 text-secondary">Percakapan tim dan pelanggan.</p>
                </div>
                <button type="button" class="btn btn-primary">New Message</button>
            </div>
        </div>
    </div>

    <div class="card card-lg overflow-hidden">
        <div class="row g-0">
            {{-- Daftar kontak --}}
            <div class="col-lg-4 border-end border-dashed">
                <div class="p-4 border-bottom border-dashed">
                    <input type="search" class="form-control" placeholder="Cari percakapan..." />
                </div>
                <div class="list-group list-group-flush" style="max-height: 540px; overflow-y: auto">
                    <a href="#" class="list-group-item list-group-item-action active border-0 p-4">
                        <div class="d-flex gap-3">
                            <img src="{{ asset('assets/images/avatar/avatar-1.jpg') }}" alt=""
                                class="avatar avatar-md rounded-circle" />
                            <div class="flex-grow-1" style="min-width: 0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Ana Wijaya</span>
                                    <small>10:24</small>
                                </div>
                                <small class="d-block text-truncate">Baik, laporan sudah saya kirim.</small>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 p-4">
                        <div class="d-flex gap-3">
                            <img src="{{ asset('assets/images/avatar/avatar-2.jpg') }}" alt=""
                                class="avatar avatar-md rounded-circle" />
                            <div class="flex-grow-1" style="min-width: 0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Budi Santoso</span>
                                    <small>09:12</small>
                                </div>
                                <small class="d-block text-truncate">Meeting jam 2 masih on?</small>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 p-4">
                        <div class="d-flex gap-3">
                            <img src="{{ asset('assets/images/avatar/avatar-3.jpg') }}" alt=""
                                class="avatar avatar-md rounded-circle" />
                            <div class="flex-grow-1" style="min-width: 0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Citra Lestari</span>
                                    <span class="badge bg-primary rounded-pill">2</span>
                                </div>
                                <small class="d-block text-truncate">Stok produk terbaru sudah masuk.</small>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 p-4">
                        <div class="d-flex gap-3">
                            <img src="{{ asset('assets/images/avatar/avatar-4.jpg') }}" alt=""
                                class="avatar avatar-md rounded-circle" />
                            <div class="flex-grow-1" style="min-width: 0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Dewi Anggraini</span>
                                    <small>Kemarin</small>
                                </div>
                                <small class="d-block text-truncate">Terima kasih bantuannya.</small>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 p-4">
                        <div class="d-flex gap-3">
                            <img src="{{ asset('assets/images/avatar/avatar-5.jpg') }}" alt=""
                                class="avatar avatar-md rounded-circle" />
                            <div class="flex-grow-1" style="min-width: 0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Rina Kartika</span>
                                    <small>Kemarin</small>
                                </div>
                                <small class="d-block text-truncate">Invoice sudah dibayar ya.</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Ruang chat --}}
            <div class="col-lg-8">
                <div class="d-flex align-items-center justify-content-between p-4 border-bottom border-dashed">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('assets/images/avatar/avatar-1.jpg') }}" alt=""
                            class="avatar avatar-md rounded-circle" />
                        <div>
                            <div class="fw-semibold">Ana Wijaya</div>
                            <small class="text-success">Online</small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-icon btn-ghost" aria-label="Menu">•••</button>
                </div>

                <div class="p-4 d-flex flex-column gap-4" style="height: 420px; overflow-y: auto">
                    <div class="d-flex gap-3">
                        <img src="{{ asset('assets/images/avatar/avatar-1.jpg') }}" alt=""
                            class="avatar avatar-sm rounded-circle" />
                        <div class="bg-gray-100 rounded-3 p-3" style="max-width: 70%">
                            <p class="mb-1">Halo, laporan penjualan minggu ini sudah siap?</p>
                            <small class="text-secondary">10:20</small>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end">
                        <div class="bg-primary text-white rounded-3 p-3" style="max-width: 70%">
                            <p class="mb-1">Sudah, sebentar saya kirimkan ya.</p>
                            <small class="text-white-50">10:22</small>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end">
                        <div class="bg-primary text-white rounded-3 p-3" style="max-width: 70%">
                            <p class="mb-0">Laporan-Q3-2026.pdf</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <img src="{{ asset('assets/images/avatar/avatar-1.jpg') }}" alt=""
                            class="avatar avatar-sm rounded-circle" />
                        <div class="bg-gray-100 rounded-3 p-3" style="max-width: 70%">
                            <p class="mb-1">Baik, laporan sudah saya kirim ke tim finance.</p>
                            <small class="text-secondary">10:24</small>
                        </div>
                    </div>
                </div>

                <div class="p-4 border-top border-dashed">
                    <form class="d-flex gap-2" onsubmit="return false">
                        <input type="text" class="form-control" placeholder="Tulis pesan..." />
                        <button type="submit" class="btn btn-primary">Kirim</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
