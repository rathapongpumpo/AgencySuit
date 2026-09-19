<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#08090a">
        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
        <title>@yield('title', config('app.name'))</title>
        @unless (app()->environment('testing'))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endunless
    </head>
    <body class="as-auth-body">
        <div class="as-auth-wrapper">
            <header class="as-auth-topbar">
                <a href="{{ url('/') }}" class="as-auth-brand" aria-label="AgencySuit หน้าหลัก">
                    <span class="as-brand-mark">AS</span>
                    <span class="as-brand-name">AgencySuit</span>
                </a>
            </header>
            <main class="as-auth-main">
                @yield('content')
            </main>
        </div>
    </body>
</html>
