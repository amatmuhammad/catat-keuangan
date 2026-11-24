{{-- Tombol pasang aplikasi / pintasan layar utama --}}
{{-- Variant: nav (default, untuk topbar) atau guest (mengambang di halaman login) --}}
@if (($variant ?? 'nav') === 'nav')
    <li id="installAppNavItem">
        <button type="button" class="btn btn-white btn-sm d-inline-flex align-items-center gap-2"
            id="installAppBtn" aria-label="Pasang aplikasi di layar utama">
            <i class="ti ti-download lh-1 fs-5"></i>
            <span class="d-none d-md-inline">Pasang Aplikasi</span>
        </button>
    </li>
@else
    <button type="button" class="install-guest-fab" id="installAppBtn"
        aria-label="Pasang aplikasi di layar utama">
        <i class="ti ti-download lh-1"></i>
        <span>Pasang Aplikasi</span>
    </button>
@endif

{{-- Modal panduan --}}
<div class="modal" id="installAppModal" tabindex="-1" aria-labelledby="installAppModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('icons/icon-192x192.png') }}" alt="" class="rounded-3"
                        width="44" height="44">
                    <div>
                        <h5 class="modal-title mb-0" id="installAppModalLabel">Pasang Catat Keuangan</h5>
                        <div class="small text-secondary">Buka cepat dari layar utama ponsel Anda.</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body pt-3">
                {{-- Area untuk pemasangan otomatis (Android/Chrome) --}}
                <div id="installAutoSection" class="d-none">
                    <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-primary-subtle mb-3">
                        <i class="ti ti-device-mobile-check fs-3 text-primary lh-1"></i>
                        <div class="small">
                            Tekan tombol di bawah untuk memasang aplikasi langsung dari browser.
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="installAppConfirmBtn">
                        <i class="ti ti-download me-1"></i> Pasang Sekarang
                    </button>
                </div>

                {{-- Panduan manual (fallback semua browser) --}}
                <div id="installManualSection">
                    <div class="small text-secondary mb-3">Ikuti langkah sesuai browser Anda:</div>

                    <div class="install-step">
                        <span class="install-step-number">1</span>
                        <div>
                            <div class="fw-semibold">Buka menu browser</div>
                            <div class="small text-secondary" id="installBrowserHint">
                                Ketuk ikon <span class="fw-semibold">⋮</span> (titik tiga) di pojok kanan atas
                                Chrome.
                            </div>
                        </div>
                    </div>

                    <div class="install-step">
                        <span class="install-step-number">2</span>
                        <div>
                            <div class="fw-semibold">Pilih "Pasang aplikasi" / "Tambahkan ke layar utama"</div>
                            <div class="small text-secondary">
                                Beberapa browser menyebutnya <span class="fw-semibold">Install app</span> atau
                                <span class="fw-semibold">Add to Home screen</span>.
                            </div>
                        </div>
                    </div>

                    <div class="install-step">
                        <span class="install-step-number">3</span>
                        <div>
                            <div class="fw-semibold">Konfirmasi pemasangan</div>
                            <div class="small text-secondary">Ketuk <span class="fw-semibold">Pasang</span> atau
                                <span class="fw-semibold">Tambah</span>, lalu ikon aplikasi akan muncul di layar
                                utama.
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info small d-flex align-items-start gap-2 mt-3 mb-0 py-2 px-3">
                        <i class="ti ti-info-circle fs-5 lh-1"></i>
                        <span>
                            Buka halaman ini melalui <span class="fw-semibold">HTTPS</span> dengan Chrome Android
                            agar opsi pemasangan otomatis muncul. Link ngrok sudah mendukung HTTPS.
                        </span>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-white" data-bs-dismiss="modal">Mengerti</button>
            </div>
        </div>
    </div>
</div>
