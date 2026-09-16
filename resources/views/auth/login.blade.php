@extends('layouts.guest')

@section('title', 'เข้าสู่ระบบ | AgencySuit')

@section('content')
    <div class="mx-auto flex w-full max-w-sm flex-1 flex-col justify-center">
        <a href="{{ url('/') }}" class="text-sm font-semibold text-green-800">AgencySuit</a>
        <h1 class="mt-3 text-3xl font-bold tracking-tight">เข้าสู่ระบบ</h1>
        <p class="mt-2 text-sm leading-6 text-stone-600">จัดการงานนายหน้าของคุณได้จากที่เดียว</p>
        @include('auth.partials.messages')
        <a href="{{ route('google.redirect') }}" class="mt-7 flex min-h-12 items-center justify-center gap-3 rounded-xl border border-stone-300 bg-white px-4 text-sm font-semibold text-stone-800 shadow-sm"><span aria-hidden="true" class="text-lg font-bold text-red-500">G</span>ดำเนินการต่อด้วย Google</a>
        <div class="my-6 flex items-center gap-3 text-xs text-stone-400"><span class="h-px flex-1 bg-stone-200"></span>หรือ<span class="h-px flex-1 bg-stone-200"></span></div>
        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf
            <div><label for="email" class="text-sm font-medium">อีเมล</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base focus:border-green-700 focus:ring-green-700"></div>
            <div><div class="flex items-center justify-between gap-3"><label for="password" class="text-sm font-medium">รหัสผ่าน</label><a href="{{ route('password.request') }}" class="text-sm font-medium text-green-800">ลืมรหัสผ่าน?</a></div><div class="relative mt-1.5"><input id="password" name="password" type="password" autocomplete="current-password" required class="min-h-12 w-full rounded-xl border border-stone-300 py-0 pl-4 pr-20 text-base focus:border-green-700 focus:ring-green-700"><button type="button" data-password-toggle="password" class="absolute inset-y-0 right-0 min-h-12 px-4 text-sm font-medium text-green-800">แสดง</button></div></div>
            <label class="flex min-h-11 items-center gap-3 text-sm text-stone-600"><input name="remember" type="checkbox" class="rounded border-stone-300 text-green-800 focus:ring-green-700">จำการเข้าสู่ระบบ</label>
            <button type="submit" class="min-h-12 w-full rounded-xl bg-green-900 px-5 text-base font-semibold text-white">เข้าสู่ระบบ</button>
        </form>
        <p class="mt-6 text-center text-sm text-stone-600">ยังไม่มีบัญชี? <a href="{{ route('register') }}" class="font-semibold text-green-800">สมัครสมาชิก</a></p>
    </div>
@endsection
