@extends('layouts.guest')

@section('title', 'ตั้งรหัสผ่านใหม่ | AgencySuit')

@section('content')
    <div class="as-guest-panel">
        <h1 class="as-guest-title">ตั้งรหัสผ่าน<br>ใหม่อีกครั้ง</h1>
        @include('auth.partials.messages')
        <form method="POST" action="{{ route('password.update') }}" class="as-form mt-7" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div class="as-field"><label for="email" class="as-field-label">อีเมล</label><input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" autocomplete="email" required autofocus class="as-input"></div>
            <div class="as-field"><label for="password" class="as-field-label">รหัสผ่านใหม่</label><div class="relative"><input id="password" name="password" type="password" autocomplete="new-password" required class="as-input pr-20"><button type="button" data-password-toggle="password" class="absolute inset-y-0 right-0 min-h-12 px-4 text-sm font-bold text-teal-800">แสดง</button></div></div>
            <div class="as-field"><label for="password_confirmation" class="as-field-label">ยืนยันรหัสผ่านใหม่</label><div class="relative"><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="as-input pr-20"><button type="button" data-password-toggle="password_confirmation" class="absolute inset-y-0 right-0 min-h-12 px-4 text-sm font-bold text-teal-800">แสดง</button></div></div>
            <button type="submit" class="as-action-primary">บันทึกรหัสผ่านใหม่</button>
        </form>
    </div>
@endsection
