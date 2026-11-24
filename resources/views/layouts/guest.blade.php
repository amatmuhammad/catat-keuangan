<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body>
    @yield('content')

    @include('components.install-app-button', ['variant' => 'guest'])

    @include('partials.scripts')
    <script src="{{ asset('assets/js/pwa-installer.js') }}?v={{ filemtime(public_path('assets/js/pwa-installer.js')) }}" defer></script>
</body>
</html>
