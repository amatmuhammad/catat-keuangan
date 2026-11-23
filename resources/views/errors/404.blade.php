@extends('layouts.guest')

@section('title', '404 - Page Not Found')

@push('styles')
    <style>
        /* Ilustrasi 404 aslinya gelap, jadi dibalik warnanya saat dark mode. */
        [data-bs-theme="dark"] .theme-illustration {
            filter: invert(1) hue-rotate(180deg);
        }
    </style>
@endpush

@section('content')
    <main class="vh-100 d-flex align-items-center justify-content-center">
        <section class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="text-center">
                        <div>
                            <img src="{{ asset('assets/images/svg/404.svg') }}" alt="404"
                                class="img-fluid theme-illustration" />
                        </div>
                        <h1 class="display-4">Oops! the page not found.</h1>
                        <p class="mb-6 fs-5">Halaman yang kamu cari tidak tersedia atau sudah dipindahkan.</p>
                        <a href="{{ url('/') }}" class="btn btn-primary btn-lg">Go Home</a>
                    </div>
                </div>
            </div>
        </section>

        <div class="position-absolute end-0 bottom-0 m-4">
            <div class="dropdown">
                <button class="btn btn-light btn-icon rounded-circle d-flex align-items-center" type="button"
                    aria-expanded="false" data-bs-toggle="dropdown" aria-label="Toggle theme (auto)">
                    <i class="ti theme-icon-active lh-1"><i class="ti theme-icon ti-sun"></i></i>
                    <span class="visually-hidden bs-theme-text">Toggle theme</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li>
                        <button type="button" class="dropdown-item d-flex align-items-center active"
                            data-bs-theme-value="light" aria-pressed="true">
                            <i class="ti theme-icon ti-sun"></i>
                            <span class="ms-2">Light</span>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item d-flex align-items-center"
                            data-bs-theme-value="dark" aria-pressed="false">
                            <i class="ti theme-icon ti-moon-stars"></i>
                            <span class="ms-2">Dark</span>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item d-flex align-items-center"
                            data-bs-theme-value="auto" aria-pressed="false">
                            <i class="ti theme-icon ti-circle-half-2"></i>
                            <span class="ms-2">Auto</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </main>
@endsection
