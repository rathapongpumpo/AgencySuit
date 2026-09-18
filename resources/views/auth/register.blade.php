@extends('layouts.guest')

@section('title', 'สมัครสมาชิก | AgencySuit')

@section('content')
    <div class="as-guest-panel">
        <h1 class="as-guest-title">เริ่มจัดงาน<br>ให้เป็นที่เดียว</h1>
        <p class="as-guest-copy">เริ่มต้นด้วยอีเมลและรหัสผ่าน แล้วค่อยเติม workflow ของคุณทีละขั้น</p>
        @include('auth.partials.messages')

        <a href="{{ route('google.redirect') }}" class="as-action-secondary mt-7"><span class="font-bold text-teal-800">G</span>ดำเนินการต่อด้วย Google</a>
        <div class="as-divider">หรือ</div>

        <form method="POST" action="{{ route('register.store') }}" class="as-form" novalidate>
            @csrf
            <div class="as-field">
                <label for="email" class="as-field-label">อีเมล</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus class="as-input">
            </div>
            <div class="as-field">
                <label for="password" class="as-field-label">รหัสผ่าน</label>
                <div class="relative"><input id="password" name="password" type="password" autocomplete="new-password" required class="as-input pr-20"><button type="button" data-password-toggle="password" class="absolute inset-y-0 right-0 min-h-12 px-4 text-sm font-bold text-teal-800">แสดง</button></div>
            </div>
            <div class="as-field">
                <label for="password_confirmation" class="as-field-label">ยืนยันรหัสผ่าน</label>
                <div class="relative"><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="as-input pr-20"><button type="button" data-password-toggle="password_confirmation" class="absolute inset-y-0 right-0 min-h-12 px-4 text-sm font-bold text-teal-800">แสดง</button></div>
            </div>
            <button type="submit" class="as-action-primary">สมัครสมาชิก</button>
        </form>
        <p class="mt-6 text-center text-sm text-stone-600">มีบัญชีแล้ว? <a href="{{ route('login') }}" class="as-text-link">เข้าสู่ระบบ</a></p>
    </div>
@endsection
