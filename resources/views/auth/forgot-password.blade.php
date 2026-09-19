@extends('layouts.guest')

@section('title', 'ลืมรหัสผ่าน | AgencySuit')

@section('content')
    <div class="as-auth-header">
        <h1 class="as-auth-title">ลืมรหัสผ่าน</h1>
        <p class="as-auth-copy">กรอกอีเมลของคุณเพื่อรับลิงก์สำหรับตั้งรหัสผ่านใหม่</p>
    </div>

    @include('auth.partials.messages')

    <form method="POST" action="{{ route('password.email') }}" class="as-auth-form" novalidate>
        @csrf
        <div class="as-auth-field">
            <label for="email" class="as-auth-label">อีเมล</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus placeholder="name@example.com" class="as-auth-input">
        </div>
        <button type="submit" class="as-btn-submit mt-2">ส่งลิงก์ตั้งรหัสผ่าน</button>
    </form>
    <p class="as-auth-footer"><a href="{{ route('login') }}" class="as-auth-link font-semibold">กลับไปเข้าสู่ระบบ</a></p>
@endsection
