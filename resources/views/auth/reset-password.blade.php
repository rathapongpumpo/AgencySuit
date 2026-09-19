@extends('layouts.guest')

@section('title', 'ตั้งรหัสผ่านใหม่ | AgencySuit')

@section('content')
    <div class="as-auth-header">
        <h1 class="as-auth-title">ตั้งรหัสผ่านใหม่</h1>
        <p class="as-auth-copy">กำหนดรหัสผ่านใหม่เพื่อเข้าใช้งานบัญชีของคุณ</p>
    </div>

    @include('auth.partials.messages')

    <form method="POST" action="{{ route('password.update') }}" class="as-auth-form" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="as-auth-field">
            <label for="email" class="as-auth-label">อีเมล</label>
            <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" autocomplete="email" required autofocus placeholder="name@example.com" class="as-auth-input">
        </div>
        <div class="as-auth-field">
            <label for="password" class="as-auth-label">รหัสผ่านใหม่</label>
            <div class="relative">
                <input id="password" name="password" type="password" autocomplete="new-password" required placeholder="อย่างน้อย 8 ตัวอักษร" class="as-auth-input pr-12">
                <button type="button" data-password-toggle="password" aria-label="แสดงรหัสผ่าน" class="absolute inset-y-0 right-0 flex items-center justify-center px-3.5 text-stone-400 hover:text-stone-750 transition-colors">
                    <x-icon name="eye" size="18" class="toggle-icon-eye" />
                    <x-icon name="eye-off" size="18" class="toggle-icon-eye-off hidden" />
                </button>
            </div>
        </div>
        <div class="as-auth-field">
            <label for="password_confirmation" class="as-auth-label">ยืนยันรหัสผ่านใหม่</label>
            <div class="relative">
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required placeholder="กรอกรหัสผ่านใหม่อีกครั้ง" class="as-auth-input pr-12">
                <button type="button" data-password-toggle="password_confirmation" aria-label="แสดงรหัสผ่าน" class="absolute inset-y-0 right-0 flex items-center justify-center px-3.5 text-stone-400 hover:text-stone-750 transition-colors">
                    <x-icon name="eye" size="18" class="toggle-icon-eye" />
                    <x-icon name="eye-off" size="18" class="toggle-icon-eye-off hidden" />
                </button>
            </div>
        </div>
        <button type="submit" class="as-btn-submit mt-2">บันทึกรหัสผ่านใหม่</button>
    </form>
    <p class="as-auth-footer"><a href="{{ route('login') }}" class="as-auth-link font-semibold">กลับไปเข้าสู่ระบบ</a></p>
@endsection
