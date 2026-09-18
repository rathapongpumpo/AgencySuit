@extends('layouts.app')

@section('title', 'ทรัพย์ | AgencySuit')

@section('content')
    @if ($properties->isEmpty())
        <div class="as-page-head">
            <div>
                <h1 class="as-page-title">ทรัพย์</h1>
                <p class="as-page-subtitle">เริ่มจากทรัพย์ที่คุณกำลังดูแล</p>
            </div>
        </div>
        <div class="as-surface overflow-hidden">
            <x-empty-state title="ยังไม่มีทรัพย์ในระบบ" description="เพิ่มทรัพย์แรก เพื่อให้พร้อมจับคู่และติดตามลูกค้าในขั้นตอนถัดไป" action="เพิ่มทรัพย์" :action-url="route('properties.create')" />
        </div>
    @else
        <div class="as-page-head">
            <div>
                <h1 class="as-page-title">ทรัพย์ของคุณ</h1>
                <p class="as-page-subtitle">{{ $properties->count() }} รายการที่กำลังดูแล</p>
            </div>
            <a href="{{ route('properties.create') }}" class="as-inline-action"><x-icon name="plus" size="18" />เพิ่มทรัพย์</a>
        </div>

        <ul class="as-list-surface">
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
                            <span class="as-list-meta"><strong>{{ $property->transaction_label }}</strong> · {{ $property->formattedPrice() }}</span>
                            <span class="as-list-meta"><x-icon name="building" size="14" class="mr-1 inline" />{{ $property->bedrooms }} ห้องนอน <span class="mx-1 text-stone-300">·</span> <x-icon name="map-pin" size="14" class="mr-1 inline" />{{ $property->location }}</span>
                        </span>
                        <x-icon name="chevron-right" size="19" class="as-row-chevron" />
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
