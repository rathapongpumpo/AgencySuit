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
        <div class="mx-auto min-h-screen w-full max-w-lg pb-24">
            <header class="border-b border-stone-200 bg-white px-5 py-4 sm:px-6">
                <a href="/" class="text-lg font-bold tracking-tight text-green-900">{{ config('app.name') }}</a>
            </header>

            <main class="px-5 py-6 sm:px-6">
                @yield('content')
            </main>
        </div>

        <nav aria-label="เมนูหลัก" class="fixed inset-x-0 bottom-0 z-10 border-t border-stone-200 bg-white/95 px-2 pb-[max(0.5rem,env(safe-area-inset-bottom))] pt-2 backdrop-blur">
            <div class="mx-auto grid max-w-lg grid-cols-4 gap-1">
                @foreach (['วันนี้', 'ทรัพย์', 'ลูกค้า', 'เพิ่มเติม'] as $item)
                    <span class="flex min-h-11 items-center justify-center rounded-xl px-1 text-center text-sm font-medium text-stone-600">{{ $item }}</span>
                @endforeach
            </div>
        </nav>
    </body>
</html>
