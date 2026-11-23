@extends('layouts.app')

@section('title', 'Blog')

@section('content')
    <div class="row mb-6">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="mb-1 h2">Blog</h1>
                    <p class="mb-0 text-secondary">Kelola artikel dan konten website.</p>
                </div>
                <a href="#" class="btn btn-primary">New Post</a>
            </div>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-xl-4 col-md-6">
            <div class="card card-lg h-100">
                <img src="{{ asset('assets/images/ecommerce/product-9.jpg') }}" class="card-img-top rounded-top-3"
                    alt="" style="height: 200px; object-fit: cover" />
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-primary-subtle text-primary-emphasis">Marketing</span>
                        <small class="text-secondary">24 Okt 2026</small>
                    </div>
                    <h4 class="h5 mb-2">Strategi Meningkatkan Penjualan Online</h4>
                    <p class="text-secondary mb-4">
                        Panduan praktis menyusun funnel penjualan yang efektif untuk toko online.
                    </p>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/images/avatar/avatar-1.jpg') }}" alt=""
                                class="avatar avatar-xs rounded-circle" />
                            <small>Ana Wijaya</small>
                        </div>
                        <a href="#" class="link-primary small">Baca</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card card-lg h-100">
                <img src="{{ asset('assets/images/ecommerce/product-10.jpg') }}" class="card-img-top rounded-top-3"
                    alt="" style="height: 200px; object-fit: cover" />
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-info-subtle text-info-emphasis">Tutorial</span>
                        <small class="text-secondary">21 Okt 2026</small>
                    </div>
                    <h4 class="h5 mb-2">Cara Integrasi Payment Gateway</h4>
                    <p class="text-secondary mb-4">
                        Langkah demi langkah menghubungkan payment gateway ke aplikasi Laravel.
                    </p>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/images/avatar/avatar-2.jpg') }}" alt=""
                                class="avatar avatar-xs rounded-circle" />
                            <small>Budi Santoso</small>
                        </div>
                        <a href="#" class="link-primary small">Baca</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card card-lg h-100">
                <img src="{{ asset('assets/images/ecommerce/product-3.jpg') }}" class="card-img-top rounded-top-3"
                    alt="" style="height: 200px; object-fit: cover" />
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-success-subtle text-success-emphasis">Update</span>
                        <small class="text-secondary">18 Okt 2026</small>
                    </div>
                    <h4 class="h5 mb-2">Fitur Baru di Versi 1.1</h4>
                    <p class="text-secondary mb-4">
                        Ringkasan peningkatan performa dan fitur baru yang baru saja dirilis.
                    </p>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/images/avatar/avatar-3.jpg') }}" alt=""
                                class="avatar avatar-xs rounded-circle" />
                            <small>Citra Lestari</small>
                        </div>
                        <a href="#" class="link-primary small">Baca</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-lg">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-5">
                <h5 class="mb-0">Semua Artikel</h5>
                <div class="dropdown">
                    <button class="btn btn-white btn-sm" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">Kategori</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Semua</a></li>
                        <li><a class="dropdown-item" href="#">Marketing</a></li>
                        <li><a class="dropdown-item" href="#">Tutorial</a></li>
                        <li><a class="dropdown-item" href="#">Update</a></li>
                    </ul>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold">Strategi Meningkatkan Penjualan Online</td>
                            <td>Marketing</td>
                            <td><span class="badge bg-success-subtle text-success-emphasis">Published</span></td>
                            <td>24 Okt 2026</td>
                            <td class="text-end"><a href="#" class="link-primary">Edit</a></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Cara Integrasi Payment Gateway</td>
                            <td>Tutorial</td>
                            <td><span class="badge bg-success-subtle text-success-emphasis">Published</span></td>
                            <td>21 Okt 2026</td>
                            <td class="text-end"><a href="#" class="link-primary">Edit</a></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Fitur Baru di Versi 1.1</td>
                            <td>Update</td>
                            <td><span class="badge bg-success-subtle text-success-emphasis">Published</span></td>
                            <td>18 Okt 2026</td>
                            <td class="text-end"><a href="#" class="link-primary">Edit</a></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Tips SEO untuk Pemula</td>
                            <td>Marketing</td>
                            <td><span class="badge bg-warning-subtle text-warning-emphasis">Draft</span></td>
                            <td>-</td>
                            <td class="text-end"><a href="#" class="link-primary">Edit</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
