@extends('layouts.app')

@section('title', 'วันนี้ | AgencySuit')

@section('content')
    @php($hasWork = $overdueFollowUps->isNotEmpty() || $todayAppointments->isNotEmpty() || $todayFollowUps->isNotEmpty())

    <div class="as-page-head">
        <div>
            <h1 class="as-page-title">{{ $hasWork ? 'สิ่งที่ต้องทำวันนี้' : 'วันนี้' }}</h1>
            <p class="as-page-date">{{ now()->format('d/m/Y') }} · งานที่ควรขยับต่อ</p>
        </div>
    </div>

    @if (! $hasWork)
        <div class="as-surface overflow-hidden">
            <x-empty-state title="ยังไม่มีรายการต้องทำ" description="เพิ่มทรัพย์หรือลูกค้ารายแรก แล้วระบบจะรวมงานที่ต้องติดตามไว้ที่นี่" action="เพิ่มรายการแรก" />
        </div>
    @else
        @if ($overdueFollowUps->isNotEmpty())
            <section class="as-work-section" aria-labelledby="overdue-heading">
                <div class="as-section-head">
                    <h2 id="overdue-heading" class="as-section-title">เลยกำหนด <span class="as-count as-count--alert">{{ $overdueFollowUps->count() }}</span></h2>
                    <span class="as-section-action">ต้องจัดการก่อน</span>
                </div>
                <ul>
                    @foreach ($overdueFollowUps as $followUp)
                        <li class="as-work-row as-work-row--overdue">
                            <span class="as-check"><x-icon name="clock" size="15" /></span>
                            <div class="as-row-body">
                                <a href="{{ route('clients.show', $followUp->client) }}" class="as-row-title">ติดตาม {{ $followUp->client->name }}</a>
                                <span class="as-row-meta">{{ $followUp->note ?: 'เปิดหน้าลูกค้าเพื่อดูรายละเอียด' }}</span>
                                <form method="POST" action="{{ route('followups.complete', $followUp) }}">
                                    @csrf @method('PATCH')
                                    <button class="as-row-control" type="submit"><x-icon name="check" size="16" />ทำแล้ว</button>
                                </form>
                            </div>
                            <span class="as-row-time as-row-time--alert">{{ $followUp->due_date->format('d/m') }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($todayAppointments->isNotEmpty())
            <section class="as-work-section" aria-labelledby="appointments-heading">
                <div class="as-section-head">
                    <h2 id="appointments-heading" class="as-section-title">นัดวันนี้ <span class="as-count">{{ $todayAppointments->count() }}</span></h2>
                    <span class="as-section-action">{{ now()->format('d/m') }}</span>
                </div>
                <ul>
                    @foreach ($todayAppointments as $appointment)
                        <li>
                            <a href="{{ route('appointments.show', $appointment) }}" class="as-work-row">
                                <span class="as-check"><x-icon name="calendar" size="15" /></span>
                                <span class="as-row-body">
                                    <span class="as-row-title">{{ $appointment->client->name }} · {{ $appointment->property->name }}</span>
                                    <span class="as-row-meta">นัดดูทรัพย์ · เปิดดูรายละเอียดนัด</span>
                                </span>
                                <span class="as-row-time">{{ substr($appointment->appointment_time, 0, 5) }}<x-icon name="chevron-right" size="18" class="as-row-chevron ml-1 inline" /></span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($todayFollowUps->isNotEmpty())
            <section class="as-work-section" aria-labelledby="today-followups-heading">
                <div class="as-section-head">
                    <h2 id="today-followups-heading" class="as-section-title">ต้องติดตามวันนี้ <span class="as-count">{{ $todayFollowUps->count() }}</span></h2>
                    <span class="as-section-action">วันนี้</span>
                </div>
                <ul>
                    @foreach ($todayFollowUps as $followUp)
                        <li class="as-work-row">
                            <span class="as-check"><x-icon name="clock" size="15" /></span>
                            <div class="as-row-body">
                                <a href="{{ route('clients.show', $followUp->client) }}" class="as-row-title">{{ $followUp->client->name }}</a>
                                <span class="as-row-meta">{{ $followUp->note ?: 'กลับไปคุยกับลูกค้ารายนี้' }}</span>
                                <form method="POST" action="{{ route('followups.complete', $followUp) }}">
                                    @csrf @method('PATCH')
                                    <button class="as-row-control" type="submit"><x-icon name="check" size="16" />ทำแล้ว</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    @endif
@endsection
