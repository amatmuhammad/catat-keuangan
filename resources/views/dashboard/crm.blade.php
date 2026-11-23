@extends('layouts.app')

@section('title', 'CRM')

@section('content')
    <div class="row mb-6">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="mb-1 h2">CRM</h1>
                    <p class="mb-0 text-secondary">Pantau kontak, perusahaan, dan peluang penjualan.</p>
                </div>
                <a href="#" class="btn btn-primary">Add Contact</a>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-xl-3 row-cols-md-3 mb-6 g-6">
        <div class="col">
            <div class="card card-lg">
                <div class="card-body d-flex flex-column gap-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-primary-subtle text-primary-emphasis">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                            </svg>
                        </div>
                        <div>Contacts</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center lh-1">
                        <div class="fs-3 fw-bold">1,248</div>
                        <div class="text-success small">+4.8%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg">
                <div class="card-body d-flex flex-column gap-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-info-subtle text-info-emphasis">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-building">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 21l18 0" />
                                <path d="M5 21v-14l8 -4v18" />
                                <path d="M19 21v-10l-6 -4" />
                                <path d="M9 9l0 .01" />
                                <path d="M9 12l0 .01" />
                                <path d="M9 15l0 .01" />
                                <path d="M9 18l0 .01" />
                            </svg>
                        </div>
                        <div>Companies</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center lh-1">
                        <div class="fs-3 fw-bold">86</div>
                        <div class="text-success small">+2.1%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg">
                <div class="card-body d-flex flex-column gap-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-success-subtle text-success-emphasis">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-target">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                <path d="M12 12m-5 0a5 5 0 1 0 10 0a5 5 0 1 0 -10 0" />
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            </svg>
                        </div>
                        <div>Open Deals</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center lh-1">
                        <div class="fs-3 fw-bold">42</div>
                        <div class="text-warning small">+8.4%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-xl-8 col-12">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <h5 class="mb-0">Contacts</h5>
                        <div class="dropdown">
                            <button class="btn btn-white btn-sm" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Filter</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Semua</a></li>
                                <li><a class="dropdown-item" href="#">Aktif</a></li>
                                <li><a class="dropdown-item" href="#">Tidak aktif</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Perusahaan</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ asset('assets/images/avatar/avatar-5.jpg') }}" alt=""
                                                class="avatar avatar-sm rounded-circle" />
                                            <span class="fw-semibold">Rina Kartika</span>
                                        </div>
                                    </td>
                                    <td>PT Maju Jaya</td>
                                    <td>rina@majujaya.co.id</td>
                                    <td><span class="badge bg-success-subtle text-success-emphasis">Active</span></td>
                                    <td class="text-end"><a href="#" class="link-primary">Detail</a></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ asset('assets/images/avatar/avatar-6.jpg') }}" alt=""
                                                class="avatar avatar-sm rounded-circle" />
                                            <span class="fw-semibold">Andi Pratama</span>
                                        </div>
                                    </td>
                                    <td>CV Karya Mandiri</td>
                                    <td>andi@karya.co.id</td>
                                    <td><span class="badge bg-warning-subtle text-warning-emphasis">Follow up</span></td>
                                    <td class="text-end"><a href="#" class="link-primary">Detail</a></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ asset('assets/images/avatar/avatar-7.jpg') }}" alt=""
                                                class="avatar avatar-sm rounded-circle" />
                                            <span class="fw-semibold">Siti Aminah</span>
                                        </div>
                                    </td>
                                    <td>PT Sentosa</td>
                                    <td>siti@sentosa.id</td>
                                    <td><span class="badge bg-success-subtle text-success-emphasis">Active</span></td>
                                    <td class="text-end"><a href="#" class="link-primary">Detail</a></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ asset('assets/images/avatar/avatar-8.jpg') }}" alt=""
                                                class="avatar avatar-sm rounded-circle" />
                                            <span class="fw-semibold">Hendra Gunawan</span>
                                        </div>
                                    </td>
                                    <td>Startup Nusantara</td>
                                    <td>hendra@nusantara.io</td>
                                    <td><span class="badge bg-secondary-subtle text-secondary-emphasis">Inactive</span></td>
                                    <td class="text-end"><a href="#" class="link-primary">Detail</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-12">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <h5 class="mb-5">Deals Pipeline</h5>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Proposal</span>
                            <span class="text-secondary">$24,000</span>
                        </div>
                        <div class="progress" style="height: 6px">
                            <div class="progress-bar bg-primary" style="width: 75%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Negotiation</span>
                            <span class="text-secondary">$18,500</span>
                        </div>
                        <div class="progress" style="height: 6px">
                            <div class="progress-bar bg-info" style="width: 55%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Closed Won</span>
                            <span class="text-secondary">$42,000</span>
                        </div>
                        <div class="progress" style="height: 6px">
                            <div class="progress-bar bg-success" style="width: 90%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Closed Lost</span>
                            <span class="text-secondary">$6,200</span>
                        </div>
                        <div class="progress" style="height: 6px">
                            <div class="progress-bar bg-danger" style="width: 20%"></div>
                        </div>
                    </div>

                    <a href="#" class="btn btn-white btn-sm mt-5 w-100">Lihat pipeline</a>
                </div>
            </div>
        </div>
    </div>
@endsection
