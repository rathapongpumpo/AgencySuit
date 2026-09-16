@extends('layouts.app')

@section('title', 'วันนี้ | AgencySuit')

@section('content')
    <p class="text-sm font-semibold text-green-800">วันนี้</p>
    <h1 class="mt-2 text-2xl font-bold tracking-tight">ยังไม่มีรายการต้องทำ</h1>
    <x-empty-state title="เริ่มจัดงานของคุณ" description="เพิ่มทรัพย์หรือลูกค้ารายแรก แล้วระบบจะช่วยรวมงานที่ต้องติดตามไว้ที่นี่" action="เพิ่มรายการแรก" />
@endsection
