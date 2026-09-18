@extends('layouts.app')

@section('title', 'จัดการผู้ใช้งาน | AgencySuit Admin')

@section('content')
    <div class="as-page-head">
        <div class="flex items-center gap-3">
            <x-back-button fallback="{{ route('more') }}" />
            <div>
                <h1 class="as-page-title">จัดการผู้ใช้งาน</h1>
                <p class="as-page-subtitle">ทั้งหมด {{ number_format($counts['total']) }} บัญชี (Free: {{ $counts['free'] }}, Pro: {{ $counts['pro'] }})</p>
            </div>
        </div>
    </div>

    {{-- Admin Navigation Tabs --}}
    <div class="mb-4 flex gap-2 border-b border-stone-200 pb-2">
        <a href="{{ route('admin.users.index') }}" class="as-pill is-active flex items-center gap-1.5">
            <x-icon name="users" size="16" />
            <span>ผู้ใช้งาน ({{ $counts['total'] }})</span>
        </a>
        <a href="{{ route('admin.feedback.index') }}" class="as-pill flex items-center gap-1.5">
            <x-icon name="message" size="16" />
            <span>ข้อเสนอแนะ</span>
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Search & Filter --}}
    <div class="mb-4 space-y-2">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-2">
            <input
                type="search"
                name="q"
                value="{{ request('q') }}"
                placeholder="ค้นหาชื่อ หรืออีเมล..."
                class="as-input flex-1"
            />
            @if (request('plan'))
                <input type="hidden" name="plan" value="{{ request('plan') }}" />
            @endif
            <button type="submit" class="as-action-secondary px-4 py-2">ค้นหา</button>
        </form>

        <div class="flex flex-wrap gap-1.5 pt-1">
            <a href="{{ route('admin.users.index', array_filter(['q' => request('q')])) }}"
               @class(['as-filter-chip', 'is-active' => ! request('plan')])>
                ทั้งหมด ({{ $counts['total'] }})
            </a>
            <a href="{{ route('admin.users.index', array_filter(['plan' => 'free', 'q' => request('q')])) }}"
               @class(['as-filter-chip', 'is-active' => request('plan') === 'free'])>
                FREE ({{ $counts['free'] }})
            </a>
            <a href="{{ route('admin.users.index', array_filter(['plan' => 'pro', 'q' => request('q')])) }}"
               @class(['as-filter-chip', 'is-active' => request('plan') === 'pro'])>
                PRO ({{ $counts['pro'] }})
            </a>
        </div>
    </div>

    {{-- User List Cards --}}
    @if ($users->isEmpty())
        <div class="rounded-2xl border border-dashed border-stone-300 bg-white p-6 text-center text-stone-500">
            <p>ไม่พบบัญชีผู้ใช้ตามเงื่อนไขที่ค้นหา</p>
            @if (request('q') || request('plan'))
                <a href="{{ route('admin.users.index') }}" class="as-action-secondary mt-3 inline-block text-xs">ล้างตัวกรอง</a>
            @endif
        </div>
    @else
        <div class="space-y-3">
            @foreach ($users as $user)
                <div class="as-card space-y-2.5 p-3.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="truncate font-semibold text-stone-900">{{ $user->name }}</span>
                                @if ($user->is_admin)
                                    <span class="rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold text-amber-900">ADMIN</span>
                                @endif
                            </div>
                            <div class="truncate text-xs text-stone-500">{{ $user->email }}</div>
                            <div class="text-[11px] text-stone-400">สมัครเมื่อ {{ $user->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div>
                            @if ($user->plan === 'pro')
                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">PRO</span>
                            @else
                                <span class="rounded-full bg-stone-100 px-2.5 py-0.5 text-xs font-semibold text-stone-700">FREE</span>
                            @endif
                        </div>
                    </div>

                    {{-- Activity Summary --}}
                    <div class="grid grid-cols-3 gap-2 rounded-xl bg-stone-50 p-2 text-center text-xs">
                        <div>
                            <span class="text-stone-400 block text-[10px]">ทรัพย์</span>
                            <span class="font-bold text-stone-700">{{ $user->properties_count }}</span>
                        </div>
                        <div>
                            <span class="text-stone-400 block text-[10px]">ลูกค้า</span>
                            <span class="font-bold text-stone-700">{{ $user->clients_count }}</span>
                        </div>
                        <div>
                            <span class="text-stone-400 block text-[10px]">ดีล</span>
                            <span class="font-bold text-stone-700">{{ $user->deals_count }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end border-t border-stone-100 pt-2">
                        <form method="POST" action="{{ route('admin.users.update-plan', $user) }}">
                            @csrf
                            @method('PATCH')
                            @if ($user->plan === 'pro')
                                <input type="hidden" name="plan" value="free" />
                                <button type="submit" class="as-action-secondary px-3 py-1.5 text-xs text-stone-700" onclick="return confirm('ต้องการปรับแผนของ {{ $user->name }} เป็น FREE หรือไม่?')">
                                    ปรับเป็น FREE
                                </button>
                            @else
                                <input type="hidden" name="plan" value="pro" />
                                <button type="submit" class="as-action-primary px-3 py-1.5 text-xs" onclick="return confirm('ต้องการอัปเกรดแผนของ {{ $user->name }} เป็น PRO หรือไม่?')">
                                    อัปเกรดเป็น PRO
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    @endif
@endsection
