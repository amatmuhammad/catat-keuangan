@extends('layouts.app')

@section('title', 'Ecommerce')

@section('content')
    <div class="row mb-6">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="mb-1 h2">Ecommerce</h1>
                    <p class="mb-0 text-secondary">Kelola pesanan, produk, dan pelanggan.</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-white">Import</button>
                    <a href="#" class="btn btn-primary">Add Product</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-xl-3 row-cols-md-3 mb-6 g-6">
        <div class="col">
            <div class="card card-lg">
                <div class="card-body d-flex flex-column gap-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-warning-darker text-warning-lighter">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-cart">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M17 17h-11v-14h-2" />
                                <path d="M6 5l14 1l-1 7h-13" />
                            </svg>
                        </div>
                        <div>Total Orders</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center lh-1">
                        <div class="fs-3 fw-bold">5,312</div>
                        <div class="text-success small">+2.29%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg">
                <div class="card-body d-flex flex-column gap-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-success-darker text-success-lighter">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-coin">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1" />
                                <path d="M12 7v10" />
                            </svg>
                        </div>
                        <div>Revenue</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center lh-1">
                        <div class="fs-3 fw-bold">$120,000</div>
                        <div class="text-success small">+2.19%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg">
                <div class="card-body d-flex flex-column gap-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-info-darker text-info-lighter">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-users">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                            </svg>
                        </div>
                        <div>Customers</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center lh-1">
                        <div class="fs-3 fw-bold">8,920</div>
                        <div class="text-danger small">-1.04%</div>
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
                        <h5 class="mb-0">Recent Orders</h5>
                        <a href="#" class="link-primary">Lihat semua</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">#ORD-001</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ asset('assets/images/avatar/avatar-1.jpg') }}" alt=""
                                                class="avatar avatar-xs rounded-circle" />
                                            <span>Ana Wijaya</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ asset('assets/images/ecommerce/product-1.jpg') }}" alt=""
                                                class="rounded-2" width="36" height="36" />
                                            <span>Headphone</span>
                                        </div>
                                    </td>
                                    <td>$120.00</td>
                                    <td><span class="badge bg-success-subtle text-success-emphasis">Delivered</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">#ORD-002</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ asset('assets/images/avatar/avatar-2.jpg') }}" alt=""
                                                class="avatar avatar-xs rounded-circle" />
                                            <span>Budi Santoso</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ asset('assets/images/ecommerce/product-2.jpg') }}" alt=""
                                                class="rounded-2" width="36" height="36" />
                                            <span>Smart Watch</span>
                                        </div>
                                    </td>
                                    <td>$89.50</td>
                                    <td><span class="badge bg-warning-subtle text-warning-emphasis">Pending</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">#ORD-003</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ asset('assets/images/avatar/avatar-3.jpg') }}" alt=""
                                                class="avatar avatar-xs rounded-circle" />
                                            <span>Citra Lestari</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ asset('assets/images/ecommerce/product-3.jpg') }}" alt=""
                                                class="rounded-2" width="36" height="36" />
                                            <span>Camera Lens</span>
                                        </div>
                                    </td>
                                    <td>$245.00</td>
                                    <td><span class="badge bg-success-subtle text-success-emphasis">Delivered</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">#ORD-004</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ asset('assets/images/avatar/avatar-4.jpg') }}" alt=""
                                                class="avatar avatar-xs rounded-circle" />
                                            <span>Dewi Anggraini</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ asset('assets/images/ecommerce/product-4.jpg') }}" alt=""
                                                class="rounded-2" width="36" height="36" />
                                            <span>Keyboard</span>
                                        </div>
                                    </td>
                                    <td>$45.00</td>
                                    <td><span class="badge bg-danger-subtle text-danger-emphasis">Cancelled</span></td>
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
                    <h5 class="mb-5">Top Products</h5>

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img src="{{ asset('assets/images/ecommerce/product-5.jpg') }}" alt=""
                            class="rounded-2" width="48" height="48" />
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Wireless Mouse</div>
                            <small class="text-secondary">1,204 terjual</small>
                        </div>
                        <div class="fw-semibold">$24</div>
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img src="{{ asset('assets/images/ecommerce/product-6.jpg') }}" alt=""
                            class="rounded-2" width="48" height="48" />
                        <div class="flex-grow-1">
                            <div class="fw-semibold">USB-C Hub</div>
                            <small class="text-secondary">982 terjual</small>
                        </div>
                        <div class="fw-semibold">$39</div>
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img src="{{ asset('assets/images/ecommerce/product-7.jpg') }}" alt=""
                            class="rounded-2" width="48" height="48" />
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Mechanical Keyboard</div>
                            <small class="text-secondary">845 terjual</small>
                        </div>
                        <div class="fw-semibold">$89</div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('assets/images/ecommerce/product-8.jpg') }}" alt=""
                            class="rounded-2" width="48" height="48" />
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Monitor Stand</div>
                            <small class="text-secondary">712 terjual</small>
                        </div>
                        <div class="fw-semibold">$59</div>
                    </div>

                    <a href="#" class="btn btn-white btn-sm mt-5 w-100">Kelola produk</a>
                </div>
            </div>
        </div>
    </div>
@endsection
