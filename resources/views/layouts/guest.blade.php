<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#14532d">
        <title>@yield('title', config('app.name'))</title>
        @unless (app()->environment('testing'))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endunless
    </head>
    <body class="min-h-screen bg-stone-50 text-stone-950">
        <main class="mx-auto flex min-h-screen w-full max-w-lg flex-col px-5 py-8 sm:px-8">
            @yield('content')
        </main>
    </body>
</html>
