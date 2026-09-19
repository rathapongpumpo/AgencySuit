<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#171427">
        <script>
            (function() {
                var mode = localStorage.getItem('as_theme') || 'system';
                document.documentElement.setAttribute('data-theme', mode);
                var isDark = mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                if (isDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                var meta = document.querySelector('meta[name="theme-color"]');
                if (meta) meta.setAttribute('content', isDark ? '#171427' : '#f6f5fb');
            })();
        </script>
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
    <body>
        <div class="as-shell">
            <header class="as-topbar">
                <a href="{{ route('today') }}" class="as-brand">
                    <span class="as-brand-mark">AS</span>
                    <span class="as-brand-name">AgencySuit</span>
                </a>
                <span class="as-topbar-meta">งานของคุณ</span>
            </header>

            <main class="as-main">
                @if (session('error'))
                    <div role="alert" class="as-alert as-alert--error mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>

        @unless (request()->routeIs('properties.create', 'properties.edit', 'clients.create', 'clients.edit', 'appointments.create', 'appointments.edit', 'clients.appointments.create', 'clients.deals.create', 'deals.edit'))
            <button type="button" data-quick-add-open aria-haspopup="dialog" aria-controls="quick-add-sheet" class="as-fab" aria-label="เพิ่มรายการ">
                <x-icon name="plus" size="26" />
            </button>
        @endunless

        <nav aria-label="เมนูหลัก" class="as-bottom-nav">
            <div class="as-bottom-nav-inner">
                <a href="{{ route('today') }}" @class(['as-nav-item is-active' => request()->routeIs('today'), 'as-nav-item' => ! request()->routeIs('today')]) @if(request()->routeIs('today')) aria-current="page" @endif>
                    <x-icon name="home" class="as-nav-icon" />
                    <span>วันนี้</span>
                </a>
                <a href="{{ route('properties.index') }}" @class(['as-nav-item is-active' => request()->routeIs('properties.*'), 'as-nav-item' => ! request()->routeIs('properties.*')]) @if(request()->routeIs('properties.*')) aria-current="page" @endif>
                    <x-icon name="building" class="as-nav-icon" />
                    <span>ทรัพย์</span>
                </a>
                <a href="{{ route('clients.index') }}" @class(['as-nav-item is-active' => request()->routeIs('clients.*'), 'as-nav-item' => ! request()->routeIs('clients.*')]) @if(request()->routeIs('clients.*')) aria-current="page" @endif>
                    <x-icon name="users" class="as-nav-icon" />
                    <span>ลูกค้า</span>
                </a>
                <a href="{{ route('more') }}" @class(['as-nav-item is-active' => request()->routeIs('more', 'upgrade', 'feedback.*'), 'as-nav-item' => ! request()->routeIs('more', 'upgrade', 'feedback.*')]) @if(request()->routeIs('more', 'upgrade', 'feedback.*')) aria-current="page" @endif>
                    <x-icon name="more" class="as-nav-icon" />
                    <span>เพิ่มเติม</span>
                </a>
            </div>
        </nav>

        <dialog id="quick-add-sheet" aria-labelledby="quick-add-title" class="as-sheet fixed inset-x-0 bottom-0 top-[auto] mx-auto mb-0 mt-auto p-0">
            <section class="px-4 pb-[max(1.5rem,env(safe-area-inset-bottom))] pt-3">
                <div class="as-sheet-handle"></div>
                <div class="as-sheet-title-row">
                    <h2 id="quick-add-title" class="as-sheet-title">เพิ่มรายการ</h2>
                    <button type="button" data-quick-add-close class="as-sheet-close">ปิด</button>
                </div>
                <p class="as-page-subtitle mt-1">เลือกสิ่งที่ต้องการเริ่มต้น</p>
                <div class="mt-4 divide-y divide-stone-100">
                    <a href="{{ route('properties.create') }}" class="as-sheet-link"><span class="as-icon-box"><x-icon name="building" size="18" /></span>เพิ่มทรัพย์<x-icon name="chevron-right" size="18" class="ml-auto text-stone-400" /></a>
                    <a href="{{ route('clients.create') }}" class="as-sheet-link"><span class="as-icon-box"><x-icon name="users" size="18" /></span>เพิ่มลูกค้า<x-icon name="chevron-right" size="18" class="ml-auto text-stone-400" /></a>
                    <a href="{{ route('appointments.create') }}" class="as-sheet-link"><span class="as-icon-box"><x-icon name="calendar" size="18" /></span>นัดดู<x-icon name="chevron-right" size="18" class="ml-auto text-stone-400" /></a>
                    <a href="{{ route('followups.create') }}" class="as-sheet-link"><span class="as-icon-box"><x-icon name="clock" size="18" /></span>ติดตาม<x-icon name="chevron-right" size="18" class="ml-auto text-stone-400" /></a>
                </div>
                <p class="mt-3 text-sm text-stone-500">เริ่มจากข้อมูลหลักก่อน แล้วเติมรายละเอียดภายหลังได้</p>
            </section>
        </dialog>
    </body>
</html>
