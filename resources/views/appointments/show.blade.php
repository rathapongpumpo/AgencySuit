@extends('layouts.app')

@section('title', 'รายละเอียดนัดดู | AgencySuit')

@section('content')
    <a href="{{ route('today') }}" class="as-back-link"><x-icon name="chevron-right" size="18" class="rotate-180" />วันนี้</a>
    @if(session('success'))<p role="status" class="as-alert as-alert--success mb-5">{{ session('success') }}</p>@endif
    <div class="as-detail-hero">
        <h1 class="as-detail-title">นัดดูทรัพย์</h1>
        <p class="as-detail-subtitle">{{ $appointment->appointment_date->format('d/m/Y') }} · {{ substr($appointment->appointment_time, 0, 5) }}</p>
    </div>
    <dl class="as-detail-list">
        <div><dt>ลูกค้า</dt><dd>{{ $appointment->client->name }}</dd></div>
        <div><dt>ทรัพย์</dt><dd>{{ $appointment->property->name }}</dd></div>
        <div><dt>วันเวลา</dt><dd>{{ $appointment->appointment_date->format('d/m/Y') }} · {{ substr($appointment->appointment_time, 0, 5) }}</dd></div>
        @if($appointment->note)<div><dt>หมายเหตุ</dt><dd>{{ $appointment->note }}</dd></div>@endif
    </dl>
    @if($appointment->status === 'scheduled')
        <div class="as-form-actions mt-6"><a href="{{ route('appointments.edit', $appointment) }}" class="as-action-primary">แก้ไข</a><form method="POST" action="{{ route('appointments.cancel', $appointment) }}">@csrf @method('PATCH')<button class="as-action-secondary" type="submit">ยกเลิกนัดดู</button></form></div>
    @else
        <p class="as-alert mt-6">นัดนี้ถูกยกเลิกแล้ว</p>
    @endif
@endsection
