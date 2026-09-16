@extends('layouts.app')

@section('title', 'ลูกค้า | AgencySuit')

@section('content')
    <p class="text-sm font-semibold text-green-800">ลูกค้า</p>
    <h1 class="mt-2 text-2xl font-bold tracking-tight">ยังไม่มีลูกค้าในระบบ</h1>
    <x-empty-state title="เพิ่มลูกค้ารายแรก" description="บันทึกความต้องการของลูกค้า เพื่อเตรียมดูทรัพย์และติดตามงานได้ง่ายขึ้น" action="เพิ่มลูกค้า" />
@endsection
