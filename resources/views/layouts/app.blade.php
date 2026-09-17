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
        <div class="mx-auto min-h-screen w-full max-w-lg pb-28">
            <header class="border-b border-stone-200 bg-white px-4 py-4 sm:px-6">
                <a href="{{ route('today') }}" class="text-base font-bold tracking-tight text-green-900">{{ config('app.name') }}</a>
            </header>

            <main class="px-4 py-6 sm:px-6">
                @yield('content')
            </main>
        </div>

        @unless (request()->routeIs('properties.create', 'properties.edit', 'clients.create', 'clients.edit', 'appointments.create', 'appointments.edit', 'clients.appointments.create', 'clients.deals.create', 'deals.edit'))
            <button type="button" data-quick-add-open aria-haspopup="dialog" aria-controls="quick-add-sheet" class="fixed bottom-[calc(5.5rem+env(safe-area-inset-bottom))] right-4 z-20 flex size-13 items-center justify-center rounded-full bg-green-900 text-3xl font-light leading-none text-white shadow-lg shadow-green-950/20" aria-label="เพิ่มรายการ">+</button>
        @endunless

        <nav aria-label="เมนูหลัก" class="fixed inset-x-0 bottom-0 z-10 border-t border-stone-200 bg-white px-2 pb-[max(0.5rem,env(safe-area-inset-bottom))] pt-2">
            <div class="mx-auto grid max-w-lg grid-cols-4 gap-1">
                <a href="{{ route('today') }}" @class(['flex min-h-11 items-center justify-center rounded-lg px-1 text-center text-sm font-medium', 'bg-green-50 text-green-900' => request()->routeIs('today'), 'text-stone-600' => ! request()->routeIs('today')])>วันนี้</a>
                <a href="{{ route('properties.index') }}" @class(['flex min-h-11 items-center justify-center rounded-lg px-1 text-center text-sm font-medium', 'bg-green-50 text-green-900' => request()->routeIs('properties.index'), 'text-stone-600' => ! request()->routeIs('properties.index')])>ทรัพย์</a>
                <a href="{{ route('clients.index') }}" @class(['flex min-h-11 items-center justify-center rounded-lg px-1 text-center text-sm font-medium', 'bg-green-50 text-green-900' => request()->routeIs('clients.index'), 'text-stone-600' => ! request()->routeIs('clients.index')])>ลูกค้า</a>
                <a href="{{ route('more') }}" @class(['flex min-h-11 items-center justify-center rounded-lg px-1 text-center text-sm font-medium', 'bg-green-50 text-green-900' => request()->routeIs('more'), 'text-stone-600' => ! request()->routeIs('more')])>เพิ่มเติม</a>
            </div>
        </nav>

        <dialog id="quick-add-sheet" aria-labelledby="quick-add-title" class="fixed inset-x-0 bottom-0 top-[auto] mx-auto mb-0 mt-auto w-full max-w-lg rounded-t-2xl border-0 bg-white p-0 text-stone-950 shadow-2xl backdrop:bg-stone-950/30">
            <section class="px-4 pb-[max(1.5rem,env(safe-area-inset-bottom))] pt-3">
                <div class="mx-auto h-1 w-10 rounded-full bg-stone-300"></div>
                <div class="mt-5 flex items-center justify-between gap-4"><h2 id="quick-add-title" class="text-lg font-semibold">เพิ่มรายการ</h2><button type="button" data-quick-add-close class="min-h-11 px-2 text-sm font-medium text-stone-600">ปิด</button></div>
                <p class="mt-1 text-sm text-stone-600">เลือกสิ่งที่ต้องการเริ่มต้น</p>
                <div class="mt-4 divide-y divide-stone-100 rounded-xl border border-stone-200">
                    @foreach (['เพิ่มทรัพย์', 'เพิ่มลูกค้า', 'นัดดู', 'ติดตาม'] as $action)
                        @if ($action === 'เพิ่มทรัพย์')
                            <a href="{{ route('properties.create') }}" class="flex min-h-13 items-center px-4 text-base font-medium text-stone-700">{{ $action }}</a>
                        @elseif ($action === 'เพิ่มลูกค้า')
                            <a href="{{ route('clients.create') }}" class="flex min-h-13 items-center px-4 text-base font-medium text-stone-700">{{ $action }}</a>
                        @elseif ($action === 'นัดดู')
                            <a href="{{ route('appointments.create') }}" class="flex min-h-13 items-center px-4 text-base font-medium text-stone-700">{{ $action }}</a>
                        @elseif ($action === 'ติดตาม')
                            <a href="{{ route('followups.create') }}" class="flex min-h-13 items-center px-4 text-base font-medium text-stone-700">{{ $action }}</a>
                        @else
                            <span class="flex min-h-13 items-center px-4 text-base font-medium text-stone-700">{{ $action }}</span>
                        @endif
                    @endforeach
                </div>
                <p class="mt-3 text-sm text-stone-500">เริ่มจากข้อมูลหลักก่อน แล้วเติมรายละเอียดภายหลังได้</p>
            </section>
        </dialog>
    </body>
</html>
