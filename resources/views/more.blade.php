@extends('layouts.app')

@section('title', 'เพิ่มเติม | AgencySuit')

@section('content')
    <p class="text-sm font-semibold text-green-800">AgencySuit</p>
    <h1 class="mt-2 text-3xl font-bold tracking-tight">เพิ่มเติม</h1>
    <section class="mt-6 rounded-2xl border border-stone-200 bg-white p-5">
        <h2 class="font-semibold">บัญชี</h2>
        <p class="mt-2 text-sm text-stone-600">{{ auth()->user()->email }}</p>
        <form method="POST" action="{{ route('logout') }}" class="mt-5">
            @csrf
            <button type="submit" class="min-h-12 w-full rounded-xl border border-stone-300 bg-white px-5 text-base font-semibold text-stone-800">ออกจากระบบ</button>
        </form>
    </section>
@endsection
