@extends('layouts.app')

@section('title', 'รายละเอียดนัดดู | AgencySuit')

@section('content')
    <a href="{{ route('today') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-green-800">← วันนี้</a>
    @if(session('success'))<p role="status" class="mt-4 border-y border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900">{{ session('success') }}</p>@endif
    <h1 class="mt-4 text-2xl font-bold tracking-tight">นัดดูทรัพย์</h1>
    <dl class="mt-6 divide-y divide-stone-200 border-y border-stone-200 bg-white"><div class="flex min-h-14 justify-between gap-4 px-4 items-center"><dt class="text-sm text-stone-600">ลูกค้า</dt><dd class="font-semibold">{{ $appointment->client->name }}</dd></div><div class="flex min-h-14 justify-between gap-4 px-4 items-center"><dt class="text-sm text-stone-600">ทรัพย์</dt><dd class="text-right font-semibold">{{ $appointment->property->name }}</dd></div><div class="flex min-h-14 justify-between gap-4 px-4 items-center"><dt class="text-sm text-stone-600">วันเวลา</dt><dd class="font-semibold">{{ $appointment->appointment_date->format('d/m/Y') }} · {{ substr($appointment->appointment_time, 0, 5) }}</dd></div>@if($appointment->note)<div class="flex min-h-14 justify-between gap-4 px-4 items-center"><dt class="text-sm text-stone-600">หมายเหตุ</dt><dd class="text-right">{{ $appointment->note }}</dd></div>@endif</dl>
    @if($appointment->status === 'scheduled')<div class="mt-6 space-y-3"><a href="{{ route('appointments.edit', $appointment) }}" class="flex min-h-12 w-full items-center justify-center rounded-xl bg-green-900 px-5 text-base font-semibold text-white">แก้ไข</a><form method="POST" action="{{ route('appointments.cancel', $appointment) }}">@csrf @method('PATCH')<button class="min-h-12 w-full rounded-xl border border-stone-300 bg-white px-5 text-base font-semibold text-stone-800">ยกเลิกนัดดู</button></form></div>@else<p class="mt-6 text-sm text-stone-600">นัดนี้ถูกยกเลิกแล้ว</p>@endif
@endsection
