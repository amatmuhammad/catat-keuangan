@php
    $editing = isset($transaction);
    $selectedType = old('type', $transaction->type ?? 'expense');
    $selectedCategory = (string) old('category_id', $transaction->category_id ?? '');
    $selectedWallet = (string) old('wallet_id', $transaction->wallet_id ?? ($wallets->first()->id ?? ''));
    $selectedDate = old('transaction_date', isset($transaction) ? $transaction->transaction_date->format('Y-m-d') : now()->format('Y-m-d'));
@endphp

<form method="POST" action="{{ $action }}" id="transactionForm" data-prevent-duplicate-submit>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="alert alert-info d-flex align-items-start gap-2 py-2 px-3 mb-4 small">
        <i class="ti ti-user-check fs-5"></i>
        <span>
            @if ($editing)
                Transaksi ini dicatat oleh <span class="fw-semibold">{{ $transaction->user?->name ?? 'Pengguna' }}</span>
                di akun <span class="fw-semibold">{{ $currentAccount->name }}</span>.
            @else
                Transaksi akan dicatat oleh <span class="fw-semibold">{{ auth()->user()->name }}</span>
                di akun <span class="fw-semibold">{{ $currentAccount->name }}</span>.
            @endif
        </span>
    </div>

    <div class="mb-4">
        <span class="form-label d-block">Jenis transaksi</span>

        <div class="type-choice">
            <!-- Pemasukan -->
            <input type="radio" name="type" value="income" class="btn-check"
                id="type-income" autocomplete="off"
                @checked($selectedType === 'income' or ! in_array($selectedType, ['income', 'expense']))>
            <label class="choice-card choice-income" for="type-income" data-type="income">
                <span class="choice-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 19l0 -14" />
                        <path d="M16 9l-4 -4l-4 4" />
                    </svg>
                </span>
                <span class="choice-label">Pemasukan</span>
                <span class="choice-desc">Uang masuk: gaji, hadiah, penjualan</span>
            </label>

            <!-- Pengeluaran -->
            <input type="radio" name="type" value="expense" class="btn-check"
                id="type-expense" autocomplete="off"
                @checked($selectedType === 'expense')>
            <label class="choice-card choice-expense" for="type-expense" data-type="expense">
                <span class="choice-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M16 15l-4 4l-4 -4" />
                    </svg>
                </span>
                <span class="choice-label">Pengeluaran</span>
                <span class="choice-desc">Uang keluar: makan, tagihan, belanja</span>
            </label>
        </div>

        <div class="form-text" style="margin-top: 0.5rem; font-size: 0.78rem; color: var(--ds-secondary-color)">
            Pilih jenis transaksi untuk mengelompokkan ke dalam pemasukan atau pengeluaran. Pilihan ini akan mempengaruhi kategori yang tersedia.
        </div>

        @error('type')
            <div class="text-danger small mt-1">{{ $errors->first('type') }}</div>
        @enderror
    </div>

    <div class="mb-4">
        <label for="amount" class="form-label">Nominal</label>
        <div class="input-group input-group-lg">
            <span class="input-group-text">Rp</span>
            <input type="number" id="amount" name="amount" step="0.01" min="0.01" inputmode="decimal"
                class="form-control amount-input @error('amount') is-invalid @enderror"
                value="{{ old('amount', isset($transaction) ? (float) $transaction->amount : '') }}"
                placeholder="0" required>
            @error('amount')
                <div class="invalid-feedback">{{ $errors->first('amount') }}</div>
            @enderror
        </div>
        <div class="form-text">Masukkan angka tanpa titik atau koma pemisah ribuan.</div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <label for="wallet_id" class="form-label">Dompet</label>
            <select id="wallet_id" name="wallet_id" class="form-select @error('wallet_id') is-invalid @enderror" required>
                <option value="">Pilih dompet</option>
                @if ($wallets->contains(fn ($wallet) => $wallet->isShared()))
                    <optgroup label="Kas Bersama">
                        @foreach ($wallets->filter(fn ($wallet) => $wallet->isShared()) as $wallet)
                            <option value="{{ $wallet->id }}" @selected($selectedWallet === (string) $wallet->id)
                                >{{ $wallet->name }} — {{ $wallet->formattedBalance() }}</option>
                        @endforeach
                    </optgroup>
                @endif
                @if ($wallets->contains(fn ($wallet) => ! $wallet->isShared()))
                    <optgroup label="Saldo Pribadi Saya">
                        @foreach ($wallets->filter(fn ($wallet) => ! $wallet->isShared()) as $wallet)
                            <option value="{{ $wallet->id }}" @selected($selectedWallet === (string) $wallet->id)
                                >{{ $wallet->name }} — {{ $wallet->formattedBalance() }}</option>
                        @endforeach
                    </optgroup>
                @endif
            </select>
            <div class="form-text">Pilih Kas Bersama untuk menambah saldo bersama, atau Saldo Pribadi untuk saldo Anda sendiri.</div>
            @error('wallet_id')
                <div class="invalid-feedback">{{ $errors->first('wallet_id') }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="category_id" class="form-label">Kategori</label>
            <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                <option value="">Tanpa kategori</option>
                <optgroup label="Pemasukan">
                    @foreach ($categories->where('type', 'income') as $category)
                        <option value="{{ $category->id }}" data-type="income"
                            @selected($selectedCategory === (string) $category->id)
                            >{{ $category->name }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Pengeluaran">
                    @foreach ($categories->where('type', 'expense') as $category)
                        <option value="{{ $category->id }}" data-type="expense"
                            @selected($selectedCategory === (string) $category->id)
                            >{{ $category->name }}</option>
                    @endforeach
                </optgroup>
            </select>
            @error('category_id')
                <div class="invalid-feedback">{{ $errors->first('category_id') }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="transaction_date" class="form-label">Tanggal transaksi</label>
            <input type="date" id="transaction_date" name="transaction_date"
                class="form-control @error('transaction_date') is-invalid @enderror"
                value="{{ $selectedDate }}" required>
            @error('transaction_date')
                <div class="invalid-feedback">{{ $errors->first('transaction_date') }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="description" class="form-label">Catatan (opsional)</label>
            <input type="text" id="description" name="description" maxlength="255"
                class="form-control @error('description') is-invalid @enderror"
                value="{{ old('description', $transaction->description ?? '') }}"
                placeholder="Misal: makan siang tim">
            @error('description')
                <div class="invalid-feedback">{{ $errors->first('description') }}</div>
            @enderror
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mt-5">
        <button type="submit" class="btn btn-primary px-4">
            {{ $editing ? 'Simpan Perubahan' : 'Simpan Transaksi' }}
        </button>
        <a href="{{ route('transactions.index') }}" class="btn btn-white">Batal</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('transactionForm');
            if (!form) return;
            form.addEventListener('submit', function () {
                if (!form.dataset.submitting) {
                    form.dataset.submitting = '1';
                } else {
                    event.preventDefault();
                    return false;
                }
            });
        });
    </script>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('transactionForm');
                const categorySelect = document.getElementById('category_id');

                if (!form || !categorySelect) {
                    return;
                }

                const typeInputs = form.querySelectorAll('input[name="type"]');

                function syncCategories() {
                    const selected = form.querySelector('input[name="type"]:checked');
                    if (!selected) {
                        return;
                    }

                    categorySelect.querySelectorAll('option[data-type]').forEach(function (option) {
                        const matches = option.dataset.type === selected.value;
                        option.disabled = !matches;
                        option.hidden = !matches;
                    });

                    const current = categorySelect.selectedOptions[0];
                    if (current && current.disabled) {
                        categorySelect.value = '';
                    }
                }

                // Pastikan klik di seluruh area kartu memilih radio yang sesuai.
                const choiceCards = form.querySelectorAll('.choice-card');

                function selectCard(card) {
                    const targetId = card.getAttribute('for');
                    if (!targetId) {
                        return;
                    }

                    const input = document.getElementById(targetId);
                    if (!input) {
                        return;
                    }

                    input.checked = true;

                    choiceCards.forEach(function (item) {
                        item.classList.toggle('selected', item === card);
                    });

                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }

                choiceCards.forEach(function (card) {
                    card.addEventListener('click', function (event) {
                        event.preventDefault();
                        selectCard(card);
                    });

                    card.addEventListener('keydown', function (event) {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            selectCard(card);
                        }
                    });
                });

                typeInputs.forEach(function (input) {
                    input.addEventListener('change', function () {
                        const linkedCard = form.querySelector('.choice-card[for="' + input.id + '"]');
                        if (linkedCard) {
                            choiceCards.forEach(function (item) {
                                item.classList.toggle('selected', item === linkedCard);
                            });
                        }
                        syncCategories();
                    });
                });

                // Samakan state visual saat halaman pertama dimuat.
                const checkedInput = form.querySelector('input[name="type"]:checked');
                if (checkedInput) {
                    const activeCard = form.querySelector('.choice-card[for="' + checkedInput.id + '"]');
                    if (activeCard) {
                        activeCard.classList.add('selected');
                    }
                }

                syncCategories();
            });
        </script>
    @endpush
@endonce