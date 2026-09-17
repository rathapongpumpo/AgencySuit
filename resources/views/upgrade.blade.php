@extends('layouts.app')

@section('title', 'แผนการใช้งาน | AgencySuit')

@section('content')
    <a href="{{ route('more') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-green-800">← เพิ่มเติม</a>
    <p class="mt-4 text-sm font-semibold text-green-800">แผนการใช้งาน</p>
    <h1 class="mt-2 text-2xl font-bold tracking-tight">Agent Pocket Pro</h1>
    <p class="mt-2 text-sm leading-6 text-stone-600">ซื้อครั้งเดียว ปลดข้อจำกัด Free เมื่อระบบชำระเงินพร้อมใช้งาน</p>
    <section class="mt-6 border-y border-stone-200 bg-white px-4 py-4">
        <p class="text-base font-semibold">แผนปัจจุบัน: {{ strtoupper(auth()->user()->plan) }}</p>
        <p class="mt-2 text-sm leading-6 text-stone-600">หน้านี้เป็น placeholder สำหรับต่อ payment ภายหลัง ยังไม่มีการเรียกเก็บเงินจริง</p>
    </section>
@endsection
