@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
    <div class="row mb-6">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
                <div>
                    <h1 class="page-title mb-1">Kategori</h1>
                    <p class="mb-0 text-secondary">Kelompokkan transaksi pemasukan dan pengeluaran Anda.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('wallets.index') }}" class="btn btn-white">Kelola Dompet</a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryCreateModal">
                        Tambah Kategori
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-xl-6 col-12">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success-emphasis">Pemasukan</span>
                        </div>
                        <span class="small text-secondary">
                            {{ $categories->where('type', 'income')->count() }} kategori
                        </span>
                    </div>

                    @forelse ($categories->where('type', 'income') as $category)
                        <div class="transaction-row">
                            <div class="transaction-icon bg-success-subtle text-success-emphasis">
                                <i class="ti ti-arrow-down-left fs-5"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold text-truncate">{{ $category->name }}</div>
                                <div class="small text-secondary">{{ $category->transactions_count }} transaksi</div>
                            </div>
                            <div class="transaction-actions">
                                <button type="button" class="btn btn-white" data-bs-toggle="modal"
                                    data-bs-target="#categoryEditModal" data-name="{{ $category->name }}"
                                    data-type="{{ $category->type }}"
                                    data-action="{{ route('categories.update', $category) }}">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                    onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger"
                                        @disabled($category->transactions_count > 0)
                                        title="{{ $category->transactions_count > 0 ? 'Kategori masih dipakai transaksi' : 'Hapus kategori' }}">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <p class="text-secondary mb-3">Belum ada kategori pemasukan.</p>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#categoryCreateModal">Tambah Kategori</button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-12">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger-subtle text-danger-emphasis">Pengeluaran</span>
                        </div>
                        <span class="small text-secondary">
                            {{ $categories->where('type', 'expense')->count() }} kategori
                        </span>
                    </div>

                    @forelse ($categories->where('type', 'expense') as $category)
                        <div class="transaction-row">
                            <div class="transaction-icon bg-danger-subtle text-danger-emphasis">
                                <i class="ti ti-arrow-up-right fs-5"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold text-truncate">{{ $category->name }}</div>
                                <div class="small text-secondary">{{ $category->transactions_count }} transaksi</div>
                            </div>
                            <div class="transaction-actions">
                                <button type="button" class="btn btn-white" data-bs-toggle="modal"
                                    data-bs-target="#categoryEditModal" data-name="{{ $category->name }}"
                                    data-type="{{ $category->type }}"
                                    data-action="{{ route('categories.update', $category) }}">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                    onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger"
                                        @disabled($category->transactions_count > 0)
                                        title="{{ $category->transactions_count > 0 ? 'Kategori masih dipakai transaksi' : 'Hapus kategori' }}">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <p class="text-secondary mb-3">Belum ada kategori pengeluaran.</p>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#categoryCreateModal">Tambah Kategori</button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-light border mt-6 mb-0 small text-secondary">
        Kategori yang sudah dipakai transaksi tidak dapat dihapus agar riwayat arus kas tetap utuh.
        Ubah jenis kategori juga tidak diperbolehkan jika sudah memiliki transaksi.
    </div>

    <!-- Modal tambah kategori -->
    <div class="modal fade" id="categoryCreateModal" tabindex="-1" aria-labelledby="categoryCreateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('categories.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryCreateModalLabel">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Nama kategori</label>
                        <input type="text" id="category_name" name="name" class="form-control" maxlength="50" required
                            placeholder="Misal: Belanja Bulanan">
                    </div>
                    <div>
                        <label for="category_type" class="form-label">Jenis</label>
                        <select id="category_type" name="type" class="form-select" required>
                            <option value="expense">Pengeluaran</option>
                            <option value="income">Pemasukan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal edit kategori -->
    <div class="modal fade" id="categoryEditModal" tabindex="-1" aria-labelledby="categoryEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="categoryEditForm" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryEditModalLabel">Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="category_edit_name" class="form-label">Nama kategori</label>
                        <input type="text" id="category_edit_name" name="name" class="form-control" maxlength="50" required>
                    </div>
                    <div>
                        <label for="category_edit_type" class="form-label">Jenis</label>
                        <select id="category_edit_type" name="type" class="form-select" required>
                            <option value="expense">Pengeluaran</option>
                            <option value="income">Pemasukan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categoryEditModal = document.getElementById('categoryEditModal');
            const categoryEditForm = document.getElementById('categoryEditForm');
            const categoryEditName = document.getElementById('category_edit_name');
            const categoryEditType = document.getElementById('category_edit_type');

            if (!categoryEditModal) {
                return;
            }

            categoryEditModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) return;

                categoryEditForm.action = button.dataset.action;
                categoryEditName.value = button.dataset.name;
                categoryEditType.value = button.dataset.type;
            });
        });
    </script>
@endpush
