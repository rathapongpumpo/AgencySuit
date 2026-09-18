@extends('layouts.guest')

@section('title', 'เข้าสู่ระบบ | AgencySuit')

@section('content')
    <div class="as-guest-panel">
        <h1 class="as-guest-title">กลับมาจัดงาน<br>ให้เดินต่อกัน</h1>
        <p class="as-guest-copy">ทรัพย์ ลูกค้า นัดดู และสิ่งที่ต้องตามวันนี้ อยู่ในที่เดียว</p>
        @include('auth.partials.messages')

        <a href="{{ route('google.redirect') }}" class="as-action-secondary mt-7"><span class="font-bold text-teal-800">G</span>ดำเนินการต่อด้วย Google</a>
        <div class="as-divider">หรือ</div>

        <form method="POST" action="{{ route('login.store') }}" class="as-form" novalidate>
            @csrf
            <div class="as-field">
                <label for="email" class="as-field-label">อีเมล</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus class="as-input">
            </div>
            <div class="as-field">
                <div class="flex items-center justify-between gap-3"><label for="password" class="as-field-label">รหัสผ่าน</label><a href="{{ route('password.request') }}" class="as-text-link text-sm">ลืมรหัสผ่าน?</a></div>
                <div class="relative">
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="as-input pr-20">
                    <button type="button" data-password-toggle="password" class="absolute inset-y-0 right-0 min-h-12 px-4 text-sm font-bold text-teal-800">แสดง</button>
                </div>
            </div>
            <label class="flex min-h-11 items-center gap-3 text-sm text-stone-600"><input name="remember" type="checkbox" class="rounded border-stone-300 text-teal-800 focus:ring-teal-700">จำการเข้าสู่ระบบ</label>
            <button type="submit" class="as-action-primary">เข้าสู่ระบบ</button>
        </form>
        <p class="mt-6 text-center text-sm text-stone-600">ยังไม่มีบัญชี? <a href="{{ route('register') }}" class="as-text-link">สมัครสมาชิก</a></p>
    </div>
@endsection
