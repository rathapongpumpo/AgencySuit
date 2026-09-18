@extends('layouts.app')

@section('title', 'ทรัพย์ | AgencySuit')

@section('content')
    <div class="as-page-head">
        <div>
            <h1 class="as-page-title">ทรัพย์ของคุณ</h1>
            <p class="as-page-subtitle">{{ $properties->count() }} รายการที่กำลังดูแล</p>
        </div>
        <a href="{{ route('properties.create') }}" class="as-inline-action"><x-icon name="plus" size="18" />เพิ่มทรัพย์</a>
    </div>

    {{-- Search and Filter Form --}}
    <form method="GET" action="{{ route('properties.index') }}" class="mt-4 space-y-2.5">
        <div class="relative">
            <input type="search" name="q" value="{{ $currentSearch }}" placeholder="ค้นหาชื่อทรัพย์, ทำเล, หรือเลขห้อง..." class="as-input pl-10 pr-20 text-sm">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-stone-400 pointer-events-none">
                <x-icon name="search" size="16" />
            </span>
            @if ($currentSearch || $currentType !== 'all' || $currentStatus !== 'all')
                <a href="{{ route('properties.index') }}" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-xs font-semibold text-stone-400 hover:text-stone-700">ล้างค้นหา</a>
            @endif
        </div>

        {{-- Filter Chips --}}
        <div class="flex gap-2 overflow-x-auto pb-1">
            <a href="{{ route('properties.index', ['q' => $currentSearch, 'type' => 'all', 'status' => $currentStatus !== 'all' ? $currentStatus : null]) }}"
                @class(['as-chip is-active' => $currentType === 'all', 'as-chip' => $currentType !== 'all'])>
                ทั้งหมด
            </a>
            <a href="{{ route('properties.index', ['q' => $currentSearch, 'type' => 'sale', 'status' => $currentStatus !== 'all' ? $currentStatus : null]) }}"
                @class(['as-chip is-active' => $currentType === 'sale', 'as-chip' => $currentType !== 'sale'])>
                ขาย
            </a>
            <a href="{{ route('properties.index', ['q' => $currentSearch, 'type' => 'rent', 'status' => $currentStatus !== 'all' ? $currentStatus : null]) }}"
                @class(['as-chip is-active' => $currentType === 'rent', 'as-chip' => $currentType !== 'rent'])>
                เช่า
            </a>
            <a href="{{ route('properties.index', ['q' => $currentSearch, 'type' => $currentType !== 'all' ? $currentType : null, 'status' => $currentStatus === 'available' ? null : 'available']) }}"
                @class(['as-chip is-active' => $currentStatus === 'available', 'as-chip' => $currentStatus !== 'available'])>
                เฉพาะห้องว่าง
            </a>
        </div>
    </form>

    @if ($properties->isEmpty())
        <div class="mt-5 as-surface overflow-hidden">
            @if ($currentSearch || $currentType !== 'all' || $currentStatus !== 'all')
                <x-empty-state title="ไม่พบทรัพย์ที่ค้นหา" description="ลองเปลี่ยนคำค้นหา หรือกดล้างตัวกรองเพื่อดูทรัพย์ทั้งหมด" action="ล้างการค้นหา" :action-url="route('properties.index')" />
            @else
                <x-empty-state title="ยังไม่มีทรัพย์ในระบบ" description="เพิ่มทรัพย์แรก เพื่อให้พร้อมจับคู่และติดตามลูกค้าในขั้นตอนถัดไป" action="เพิ่มทรัพย์" :action-url="route('properties.create')" />
            @endif
        </div>
    @else
        <ul class="as-list-surface mt-4">
            @foreach ($properties as $property)
                @php($primaryPhoto = $property->primaryPhoto ?? $property->photos->first())
                <li>
                    <a href="{{ route('properties.show', $property) }}" class="as-list-row">
                        @if ($primaryPhoto)
                            <img src="{{ route('properties.photos.thumbnail', [$property, $primaryPhoto]) }}" alt="" class="as-media-thumb" loading="lazy">
                        @else
                            <span class="as-media-placeholder"><x-icon name="building" size="23" /></span>
                        @endif
                        <span class="as-list-copy">
                            <span class="as-list-title-line">
                                <span class="as-list-title">{{ $property->name }}</span>
                                <span class="as-status">{{ $property->status_label }}</span>
                            </span>
                            <span class="as-list-meta">
                                <strong>{{ $property->transaction_label }}</strong> · {{ $property->formattedPrice() }}
                                @if ($property->size) · {{ $property->formattedSize() }} @endif
                            </span>
                            <span class="as-list-meta">
                                <x-icon name="building" size="14" class="mr-1 inline" />{{ $property->bedrooms }} ห้องนอน
                                <span class="mx-1 text-stone-300">·</span>
                                <x-icon name="map-pin" size="14" class="mr-1 inline" />{{ $property->location }}
                            </span>
                        </span>
                        <x-icon name="chevron-right" size="19" class="as-row-chevron" />
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
