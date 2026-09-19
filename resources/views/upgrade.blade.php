@extends('layouts.app')

@section('title', 'แผนการใช้งาน | AgencySuit')

@section('content')
    <x-back-button :fallback="route('more')" label="ย้อนกลับ" />
    <div class="as-detail-hero">
        <h1 class="as-detail-title">Agent Pocket Pro</h1>
        <p class="as-page-subtitle">ซื้อครั้งเดียว ปลดข้อจำกัด Free เมื่อระบบชำระเงินพร้อมใช้งาน</p>
    </div>
    <section class="as-surface overflow-hidden">
        <div class="px-4 py-5">
            <p class="text-base font-bold">แผนปัจจุบัน: {{ strtoupper(auth()->user()->plan) }}</p>
            <p class="mt-2 text-sm leading-6 text-stone-600">หน้านี้เป็น placeholder สำหรับต่อ payment ภายหลัง ยังไม่มีการเรียกเก็บเงินจริง</p>
        </div>
        <div class="border-t border-stone-200 px-4 py-4">
            <p class="text-sm font-bold text-[var(--as-teal)]">สิ่งที่จะต่อยอดใน Pro</p>
            <p class="mt-1 text-sm leading-6 text-stone-600">พื้นที่เก็บข้อมูลและประวัติที่มากขึ้น โดยไม่ปิดกั้น workflow หลักของคุณ</p>
        </div>
    </section>
@endsection
