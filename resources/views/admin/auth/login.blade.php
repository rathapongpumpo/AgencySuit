@extends('layouts.guest')

@section('title', 'เข้าสู่ระบบผู้ดูแล | AgencySuit Admin')

@section('content')
    <div class="as-auth-header">
        <div class="mb-2">
            <span class="inline-flex items-center gap-1.5 rounded-md px-2 py-0.5 text-xs font-bold bg-[var(--as-teal-soft)] text-[var(--as-teal)]">
                <x-icon name="shield" size="13" />
                <span>ระบบผู้ดูแลระบบ (Admin Portal)</span>
            </span>
        </div>
        <h1 class="as-auth-title">เข้าสู่ระบบ Admin</h1>
        <p class="as-auth-copy">สำหรับผู้ดูแลระบบเท่านั้น ไม่ใช่บัญชีตัวแทนทั่วไป</p>
    </div>

    @include('auth.partials.messages')

    <form method="POST" action="{{ route('admin.login.store') }}" class="as-auth-form mt-4" novalidate>
        @csrf
        <div class="as-auth-field">
            <label for="email" class="as-auth-label">อีเมลแอดมิน</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus placeholder="admin@example.com" class="as-auth-input">
        </div>

        <div class="as-auth-field">
            <label for="password" class="as-auth-label">รหัสผ่าน</label>
            <div class="relative">
                <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="••••••••" class="as-auth-input pr-12">
                <button type="button" data-password-toggle="password" aria-label="แสดงรหัสผ่าน" class="absolute inset-y-0 right-0 flex items-center justify-center px-3.5 text-stone-400 hover:text-stone-750 transition-colors">
                    <x-icon name="eye" size="18" class="toggle-icon-eye" />
                    <x-icon name="eye-off" size="18" class="toggle-icon-eye-off hidden" />
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between pt-0.5">
            <label class="inline-flex cursor-pointer items-center gap-2 text-xs font-medium text-stone-600 select-none">
                <input name="remember" type="checkbox" class="as-checkbox">
                <span>จำการเข้าสู่ระบบ</span>
            </label>
        </div>

        <button type="submit" class="as-btn-submit mt-2">เข้าสู่ระบบผู้ดูแล</button>
    </form>

    <p class="as-auth-footer mt-8">
        ตัวแทนอสังหาฯ? <a href="{{ route('login') }}" class="as-auth-link font-semibold">ไปหน้าเข้าใช้งานสำหรับตัวแทน</a>
    </p>
@endsection
