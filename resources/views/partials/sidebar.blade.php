<div id="miniSidebar">
    <div class="brand-logo">
        <a class="d-none d-md-flex align-items-center gap-2" href="{{ url('/') }}">
            <img src="{{ asset('assets/images/brand/logo/saku-kas-icon.png') }}" alt="Saku Kas"
                class="brand-logo-icon" />
            <span class="fw-bold fs-4 site-logo-text">Saku <span class="brand-name-accent">Kas</span></span>
        </a>
    </div>

    @include('partials.sidebar-nav')
</div>

{{-- Sidebar versi mobile --}}
<div class="offcanvasNav offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample"
    aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <a class="d-flex align-items-center gap-2" href="{{ url('/') }}">
            <img src="{{ asset('assets/images/brand/logo/saku-kas-icon.png') }}" alt="Saku Kas"
                class="brand-logo-icon" />
            <span class="fw-bold fs-4 site-logo-text">Saku <span class="brand-name-accent">Kas</span></span>
        </a>

        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        @include('partials.sidebar-nav')
    </div>
</div>
