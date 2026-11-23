<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body>
    @yield('content')

    @include('partials.scripts')
    <script src="{{ asset('assets/js/pwa-installer.js') }}" defer></script>
</body>
</html>
