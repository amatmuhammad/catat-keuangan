<div id="pwaInstallBanner" class="install-banner d-none" role="dialog" aria-live="polite"
    aria-labelledby="pwaInstallTitle">
    <img src="{{ asset('icons/icon-192x192.png') }}" alt="" class="install-logo">

    <div class="flex-grow-1 min-w-0">
        <div class="fw-semibold" id="pwaInstallTitle">Pasang Catat Keuangan</div>
        <div class="small text-secondary" id="pwaInstallText">
            Buka lebih cepat dari layar utama, layar penuh seperti aplikasi.
        </div>
        <div class="small text-secondary d-none" id="iosInstallHint">
            Ketuk ikon <span class="fw-semibold">Bagikan</span> di Safari, lalu pilih
            <span class="fw-semibold">Tambah ke Layar Utama</span>.
        </div>
        <div class="d-flex gap-2 mt-2">
            <button type="button" class="btn btn-primary btn-sm" id="pwaInstallBtn">Pasang Aplikasi</button>
            <button type="button" class="btn btn-white btn-sm" id="pwaDismissBtn">Nanti saja</button>
        </div>
    </div>

    <button type="button" class="btn-close ms-1" id="pwaCloseBtn" aria-label="Tutup"></button>
</div>
