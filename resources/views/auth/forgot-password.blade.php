@extends('layouts.guest')

@section('title', 'ลืมรหัสผ่าน | AgencySuit')

@section('content')
    <div class="as-guest-panel">
        <h1 class="as-guest-title">กลับเข้าสู่ระบบ<br>ได้อีกครั้ง</h1>
        <p class="as-guest-copy">กรอกอีเมลเพื่อรับลิงก์ตั้งรหัสผ่านใหม่</p>
        @include('auth.partials.messages')
        <form method="POST" action="{{ route('password.email') }}" class="as-form mt-7" novalidate>
            @csrf
            <div class="as-field"><label for="email" class="as-field-label">อีเมล</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus class="as-input"></div>
            <button type="submit" class="as-action-primary">ส่งลิงก์ตั้งรหัสผ่าน</button>
        </form>
        <p class="mt-6 text-center text-sm"><a href="{{ route('login') }}" class="as-text-link">กลับไปเข้าสู่ระบบ</a></p>
    </div>
@endsection
