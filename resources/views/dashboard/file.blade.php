@extends('layouts.app')

@section('title', 'File Manager')

@section('content')
    <div class="row mb-6">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="mb-1 h2">File Manager</h1>
                    <p class="mb-0 text-secondary">Kelola dokumen dan aset yang diunggah.</p>
                </div>
                <a href="#" class="btn btn-primary">Upload File</a>
            </div>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-xl-4 col-12">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Storage</h5>
                        <span class="text-secondary small">64% terpakai</span>
                    </div>
                    <div class="progress mb-4" style="height: 8px">
                        <div class="progress-bar bg-primary" style="width: 64%"></div>
                    </div>
                    <p class="text-secondary mb-0">6.4 GB dari 10 GB digunakan.</p>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-12">
            <div class="row row-cols-3 g-6 h-100">
                <div class="col">
                    <div class="card card-lg h-100">
                        <div class="card-body text-center">
                            <div class="icon-shape icon-lg rounded-circle bg-primary-subtle text-primary-emphasis mx-auto mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-folder">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2" />
                                </svg>
                            </div>
                            <div class="fs-4 fw-bold">128</div>
                            <small class="text-secondary">Folders</small>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card card-lg h-100">
                        <div class="card-body text-center">
                            <div class="icon-shape icon-lg rounded-circle bg-success-subtle text-success-emphasis mx-auto mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                </svg>
                            </div>
                            <div class="fs-4 fw-bold">1,024</div>
                            <small class="text-secondary">Files</small>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card card-lg h-100">
                        <div class="card-body text-center">
                            <div class="icon-shape icon-lg rounded-circle bg-warning-subtle text-warning-emphasis mx-auto mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-share">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M6 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                    <path d="M18 6m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                    <path d="M18 18m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                    <path d="M8.7 10.7l6.6 -3.4" />
                                    <path d="M8.7 13.3l6.6 3.4" />
                                </svg>
                            </div>
                            <div class="fs-4 fw-bold">36</div>
                            <small class="text-secondary">Shared</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-lg">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-5">
                <h5 class="mb-0">Recent Files</h5>
                <div class="d-flex gap-2">
                    <input type="search" class="form-control form-control-sm" placeholder="Cari file..." style="width: 200px" />
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Pemilik</th>
                            <th>Ukuran</th>
                            <th>Terakhir diubah</th>
                            <th class="text-end"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="icon-shape rounded-2 bg-primary-subtle text-primary-emphasis">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-text">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                            <path d="M9 9l1 0" />
                                            <path d="M9 13l6 0" />
                                            <path d="M9 17l6 0" />
                                        </svg>
                                    </span>
                                    <div>
                                        <div class="fw-semibold">Laporan-Q3-2026.pdf</div>
                                        <small class="text-secondary">PDF</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ asset('assets/images/avatar/avatar-1.jpg') }}" alt=""
                                        class="avatar avatar-xs rounded-circle" />
                                    <span>Ana</span>
                                </div>
                            </td>
                            <td>2.4 MB</td>
                            <td>24 Okt 2026</td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-ghost" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">•••</button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#">Download</a></li>
                                        <li><a class="dropdown-item" href="#">Rename</a></li>
                                        <li><a class="dropdown-item text-danger" href="#">Hapus</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="icon-shape rounded-2 bg-success-subtle text-success-emphasis">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-photo">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M15 8h.01" />
                                            <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" />
                                            <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" />
                                            <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" />
                                        </svg>
                                    </span>
                                    <div>
                                        <div class="fw-semibold">banner-promo.png</div>
                                        <small class="text-secondary">PNG</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ asset('assets/images/avatar/avatar-2.jpg') }}" alt=""
                                        class="avatar avatar-xs rounded-circle" />
                                    <span>Budi</span>
                                </div>
                            </td>
                            <td>860 KB</td>
                            <td>23 Okt 2026</td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-ghost" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">•••</button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#">Download</a></li>
                                        <li><a class="dropdown-item" href="#">Rename</a></li>
                                        <li><a class="dropdown-item text-danger" href="#">Hapus</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="icon-shape rounded-2 bg-info-subtle text-info-emphasis">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-zip">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                            <path d="M11 5h1" />
                                            <path d="M11 7h1" />
                                            <path d="M11 9h1" />
                                        </svg>
                                    </span>
                                    <div>
                                        <div class="fw-semibold">aset-website.zip</div>
                                        <small class="text-secondary">ZIP</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ asset('assets/images/avatar/avatar-3.jpg') }}" alt=""
                                        class="avatar avatar-xs rounded-circle" />
                                    <span>Citra</span>
                                </div>
                            </td>
                            <td>12.8 MB</td>
                            <td>20 Okt 2026</td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-ghost" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">•••</button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#">Download</a></li>
                                        <li><a class="dropdown-item" href="#">Rename</a></li>
                                        <li><a class="dropdown-item text-danger" href="#">Hapus</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="icon-shape rounded-2 bg-warning-subtle text-warning-emphasis">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-spreadsheet">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                            <path d="M8 11h8v7h-8z" />
                                            <path d="M8 15h8" />
                                            <path d="M11 11v7" />
                                        </svg>
                                    </span>
                                    <div>
                                        <div class="fw-semibold">data-penjualan.xlsx</div>
                                        <small class="text-secondary">XLSX</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ asset('assets/images/avatar/avatar-4.jpg') }}" alt=""
                                        class="avatar avatar-xs rounded-circle" />
                                    <span>Dewi</span>
                                </div>
                            </td>
                            <td>540 KB</td>
                            <td>18 Okt 2026</td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-ghost" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">•••</button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#">Download</a></li>
                                        <li><a class="dropdown-item" href="#">Rename</a></li>
                                        <li><a class="dropdown-item text-danger" href="#">Hapus</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
