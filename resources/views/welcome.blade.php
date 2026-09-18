@extends('layouts.guest')

@section('title', 'AgencySuit')

@section('content')
    <div class="as-guest-panel">
        <p class="text-sm font-bold uppercase tracking-[0.16em] text-teal-700">งานของคุณในที่เดียว</p>
        <h1 class="as-guest-title mt-3">ทรัพย์ ลูกค้า<br>และงานวันนี้</h1>
        <p class="as-guest-copy">พื้นที่ทำงานเรียบง่ายสำหรับนายหน้าที่ต้องการกลับมาจัดการสิ่งสำคัญได้ทันทีจากมือถือ</p>
        <div class="mt-8 space-y-3">
            <a href="{{ route('login') }}" class="as-action-primary">เข้าสู่ระบบ</a>
            <a href="{{ route('register') }}" class="as-action-secondary">สมัครสมาชิก</a>
        </div>
    </div>
@endsection
