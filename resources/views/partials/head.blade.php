<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta content="Codescandy" name="author">

<title>@yield('title', 'Dashboard') | {{ config('app.name', 'Dasher') }}</title>

<!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon .png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon .png') }}" />
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/images/favicon .png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/icon-192x192.png') }}" />
    <meta name="theme-color" content="#1a6be0" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0b1a33" media="(prefers-color-scheme: dark)">
    <meta name="msapplication-TileColor" content="#1a6be0">
    <meta name="msapplication-TileImage" content="{{ asset('assets/images/favicon/ms-icon-144x144.png') }}" />
<meta name="mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
<meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'Catat Keuangan') }}" />
@if (config('webpush.vapid.public_key'))
    <meta name="webpush-public-key" content="{{ config('webpush.vapid.public_key') }}">
@endif

<!-- Color modes (harus sebelum CSS supaya tidak berkedip) -->
<script src="{{ asset('assets/js/vendors/color-modes.js') }}"></script>
<script>
  if (localStorage.getItem('sidebarExpanded') === 'false') {
    document.documentElement.classList.add('collapsed');
    document.documentElement.classList.remove('expanded');
  } else {
    document.documentElement.classList.remove('collapsed');
    document.documentElement.classList.add('expanded');
  }
</script>

<!-- Libs CSS -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&display=swap" />
<link rel="stylesheet" href="{{ asset('assets/libs/simplebar/dist/simplebar.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/libs/@tabler/icons-webfont/tabler-icons.min.css') }}" />

<!-- PWA -->
<link rel="manifest" href="{{ asset('manifest.json') }}" />
<link rel="apple-touch-icon" sizes="192x192" href="{{ asset('icons/icon-192x192.png') }}" />

<!-- Theme CSS (cache-busted agar update langsung ter pakai) -->
<link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}?v={{ filemtime(public_path('assets/css/theme.css')) }}" />
<link rel="stylesheet" href="{{ asset('assets/css/cashflow.css') }}?v={{ filemtime(public_path('assets/css/cashflow.css')) }}" />

@stack('styles')
