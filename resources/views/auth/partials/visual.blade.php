{{-- Panel visual kiri untuk halaman login & register.
     Variabel: $visual (icon, eyebrow, headline, lead, points, footnote). --}}
<aside class="auth-aside">
    <div class="auth-aside-bg" aria-hidden="true">
        <span class="auth-gradient-orb auth-gradient-orb--top"></span>
        <span class="auth-gradient-orb auth-gradient-orb--bottom"></span>
        <span class="auth-grid-pattern"></span>
        <span class="auth-mesh-gradient"></span>
    </div>

    <a class="auth-brand" href="{{ url('/') }}" aria-label="Saku Kas">
        <span class="auth-brand-plaque">
            <img src="{{ asset('assets/images/favicon .png') }}" alt="Saku Kas" width="640"
                height="531">
        </span>
    </a>

    <div class="auth-aside-body">
        <div class="auth-aside-copy">
            <span class="auth-eyebrow">
                <i class="ti {{ $visual['icon'] }}"></i>
                {{ $visual['eyebrow'] }}
            </span>
            <h2 class="auth-headline">{!! $visual['headline'] !!}</h2>
            <p class="auth-lead">{{ $visual['lead'] }}</p>
        </div>

        <div class="auth-stage">
            <span class="auth-stage-glow" aria-hidden="true"></span>
            <span class="auth-stage-ring" aria-hidden="true"></span>
            <span class="auth-stage-accent" aria-hidden="true"></span>
            <img class="auth-art" src="{{ asset('assets/images/login-illustration.svg') }}"
                alt="Ilustrasi aplikasi pencatatan keuangan dengan saldo, grafik arus kas, dan daftar transaksi">
        </div>

        <div class="auth-stats" aria-label="Statistik aplikasi">
            <div class="auth-stat">
                <span class="auth-stat-value">50K+</span>
                <span class="auth-stat-label">Pengguna Aktif</span>
            </div>
            <div class="auth-stat-divider" aria-hidden="true"></div>
            <div class="auth-stat">
                <span class="auth-stat-value">2.1M</span>
                <span class="auth-stat-label">Transaksi Dicatat</span>
            </div>
            <div class="auth-stat-divider" aria-hidden="true"></div>
            <div class="auth-stat">
                <span class="auth-stat-value">4.9★</span>
                <span class="auth-stat-label">Rating App Store</span>
            </div>
        </div>

        <div class="auth-badge" aria-label="Penghargaan">
            <i class="ti ti-award"></i>
            <span>Dipersembahkan untuk UMKM Indonesia</span>
        </div>

        <ul class="auth-points">
            @foreach ($visual['points'] as $point)
                <li>
                    <span class="auth-point-icon" aria-hidden="true"><i class="ti {{ $point['icon'] }}"></i></span>
                    <span>{{ $point['text'] }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="auth-aside-foot">
        <i class="ti ti-shield-lock"></i>
        <span>{{ $visual['footnote'] }}</span>
    </div>

    <div class="auth-aside-decor" aria-hidden="true"></div>
</aside>
