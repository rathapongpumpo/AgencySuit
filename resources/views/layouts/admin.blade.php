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
        <title>@yield('title', 'Admin | AgencySuit')</title>
        @unless (app()->environment('testing'))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endunless
    </head>
    <body>
        <div class="as-shell">
            {{-- Standard App Topbar --}}
            <header class="as-topbar">
                <a href="{{ route('admin.users.index') }}" class="as-brand">
                    <span class="as-brand-mark">AS</span>
                    <span class="as-brand-name">AgencySuit Admin</span>
                </a>
                <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="as-row-control !mt-0 !min-h-[2rem] text-xs hover:!text-[var(--as-coral)] hover:!border-[var(--as-coral)]">
                        <x-icon name="arrow-left" size="13" />
                        <span>ออกจากระบบ</span>
                    </button>
                </form>
            </header>

            {{-- Clean Admin Segmented Tab Switcher --}}
            <div class="px-4 pt-3 pb-1">
                <div class="as-choice-grid !grid-cols-2 !mt-0">
                    <a href="{{ route('admin.users.index') }}" @class([
                        'as-choice !min-h-[2.4rem] text-xs font-bold no-underline',
                        '!border-[var(--as-teal)] !bg-[var(--as-teal-soft)] !text-[var(--as-teal)] font-extrabold' => request()->routeIs('admin.users.*'),
                    ])>
                        <x-icon name="users" size="15" class="mr-1.5 inline" />
                        <span>ผู้ใช้งาน</span>
                    </a>
                    <a href="{{ route('admin.feedback.index') }}" @class([
                        'as-choice !min-h-[2.4rem] text-xs font-bold no-underline',
                        '!border-[var(--as-teal)] !bg-[var(--as-teal-soft)] !text-[var(--as-teal)] font-extrabold' => request()->routeIs('admin.feedback.*'),
                    ])>
                        <x-icon name="message" size="15" class="mr-1.5 inline" />
                        <span>ข้อเสนอแนะ</span>
                    </a>
                </div>
            </div>

            <main class="as-main !pt-2">
                @if (session('success'))
                    <p role="status" class="as-alert as-alert--success mb-4">{{ session('success') }}</p>
                @endif
                @if (session('error'))
                    <div role="alert" class="as-alert as-alert--error mb-4">{{ session('error') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </body>
</html>
