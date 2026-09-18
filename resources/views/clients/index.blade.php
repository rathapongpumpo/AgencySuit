@extends('layouts.app')

@section('title', 'ลูกค้า | AgencySuit')

@section('content')
    <div class="as-page-head">
        <div>
            <h1 class="as-page-title">ลูกค้าของคุณ</h1>
            <p class="as-page-subtitle">{{ $clients->count() }} คนที่กำลังติดตาม</p>
        </div>
        <a href="{{ route('clients.create') }}" class="as-inline-action"><x-icon name="plus" size="18" />เพิ่มลูกค้า</a>
    </div>

    {{-- Search and Filter Form --}}
    <form method="GET" action="{{ route('clients.index') }}" class="mt-4 space-y-2.5">
        <div class="relative">
            <input type="search" name="q" value="{{ $currentSearch }}" placeholder="ค้นหาชื่อลูกค้า, เบอร์โทร, ทำเล..." class="as-input pl-10 pr-20 text-sm">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-stone-400 pointer-events-none">
                <x-icon name="search" size="16" />
            </span>
            @if ($currentSearch || $currentType !== 'all')
                <a href="{{ route('clients.index') }}" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-xs font-semibold text-stone-400 hover:text-stone-700">ล้างค้นหา</a>
            @endif
        </div>

        {{-- Filter Chips & Pipeline Link --}}
        <div class="flex items-center justify-between gap-2 overflow-x-auto pb-1">
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('clients.index', ['q' => $currentSearch, 'type' => 'all']) }}"
                    @class(['as-chip is-active' => $currentType === 'all', 'as-chip' => $currentType !== 'all'])>
                    ทั้งหมด
                </a>
                <a href="{{ route('clients.index', ['q' => $currentSearch, 'type' => 'buy']) }}"
                    @class(['as-chip is-active' => $currentType === 'buy', 'as-chip' => $currentType !== 'buy'])>
                    ซื้อ
                </a>
                <a href="{{ route('clients.index', ['q' => $currentSearch, 'type' => 'rent']) }}"
                    @class(['as-chip is-active' => $currentType === 'rent', 'as-chip' => $currentType !== 'rent'])>
                    เช่า
                </a>
            </div>
            <a href="{{ route('deals.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-teal-800 shrink-0 hover:underline">
                <x-icon name="briefcase" size="14" />
                <span>ภาพรวมดีล</span>
            </a>
        </div>
    </form>

    @if ($clients->isEmpty())
        <div class="mt-5 as-surface overflow-hidden">
            @if ($currentSearch || $currentType !== 'all')
                <x-empty-state title="ไม่พบลูกค้าที่ค้นหา" description="ลองเปลี่ยนคำค้นหา หรือกดล้างตัวกรองเพื่อดูลูกค้าทั้งหมด" action="ล้างการค้นหา" :action-url="route('clients.index')" />
            @else
                <x-empty-state title="ยังไม่มีลูกค้าในระบบ" description="เพิ่มลูกค้ารายแรก เพื่อเริ่มดูทรัพย์ที่ตรงความต้องการและตั้งงานติดตาม" action="เพิ่มลูกค้า" :action-url="route('clients.create')" />
            @endif
        </div>
    @else
        <ul class="as-list-surface mt-4">
            @foreach ($clients as $client)
                <li>
                    <a href="{{ route('clients.show', $client) }}" class="as-list-row">
                        <span class="as-icon-box mt-0.5"><x-icon name="users" size="20" /></span>
                        <span class="as-list-copy">
                            <span class="as-list-title-line">
                                <span class="as-list-title">{{ $client->name }}</span>
                                <span class="as-status">{{ $client->transactionLabel() }}</span>
                            </span>
                            <span class="as-list-meta"><strong>งบ {{ $client->formattedBudget() }}</strong></span>
                            <span class="as-list-meta"><x-icon name="map-pin" size="14" class="mr-1 inline" />{{ $client->locations }}</span>
                        </span>
                        <x-icon name="chevron-right" size="19" class="as-row-chevron" />
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
