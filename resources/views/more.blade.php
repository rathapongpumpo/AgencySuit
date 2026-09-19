@extends('layouts.app')

@section('title', 'เพิ่มเติม | AgencySuit')

@section('content')
    <div class="as-page-head">
        <div>
            <h1 class="as-page-title">เพิ่มเติม</h1>
            <p class="as-page-subtitle">บัญชีและการใช้งานของคุณ</p>
        </div>
    </div>

    <section class="as-setting-list">
        <div class="as-setting-row">
            <span class="as-icon-box"><x-icon name="users" size="19" /></span>
            <span class="min-w-0 flex-1">
                <span class="as-setting-label block">บัญชีผู้ใช้</span>
                <span class="as-setting-meta block truncate">{{ auth()->user()->email }}</span>
            </span>
        </div>
        @if (auth()->user()->isAdmin())
            <a href="{{ route('admin.users.index') }}" class="as-setting-row bg-amber-50/50">
                <span class="as-icon-box bg-amber-100 text-amber-800"><x-icon name="shield" size="19" /></span>
                <span class="min-w-0 flex-1">
                    <span class="as-setting-label block font-semibold text-amber-900">จัดการระบบ (Admin)</span>
                    <span class="as-setting-meta block text-amber-700">จัดการผู้ใช้งานและข้อเสนอแนะ</span>
                </span>
                <x-icon name="chevron-right" size="17" class="text-amber-500" />
            </a>
        @endif
        <a href="{{ route('deals.index') }}" class="as-setting-row">
            <span class="as-icon-box"><x-icon name="briefcase" size="19" /></span>
            <span class="min-w-0 flex-1"><span class="as-setting-label block">ภาพรวมดีลและคอมมิชชัน</span><span class="as-setting-meta block">ติดตามสถานะและรายได้สะสม</span></span>
            <x-icon name="chevron-right" size="17" class="as-muted" />
        </a>
        <a href="{{ route('upgrade') }}" class="as-setting-row">
            <span class="as-icon-box"><x-icon name="building" size="19" /></span>
            <span class="min-w-0 flex-1"><span class="as-setting-label block">แผนการใช้งาน</span><span class="as-setting-meta block">ดูสิทธิ์และทางเลือกเพิ่มเติม</span></span>
            <span class="as-setting-value">{{ strtoupper(auth()->user()->plan) }} <x-icon name="chevron-right" size="17" class="ml-1 inline" /></span>
        </a>
        <a href="{{ route('feedback.create') }}" class="as-setting-row">
            <span class="as-icon-box"><x-icon name="check" size="19" /></span>
            <span class="min-w-0 flex-1"><span class="as-setting-label block">ส่งความคิดเห็น</span><span class="as-setting-meta block">บอกสิ่งที่ควรทำให้ง่ายขึ้น</span></span>
            <x-icon name="chevron-right" size="17" class="as-muted" />
        </a>
        <form method="POST" action="{{ route('logout') }}" class="border-t border-[var(--as-line)] px-4 py-2">
            @csrf
            <button type="submit" class="min-h-11 text-sm font-bold text-red-700">ออกจากระบบ</button>
        </form>
    </section>

    <div class="mt-6">
        <h2 class="text-xs font-bold uppercase tracking-wider text-[var(--as-muted)] mb-2.5 px-1">ธีมการแสดงผล</h2>
        <div class="as-surface p-3 grid grid-cols-2 gap-2.5">
            <button type="button" data-set-theme="navy" class="as-theme-card" aria-pressed="false">
                <div class="as-theme-preview" style="background: #f6f8fb; border-color: #cbd5e1;">
                    <div class="flex items-center gap-1.5 mb-2">
                        <span class="w-4 h-4 rounded-md shadow-xs" style="background: #0f172a;"></span>
                        <span class="w-2.5 h-2.5 rounded-full" style="background: #c27803;"></span>
                    </div>
                    <div class="h-2 w-16 rounded bg-slate-200"></div>
                </div>
                <div class="as-theme-info">
                    <span class="as-theme-title">Midnight Navy</span>
                    <span class="as-theme-desc">คมชัด สไตล์ Linear</span>
                </div>
                <span class="as-theme-check" aria-hidden="true">
                    <x-icon name="check" size="13" />
                </span>
            </button>

            <button type="button" data-set-theme="pine" class="as-theme-card" aria-pressed="false">
                <div class="as-theme-preview" style="background: #f8f7f4; border-color: #d1dad4;">
                    <div class="flex items-center gap-1.5 mb-2">
                        <span class="w-4 h-4 rounded-md shadow-xs" style="background: #1a2e26;"></span>
                        <span class="w-2.5 h-2.5 rounded-full" style="background: #c86d51;"></span>
                    </div>
                    <div class="h-2 w-16 rounded bg-stone-200"></div>
                </div>
                <div class="as-theme-info">
                    <span class="as-theme-title">Deep Pine</span>
                    <span class="as-theme-desc">อบอุ่น สไตล์สถาปัตย์</span>
                </div>
                <span class="as-theme-check" aria-hidden="true">
                    <x-icon name="check" size="13" />
                </span>
            </button>
        </div>
    </div>
@endsection
