<!-- Libs JS -->
<script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}?v={{ filemtime(public_path('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js')) }}"></script>
<script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}?v={{ filemtime(public_path('assets/libs/simplebar/dist/simplebar.min.js')) }}"></script>

<!-- Helper warna chart agar ikut tema aktif (dipakai halaman yang punya chart) -->
<script>
    window.themePalette = function () {
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';

        return {
            isDark: isDark,
            gridColor: isDark ? 'rgba(145, 158, 171, 0.28)' : '#e9ecef',
            labelColor: isDark ? '#919eab' : '#637381',
            titleColor: isDark ? '#f9fafb' : '#1c252e',
            tooltipTheme: isDark ? 'dark' : 'light',
        };
    };
</script>

<!-- Theme JS -->
<script src="{{ asset('assets/js/main.js') }}?v={{ filemtime(public_path('assets/js/main.js')) }}"></script>
<script src="{{ asset('assets/js/live-summary.js') }}" defer></script>

{{-- Script khusus per halaman: @push('scripts') ... @endpush --}}
@stack('scripts')
