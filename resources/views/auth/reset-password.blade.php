@extends('layouts.guest')

@section('title', 'ตั้งรหัสผ่านใหม่ | AgencySuit')

@section('content')
    <div class="mx-auto flex w-full max-w-sm flex-1 flex-col justify-center">
        <a href="{{ route('login') }}" class="text-sm font-semibold text-green-800">AgencySuit</a>
        <h1 class="mt-3 text-3xl font-bold tracking-tight">ตั้งรหัสผ่านใหม่</h1>
        @include('auth.partials.messages')
        <form method="POST" action="{{ route('password.update') }}" class="mt-7 space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div><label for="email" class="text-sm font-medium">อีเมล</label><input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" autocomplete="email" required autofocus class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base focus:border-green-700 focus:ring-green-700"></div>
            <div><label for="password" class="text-sm font-medium">รหัสผ่านใหม่</label><div class="relative mt-1.5"><input id="password" name="password" type="password" autocomplete="new-password" required class="min-h-12 w-full rounded-xl border border-stone-300 py-0 pl-4 pr-20 text-base focus:border-green-700 focus:ring-green-700"><button type="button" data-password-toggle="password" class="absolute inset-y-0 right-0 min-h-12 px-4 text-sm font-medium text-green-800">แสดง</button></div></div>
            <div><label for="password_confirmation" class="text-sm font-medium">ยืนยันรหัสผ่านใหม่</label><div class="relative mt-1.5"><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="min-h-12 w-full rounded-xl border border-stone-300 py-0 pl-4 pr-20 text-base focus:border-green-700 focus:ring-green-700"><button type="button" data-password-toggle="password_confirmation" class="absolute inset-y-0 right-0 min-h-12 px-4 text-sm font-medium text-green-800">แสดง</button></div></div>
            <button type="submit" class="min-h-12 w-full rounded-xl bg-green-900 px-5 text-base font-semibold text-white">บันทึกรหัสผ่านใหม่</button>
        </form>
    </div>
@endsection
