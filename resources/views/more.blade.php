@extends('layouts.app')

@section('title', 'เพิ่มเติม | AgencySuit')

@section('content')
    <p class="text-sm font-semibold text-green-800">เพิ่มเติม</p>
    <h1 class="mt-2 text-2xl font-bold tracking-tight">การตั้งค่า</h1>

    <section class="mt-6 divide-y divide-stone-200 border-y border-stone-200 bg-white">
        <div class="flex min-h-15 items-center justify-between gap-4 px-4">
            <div><h2 class="font-medium">บัญชีผู้ใช้</h2><p class="mt-0.5 text-sm text-stone-600">{{ auth()->user()->email }}</p></div>
        </div>
        <div class="flex min-h-15 items-center justify-between gap-4 px-4 text-stone-500"><span class="font-medium">ส่งความคิดเห็น</span><span class="text-sm">เร็ว ๆ นี้</span></div>
        <form method="POST" action="{{ route('logout') }}" class="px-4 py-2">
            @csrf
            <button type="submit" class="min-h-11 text-sm font-semibold text-red-700">ออกจากระบบ</button>
        </form>
    </section>
@endsection
