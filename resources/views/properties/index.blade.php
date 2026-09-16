@extends('layouts.app')

@section('title', 'ทรัพย์ | AgencySuit')

@section('content')
    <p class="text-sm font-semibold text-green-800">ทรัพย์</p>
    @if ($properties->isEmpty())
        <h1 class="mt-2 text-2xl font-bold tracking-tight">ยังไม่มีทรัพย์ในระบบ</h1>
        <x-empty-state title="เพิ่มทรัพย์แรก" description="บันทึกทรัพย์ที่ดูแลไว้ เพื่อให้พร้อมจับคู่และติดตามลูกค้าในขั้นตอนถัดไป" action="เพิ่มทรัพย์" :action-url="route('properties.create')" />
    @else
        <h1 class="mt-2 text-2xl font-bold tracking-tight">ทรัพย์ของคุณ</h1>
        <div class="mt-6">
            <div class="flex items-center justify-between gap-4 border-b border-stone-200 pb-3">
                <a href="{{ route('properties.create') }}" class="inline-flex min-h-11 items-center rounded-lg bg-green-900 px-4 text-sm font-semibold text-white">เพิ่มทรัพย์</a>
            </div>
            <ul class="divide-y divide-stone-200">
                @foreach ($properties as $property)
                    <li>
                        <a href="{{ route('properties.show', $property) }}" class="block py-4 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-700">
                            <div class="flex items-start justify-between gap-4">
                                <h3 class="text-base font-semibold">{{ $property->name }}</h3>
                                <span class="shrink-0 text-sm font-medium text-green-800">{{ $property->status_label }}</span>
                            </div>
                            <p class="mt-1 text-sm text-stone-600">{{ $property->transaction_label }} · {{ $property->formattedPrice() }}</p>
                            <p class="mt-1 text-sm text-stone-600">{{ $property->bedrooms }} ห้องนอน · {{ $property->location }}</p>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
