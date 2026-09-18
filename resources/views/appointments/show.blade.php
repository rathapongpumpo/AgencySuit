@extends('layouts.app')

@section('title', 'รายละเอียดนัดดู | AgencySuit')

@section('content')
    <x-back-button :fallback="route('today')" label="ย้อนกลับ" />

    @if(session('success'))
        <p role="status" class="as-alert as-alert--success mb-5">{{ session('success') }}</p>
    @endif

    <div class="as-detail-hero">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h1 class="as-detail-title">นัดดูทรัพย์</h1>
                <p class="as-detail-subtitle">{{ $appointment->appointment_date->format('d/m/Y') }} · {{ substr($appointment->appointment_time, 0, 5) }} น.</p>
            </div>
            @if($appointment->status === 'scheduled')
                <span class="as-status shrink-0 font-bold">รอนัดดู</span>
            @else
                <span class="as-status as-status--quiet shrink-0 font-bold">ยกเลิกแล้ว</span>
            @endif
        </div>
    </div>

    <dl class="as-detail-list mt-4">
        <div>
            <dt>ลูกค้า</dt>
            <dd><a href="{{ route('clients.show', $appointment->client) }}" class="as-text-link font-bold">{{ $appointment->client->name }}</a></dd>
        </div>
        <div>
            <dt>ทรัพย์</dt>
            <dd><a href="{{ route('properties.show', $appointment->property) }}" class="as-text-link font-bold">{{ $appointment->property->name }}</a></dd>
        </div>
        <div>
            <dt>วันเวลา</dt>
            <dd>{{ $appointment->appointment_date->format('d/m/Y') }} เวลา {{ substr($appointment->appointment_time, 0, 5) }} น.</dd>
        </div>
        @if($appointment->note)
            <div>
                <dt>หมายเหตุ</dt>
                <dd>{{ $appointment->note }}</dd>
            </div>
        @endif
    </dl>

    @if($appointment->status === 'scheduled')
        @php
            $dtStart = \Carbon\Carbon::parse($appointment->appointment_date->format('Y-m-d').' '.$appointment->appointment_time);
            $dtEnd = (clone $dtStart)->addHour();
            $gCalTitle = 'นัดดู: '.($appointment->property?->name ?? 'ทรัพย์');
            $gCalDetails = 'ลูกค้า: '.($appointment->client?->name ?? '-').($appointment->client?->phone ? ' ('.$appointment->client->phone.')' : '').($appointment->note ? "\nหมายเหตุ: ".$appointment->note : '');
            $gCalLocation = $appointment->property?->location ?? '';
            $gCalUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE'
                .'&text='.urlencode($gCalTitle)
                .'&dates='.$dtStart->utc()->format('Ymd\THis\Z').'/'.$dtEnd->utc()->format('Ymd\THis\Z')
                .'&details='.urlencode($gCalDetails)
                .'&location='.urlencode($gCalLocation);
        @endphp

        <div class="mt-4 rounded-xl border border-stone-200 bg-white p-3.5 shadow-sm">
            <span class="text-xs font-semibold text-stone-500">บันทึกลงปฏิทินมือถือ</span>
            <div class="mt-2.5 grid grid-cols-2 gap-2">
                <a href="{{ $gCalUrl }}" target="_blank" rel="noopener noreferrer" class="as-action-secondary flex items-center justify-center gap-1.5 text-xs font-bold text-teal-900">
                    <x-icon name="calendar" size="15" />
                    <span>Google Calendar</span>
                </a>
                <a href="{{ route('appointments.ics', $appointment) }}" class="as-action-secondary flex items-center justify-center gap-1.5 text-xs font-bold text-stone-800">
                    <x-icon name="clock" size="15" />
                    <span>ดาวน์โหลด .ICS</span>
                </a>
            </div>
        </div>
    @endif

    @if($appointment->status === 'scheduled')
        <div class="mt-6 grid grid-cols-2 gap-2">
            <a href="{{ route('appointments.edit', $appointment) }}" class="as-action-primary">แก้ไขนัด</a>
            <form method="POST" action="{{ route('appointments.cancel', $appointment) }}">
                @csrf
                @method('PATCH')
                <button class="as-action-secondary" type="submit">ยกเลิกนัดดู</button>
            </form>
        </div>
    @else
        <div class="mt-6">
            <p class="as-alert as-alert--warning">นัดนี้ถูกยกเลิกแล้ว</p>
        </div>
    @endif

    <div class="mt-8 border-t border-stone-200 pt-6">
        <button type="button" class="as-action-danger" onclick="document.getElementById('del-apt-dialog').showModal()">ลบนัดดูนี้</button>
    </div>

    <dialog id="del-apt-dialog" class="as-confirm-dialog" aria-labelledby="del-apt-title">
        <h3 id="del-apt-title" class="as-section-title">ต้องการลบนัดดูนี้?</h3>
        <p class="as-page-subtitle">รายการนัดดูนี้จะถูกลบออกจากระบบอย่างถาวร</p>
        <div class="mt-5 grid grid-cols-2 gap-2">
            <button type="button" class="as-action-secondary" onclick="document.getElementById('del-apt-dialog').close()">ยกเลิก</button>
            <form method="POST" action="{{ route('appointments.destroy', $appointment) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="as-action-danger">ยืนยันลบ</button>
            </form>
        </div>
    </dialog>
@endsection
