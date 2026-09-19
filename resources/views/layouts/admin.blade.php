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
    <body class="bg-[var(--as-canvas)] text-[var(--as-ink)] min-h-screen">
        <div class="max-w-2xl mx-auto min-h-screen pb-12">
            {{-- Admin Dedicated Topbar --}}
            <header class="flex items-center justify-between px-4 py-3.5 border-b border-[var(--as-line)] bg-[var(--as-topbar-bg)] backdrop-blur-md sticky top-0 z-20">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 font-bold text-sm text-[var(--as-ink)]">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[var(--as-teal)] text-[var(--as-cta-ink)] font-extrabold text-xs">AS</span>
                    <span>AgencySuit <span class="text-xs px-1.5 py-0.5 rounded bg-[var(--as-teal-soft)] text-[var(--as-teal)] font-bold">Admin</span></span>
                </a>

                <div class="flex items-center gap-3">
                    <span class="hidden sm:inline text-xs text-[var(--as-muted)] truncate max-w-[180px]">{{ auth()->user()->email }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1.5 rounded-lg border border-[var(--as-line)] bg-[var(--as-surface)] text-[var(--as-muted)] hover:text-[var(--as-coral)] hover:border-[var(--as-coral)] transition-colors">
                            <x-icon name="arrow-left" size="14" />
                            <span>ออกจากระบบ</span>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Admin Navigation Tabs --}}
            <nav class="flex gap-2 px-4 pt-3 pb-2 border-b border-[var(--as-line)] bg-[var(--as-surface)]/50">
                <a href="{{ route('admin.users.index') }}" @class([
                    'inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold transition-all',
                    'bg-[var(--as-teal)] text-[var(--as-cta-ink)]' => request()->routeIs('admin.users.*'),
                    'border border-[var(--as-line)] text-[var(--as-muted)] hover:text-[var(--as-ink)] hover:bg-[var(--as-surface-raised)]' => ! request()->routeIs('admin.users.*'),
                ])>
                    <x-icon name="users" size="14" />
                    <span>ผู้ใช้งาน</span>
                </a>
                <a href="{{ route('admin.feedback.index') }}" @class([
                    'inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold transition-all',
                    'bg-[var(--as-teal)] text-[var(--as-cta-ink)]' => request()->routeIs('admin.feedback.*'),
                    'border border-[var(--as-line)] text-[var(--as-muted)] hover:text-[var(--as-ink)] hover:bg-[var(--as-surface-raised)]' => ! request()->routeIs('admin.feedback.*'),
                ])>
                    <x-icon name="message" size="14" />
                    <span>ข้อเสนอแนะ</span>
                </a>
            </nav>

            {{-- Main Content --}}
            <main class="px-4 pt-5">
                @if (session('success'))
                    <div role="alert" class="mb-4 rounded-xl border border-emerald-500/30 bg-emerald-500/15 p-3 text-sm text-emerald-300">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div role="alert" class="as-alert as-alert--error mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </body>
</html>
