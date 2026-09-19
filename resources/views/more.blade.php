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
        <div class="border-t border-[var(--as-line)] p-4">
            <div class="flex items-center justify-between mb-2.5">
                <span class="as-setting-label block">ธีมการแสดงผล</span>
                <span class="as-setting-meta block">ปรับตามความสบายตา</span>
            </div>
            <div class="as-segmented-control" role="group" aria-label="เลือกโหมดการแสดงผล" data-theme-switcher>
                <button type="button" class="as-segmented-btn" data-theme-value="system" aria-pressed="false">
                    <x-icon name="laptop" size="15" />
                    <span>ตามระบบ</span>
                </button>
                <button type="button" class="as-segmented-btn" data-theme-value="light" aria-pressed="false">
                    <x-icon name="sun" size="15" />
                    <span>สว่าง</span>
                </button>
                <button type="button" class="as-segmented-btn" data-theme-value="dark" aria-pressed="false">
                    <x-icon name="moon" size="15" />
                    <span>มืด</span>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="border-t border-[var(--as-line)] px-4 py-2">
            @csrf
            <button type="submit" class="min-h-11 text-sm font-bold text-red-400 hover:text-red-300">ออกจากระบบ</button>
        </form>
    </section>
@endsection
