@extends('layouts.admin')

@section('title', 'จัดการผู้ใช้งาน | AgencySuit Admin')

@section('content')
    <div class="as-page-head">
        <div>
            <h1 class="as-page-title">ผู้ใช้งานในระบบ</h1>
            <p class="as-page-subtitle">ทั้งหมด {{ number_format($counts['total']) }} บัญชี (Free: {{ $counts['free'] }}, Pro: {{ $counts['pro'] }})</p>
        </div>
    </div>

    {{-- Search and Filter Form --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="mt-4 space-y-2.5">
        <div class="relative">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="ค้นหาชื่อ หรืออีเมลผู้ใช้..." class="as-input pl-10 pr-20 text-sm">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-stone-400 pointer-events-none">
                <x-icon name="search" size="16" />
            </span>
            @if (request('q') || request('plan'))
                <a href="{{ route('admin.users.index') }}" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-xs font-semibold text-stone-400 hover:text-stone-700">ล้างค้นหา</a>
            @endif
        </div>

        {{-- Filter Chips --}}
        <div class="flex gap-2 overflow-x-auto pb-1">
            <a href="{{ route('admin.users.index', array_filter(['q' => request('q')])) }}"
                @class(['as-chip is-active' => ! request('plan'), 'as-chip' => request('plan')])>
                ทั้งหมด ({{ $counts['total'] }})
            </a>
            <a href="{{ route('admin.users.index', array_filter(['plan' => 'free', 'q' => request('q')])) }}"
                @class(['as-chip is-active' => request('plan') === 'free', 'as-chip' => request('plan') !== 'free'])>
                FREE ({{ $counts['free'] }})
            </a>
            <a href="{{ route('admin.users.index', array_filter(['plan' => 'pro', 'q' => request('q')])) }}"
                @class(['as-chip is-active' => request('plan') === 'pro', 'as-chip' => request('plan') !== 'pro'])>
                PRO ({{ $counts['pro'] }})
            </a>
        </div>
    </form>

    {{-- User List Surface --}}
    @if ($users->isEmpty())
        <div class="mt-5 as-surface overflow-hidden">
            <x-empty-state title="ไม่พบบัญชีผู้ใช้" description="ลองเปลี่ยนคำค้นหา หรือกดล้างตัวกรองเพื่อดูผู้ใช้ทั้งหมด" action="ดูผู้ใช้ทั้งหมด" :action-url="route('admin.users.index')" />
        </div>
    @else
        <ul class="as-list-surface mt-4">
            @foreach ($users as $user)
                <li class="as-list-row items-center justify-between gap-3">
                    <span class="as-icon-box shrink-0">
                        <x-icon name="users" size="20" />
                    </span>
                    <div class="as-list-copy min-w-0">
                        <div class="as-list-title-line">
                            <span class="as-list-title">{{ $user->name }}</span>
                            <div class="flex items-center gap-1.5 shrink-0">
                                @if ($user->is_admin)
                                    <span class="rounded-full px-2 py-0.5 text-xs font-bold bg-amber-500/15 text-amber-700 dark:text-amber-400 border border-amber-500/30">ADMIN</span>
                                @endif
                                <span @class([
                                    'rounded-full px-2 py-0.5 text-xs font-bold',
                                    'bg-[var(--as-teal-soft)] text-[var(--as-teal)]' => $user->plan === 'pro',
                                    'bg-[var(--as-surface-raised)] text-[var(--as-muted)] border border-[var(--as-line)]' => $user->plan !== 'pro',
                                ])>
                                    {{ strtoupper($user->plan) }}
                                </span>
                            </div>
                        </div>
                        <div class="as-list-meta truncate">{{ $user->email }}</div>
                        <div class="as-list-meta text-xs">
                            ทรัพย์ <strong>{{ $user->properties_count }}</strong> · ลูกค้า <strong>{{ $user->clients_count }}</strong> · ดีล <strong>{{ $user->deals_count }}</strong>
                            <span class="mx-1 text-[var(--as-line-strong)]">·</span>
                            {{ $user->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.users.update-plan', $user) }}" class="shrink-0">
                        @csrf
                        @method('PATCH')
                        @if ($user->plan === 'pro')
                            <input type="hidden" name="plan" value="free" />
                            <button type="submit" class="as-row-control !mt-0 text-xs" title="ปรับเป็น Free">
                                ปรับเป็น Free
                            </button>
                        @else
                            <input type="hidden" name="plan" value="pro" />
                            <button type="submit" class="as-row-control !mt-0 text-xs !border-[var(--as-teal)] !text-[var(--as-teal)]" title="อัปเกรด Pro">
                                อัปเป็น Pro
                            </button>
                        @endif
                    </form>
                </li>
            @endforeach
        </ul>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    @endif
@endsection
