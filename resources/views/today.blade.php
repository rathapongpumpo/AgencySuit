@extends('layouts.app')

@section('title', 'วันนี้ | AgencySuit')

@section('content')
    <p class="text-sm font-semibold text-green-800">วันนี้</p>
    @if ($overdueFollowUps->isEmpty() && $todayAppointments->isEmpty() && $todayFollowUps->isEmpty())
        <h1 class="mt-2 text-2xl font-bold tracking-tight">ยังไม่มีรายการต้องทำ</h1>
        <x-empty-state title="เริ่มจัดงานของคุณ" description="เพิ่มทรัพย์หรือลูกค้ารายแรก แล้วระบบจะช่วยรวมงานที่ต้องติดตามไว้ที่นี่" action="เพิ่มรายการแรก" />
    @else
        <h1 class="mt-2 text-2xl font-bold tracking-tight">สิ่งที่ต้องทำวันนี้</h1>
        @if ($overdueFollowUps->isNotEmpty())
            <section class="mt-6" aria-labelledby="overdue-heading">
                <h2 id="overdue-heading" class="text-lg font-semibold text-red-800">เลยกำหนด</h2>
                <ul class="mt-3 divide-y divide-stone-200 border-y border-stone-200 bg-white">
                    @foreach ($overdueFollowUps as $followUp)
                        <li class="px-4 py-4"><div class="flex items-start justify-between gap-3"><a href="{{ route('clients.show', $followUp->client) }}" class="min-w-0 font-semibold underline underline-offset-2">ติดตาม {{ $followUp->client->name }}</a><span class="shrink-0 text-sm text-red-700">{{ $followUp->due_date->format('d/m') }}</span></div><form method="POST" action="{{ route('followups.complete', $followUp) }}" class="mt-2">@csrf @method('PATCH')<button class="min-h-11 text-sm font-semibold text-green-800">ทำแล้ว</button></form></li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($todayAppointments->isNotEmpty())
            <section class="mt-6" aria-labelledby="appointments-heading">
                <h2 id="appointments-heading" class="text-lg font-semibold">นัดวันนี้</h2>
                <ul class="mt-3 divide-y divide-stone-200 border-y border-stone-200 bg-white">
                    @foreach ($todayAppointments as $appointment)
                        <li><a href="{{ route('appointments.show', $appointment) }}" class="block px-4 py-4"><div class="flex items-start justify-between gap-3"><span class="font-semibold">{{ $appointment->appointment_time }}</span><span class="text-sm text-stone-600">ดูนัด</span></div><p class="mt-1 text-sm text-stone-700">{{ $appointment->client->name }} · {{ $appointment->property->name }}</p></a></li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($todayFollowUps->isNotEmpty())
            <section class="mt-6" aria-labelledby="today-followups-heading">
                <h2 id="today-followups-heading" class="text-lg font-semibold">ต้องติดตามวันนี้</h2>
                <ul class="mt-3 divide-y divide-stone-200 border-y border-stone-200 bg-white">
                    @foreach ($todayFollowUps as $followUp)
                        <li class="px-4 py-4"><a href="{{ route('clients.show', $followUp->client) }}" class="font-semibold underline underline-offset-2">{{ $followUp->client->name }}</a><form method="POST" action="{{ route('followups.complete', $followUp) }}" class="mt-2">@csrf @method('PATCH')<button class="min-h-11 text-sm font-semibold text-green-800">ทำแล้ว</button></form></li>
                    @endforeach
                </ul>
            </section>
        @endif
    @endif
@endsection
