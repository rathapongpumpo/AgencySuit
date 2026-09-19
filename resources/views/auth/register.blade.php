@extends('layouts.guest')

@section('title', 'สมัครสมาชิก | AgencySuit')

@section('content')
    <div class="as-auth-header">
        <h1 class="as-auth-title">สมัครสมาชิก</h1>
        <p class="as-auth-copy">เริ่มต้นจัดการทรัพย์ ลูกค้า และงานประจำวันของคุณ</p>
    </div>

    @include('auth.partials.messages')

    <a href="{{ route('google.redirect') }}" class="as-btn-google">
        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17Z"/>
            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24Z"/>
            <path fill="#FBBC05" d="M5.28 14.27a7.22 7.22 0 0 1 0-4.54V6.58H1.25a11.98 11.98 0 0 0 0 10.84l4.03-3.15Z"/>
            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98Z"/>
        </svg>
        <span>สมัครด้วย Google</span>
    </a>

    <div class="as-auth-divider">
        <span>หรือใช้อีเมล</span>
    </div>

    <form method="POST" action="{{ route('register.store') }}" class="as-auth-form" novalidate>
        @csrf
        <div class="as-auth-field">
            <label for="email" class="as-auth-label">อีเมล</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus placeholder="name@example.com" class="as-auth-input">
        </div>
        <div class="as-auth-field">
            <label for="password" class="as-auth-label">รหัสผ่าน</label>
            <div class="relative">
                <input id="password" name="password" type="password" autocomplete="new-password" required placeholder="อย่างน้อย 8 ตัวอักษร" class="as-auth-input pr-12">
                <button type="button" data-password-toggle="password" aria-label="แสดงรหัสผ่าน" class="absolute inset-y-0 right-0 flex items-center justify-center px-3.5 text-stone-400 hover:text-stone-750 transition-colors">
                    <x-icon name="eye" size="18" class="toggle-icon-eye" />
                    <x-icon name="eye-off" size="18" class="toggle-icon-eye-off hidden" />
                </button>
            </div>
        </div>
        <div class="as-auth-field">
            <label for="password_confirmation" class="as-auth-label">ยืนยันรหัสผ่าน</label>
            <div class="relative">
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required placeholder="กรอกรหัสผ่านอีกครั้ง" class="as-auth-input pr-12">
                <button type="button" data-password-toggle="password_confirmation" aria-label="แสดงรหัสผ่าน" class="absolute inset-y-0 right-0 flex items-center justify-center px-3.5 text-stone-400 hover:text-stone-750 transition-colors">
                    <x-icon name="eye" size="18" class="toggle-icon-eye" />
                    <x-icon name="eye-off" size="18" class="toggle-icon-eye-off hidden" />
                </button>
            </div>
        </div>
        <button type="submit" class="as-btn-submit mt-2">สมัครสมาชิก</button>
    </form>
    <p class="as-auth-footer">มีบัญชีแล้ว? <a href="{{ route('login') }}" class="as-auth-link font-semibold">เข้าสู่ระบบ</a></p>
@endsection
