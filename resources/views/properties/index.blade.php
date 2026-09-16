@extends('layouts.app')

@section('title', 'ทรัพย์ | AgencySuit')

@section('content')
    <p class="text-sm font-semibold text-green-800">ทรัพย์</p>
    <h1 class="mt-2 text-2xl font-bold tracking-tight">ยังไม่มีทรัพย์ในระบบ</h1>
    <x-empty-state title="เพิ่มทรัพย์แรก" description="บันทึกทรัพย์ที่ดูแลไว้ เพื่อให้พร้อมจับคู่และติดตามลูกค้าในขั้นตอนถัดไป" action="เพิ่มทรัพย์" />
@endsection
