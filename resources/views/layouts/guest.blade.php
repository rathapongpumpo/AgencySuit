<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#f4f2ed">
        <title>@yield('title', config('app.name'))</title>
        @unless (app()->environment('testing'))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endunless
    </head>
    <body>
        <main class="as-guest-shell">
            <a href="{{ url('/') }}" class="as-guest-brand">
                <span class="as-brand-mark">AS</span>
                <span>AgencySuit</span>
            </a>
            @yield('content')
        </main>
    </body>
</html>
