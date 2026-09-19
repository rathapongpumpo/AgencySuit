@extends('layouts.app')

@section('title', 'ภาพรวมดีลและคอมมิชชัน | AgencySuit')

@section('content')
    <div class="as-page-head">
        <div>
            <h1 class="as-page-title">ภาพรวมดีล</h1>
            <p class="as-page-subtitle">จัดการสถานะและติดตามค่าคอมมิชชัน</p>
        </div>
    </div>

    {{-- Commission Summary Cards --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="as-surface p-4">
            <span class="text-xs font-semibold text-stone-500">คอมมิชชันที่ได้รับแล้ว</span>
            <p class="mt-1 text-lg font-extrabold text-[var(--as-teal)]">{{ number_format($closedCommission, 0) }} <span class="text-xs font-normal text-stone-500">บาท</span></p>
            <span class="text-[11px] text-stone-400">ปิดแล้ว {{ $closedCount }} ดีล</span>
        </div>
        <div class="as-surface p-4">
            <span class="text-xs font-semibold text-stone-500">คอมมิชชันรอปิดดีล</span>
            <p class="mt-1 text-lg font-bold text-stone-800">{{ number_format($pipelineCommission, 0) }} <span class="text-xs font-normal text-stone-500">บาท</span></p>
            <span class="text-[11px] text-stone-400">อยู่ระหว่างดีล {{ $activeCount }} รายการ</span>
        </div>
    </div>

    {{-- Filter Chips --}}
    <div class="mt-5 flex gap-2 overflow-x-auto pb-1">
        <a href="{{ route('deals.index', ['status' => 'active']) }}" @class(['as-chip is-active' => $currentFilter === 'active', 'as-chip' => $currentFilter !== 'active'])>
            กำลังดำเนินการ ({{ $activeCount }})
        </a>
        <a href="{{ route('deals.index', ['status' => 'closed']) }}" @class(['as-chip is-active' => $currentFilter === 'closed', 'as-chip' => $currentFilter !== 'closed'])>
            ปิดดีลแล้ว ({{ $closedCount }})
        </a>
        <a href="{{ route('deals.index', ['status' => 'all']) }}" @class(['as-chip is-active' => $currentFilter === 'all', 'as-chip' => $currentFilter !== 'all'])>
            ทั้งหมด ({{ $totalCount }})
        </a>
    </div>

    {{-- Deals List --}}
    @if ($deals->isEmpty())
        <div class="mt-4">
            <x-empty-state
                title="ยังไม่มีดีลในสถานะนี้"
                description="คุณสามารถสร้างดีลใหม่ได้จากหน้ารายละเอียดลูกค้า หรือปุ่มเพิ่มรายการด้านล่าง"
                action="ดูลูกค้าทั้งหมด"
                :action-url="route('clients.index')"
            />
        </div>
    @else
        <div class="mt-4 space-y-3">
            @foreach ($deals as $deal)
                <a href="{{ route('deals.show', $deal) }}" class="as-card block">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <span class="as-card-title truncate">{{ $deal->client?->name ?? 'ลูกค้า' }}</span>
                            @if ($deal->property)
                                <p class="text-xs text-stone-600 truncate mt-0.5">{{ $deal->property->name }}</p>
                            @endif
                        </div>
                        <span @class([
                            'as-status font-bold shrink-0',
                            'border-[var(--as-teal)] bg-[var(--as-teal-soft)] text-[var(--as-teal)]' => $deal->stage === 'closed',
                        ])>
                            {{ $deal->stageLabel() }}
                        </span>
                    </div>

                    <div class="mt-3 flex items-center justify-between border-t border-stone-100 pt-2.5 text-xs">
                        <span class="text-stone-500">มูลค่า: {{ $deal->amount !== null ? number_format((float) $deal->amount, 0).' บาท' : 'ยังไม่ระบุ' }}</span>
                        <span class="font-extrabold text-[var(--as-teal)]">คอมฯ ฉัน: {{ number_format($deal->agentCommission(), 0) }} บาท</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection
