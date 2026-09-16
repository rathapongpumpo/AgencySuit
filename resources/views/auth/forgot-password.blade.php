@extends('layouts.guest')

@section('title', 'ลืมรหัสผ่าน | AgencySuit')

@section('content')
    <div class="mx-auto flex w-full max-w-sm flex-1 flex-col justify-center">
        <a href="{{ route('login') }}" class="text-sm font-semibold text-green-800">AgencySuit</a>
        <h1 class="mt-3 text-3xl font-bold tracking-tight">ลืมรหัสผ่าน?</h1>
        <p class="mt-2 text-sm leading-6 text-stone-600">กรอกอีเมลเพื่อรับลิงก์ตั้งรหัสผ่านใหม่</p>
        @include('auth.partials.messages')
        <form method="POST" action="{{ route('password.email') }}" class="mt-7 space-y-4">
            @csrf
            <div><label for="email" class="text-sm font-medium">อีเมล</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base focus:border-green-700 focus:ring-green-700"></div>
            <button type="submit" class="min-h-12 w-full rounded-xl bg-green-900 px-5 text-base font-semibold text-white">ส่งลิงก์ตั้งรหัสผ่าน</button>
        </form>
        <p class="mt-6 text-center text-sm"><a href="{{ route('login') }}" class="font-semibold text-green-800">กลับไปเข้าสู่ระบบ</a></p>
    </div>
@endsection
