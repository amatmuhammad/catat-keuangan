@extends('layouts.app')

@section('title', 'Finance')

@section('content')
    <div class="row mb-6">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="mb-1 h2">Finance</h1>
                    <p class="mb-0 text-secondary">Saldo, pemasukan, pengeluaran, dan riwayat transaksi.</p>
                </div>
                <a href="#" class="btn btn-primary">Add Transaction</a>
            </div>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-xl-5 col-12">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-8">
                        <div>
                            <p class="mb-1 text-secondary">Total Balance</p>
                            <h2 class="mb-0">$48,320.00</h2>
                        </div>
                        <img src="{{ asset('assets/images/creditcard/mastercard.svg') }}" alt="Mastercard" width="48" />
                    </div>
                    <div class="d-flex justify-content-between align-items-end">
                        <div>
                            <small class="text-secondary d-block mb-1">Card Number</small>
                            <span class="fw-semibold">**** **** **** 4281</span>
                        </div>
                        <div class="text-end">
                            <small class="text-secondary d-block mb-1">Expires</small>
                            <span class="fw-semibold">09/28</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-7 col-12">
            <div class="row g-6 h-100">
                <div class="col-md-6">
                    <div class="card card-lg h-100">
                        <div class="card-body d-flex flex-column gap-8">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-shape icon-lg rounded-circle bg-success-subtle text-success-emphasis">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-trending-up">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 17l6 -6l4 4l8 -8" />
                                        <path d="M14 7l7 0l0 7" />
                                    </svg>
                                </div>
                                <div>Income</div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center lh-1">
                                <div class="fs-3 fw-bold">$12,480</div>
                                <div class="text-success small">+6.2%</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-lg h-100">
                        <div class="card-body d-flex flex-column gap-8">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-shape icon-lg rounded-circle bg-danger-subtle text-danger-emphasis">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-trending-down">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 7l6 6l4 -4l8 8" />
                                        <path d="M21 10l0 7l-7 0" />
                                    </svg>
                                </div>
                                <div>Expenses</div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center lh-1">
                                <div class="fs-3 fw-bold">$7,260</div>
                                <div class="text-danger small">-2.8%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-lg">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-5">
                <h5 class="mb-0">Transactions</h5>
                <button type="button" class="btn btn-white btn-sm">Download CSV</button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th>Kategori</th>
                            <th>Metode</th>
                            <th class="text-end">Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>24 Okt 2026</td>
                            <td>Pembayaran invoice #INV-1024</td>
                            <td><span class="badge bg-primary-subtle text-primary-emphasis">Sales</span></td>
                            <td>Transfer Bank</td>
                            <td class="text-end text-success fw-semibold">+$2,400.00</td>
                            <td><span class="badge bg-success-subtle text-success-emphasis">Completed</span></td>
                        </tr>
                        <tr>
                            <td>23 Okt 2026</td>
                            <td>Iklan Instagram</td>
                            <td><span class="badge bg-warning-subtle text-warning-emphasis">Marketing</span></td>
                            <td>Kartu Kredit</td>
                            <td class="text-end text-danger fw-semibold">-$320.00</td>
                            <td><span class="badge bg-success-subtle text-success-emphasis">Completed</span></td>
                        </tr>
                        <tr>
                            <td>22 Okt 2026</td>
                            <td>Langganan SaaS</td>
                            <td><span class="badge bg-info-subtle text-info-emphasis">Operational</span></td>
                            <td>Kartu Kredit</td>
                            <td class="text-end text-danger fw-semibold">-$89.00</td>
                            <td><span class="badge bg-warning-subtle text-warning-emphasis">Pending</span></td>
                        </tr>
                        <tr>
                            <td>21 Okt 2026</td>
                            <td>Penjualan produk</td>
                            <td><span class="badge bg-primary-subtle text-primary-emphasis">Sales</span></td>
                            <td>E-Wallet</td>
                            <td class="text-end text-success fw-semibold">+$1,150.00</td>
                            <td><span class="badge bg-success-subtle text-success-emphasis">Completed</span></td>
                        </tr>
                        <tr>
                            <td>20 Okt 2026</td>
                            <td>Gaji freelance designer</td>
                            <td><span class="badge bg-secondary-subtle text-secondary-emphasis">Payroll</span></td>
                            <td>Transfer Bank</td>
                            <td class="text-end text-danger fw-semibold">-$1,800.00</td>
                            <td><span class="badge bg-success-subtle text-success-emphasis">Completed</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
