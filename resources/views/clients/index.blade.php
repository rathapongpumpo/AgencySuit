@extends('layouts.app')

@section('title', 'ลูกค้า | AgencySuit')

@section('content')
    <p class="text-sm font-semibold text-green-800">ลูกค้า</p>
    @if ($clients->isEmpty())
        <h1 class="mt-2 text-2xl font-bold tracking-tight">ยังไม่มีลูกค้าในระบบ</h1>
        <x-empty-state title="เพิ่มลูกค้ารายแรก" description="บันทึกความต้องการของลูกค้า เพื่อเตรียมดูทรัพย์และติดตามงานได้ง่ายขึ้น" action="เพิ่มลูกค้า" :action-url="route('clients.create')" />
    @else
        <div class="flex items-end justify-between gap-4">
            <h1 class="mt-2 text-2xl font-bold tracking-tight">ลูกค้าของคุณ</h1>
            <a href="{{ route('clients.create') }}" class="inline-flex min-h-11 shrink-0 items-center rounded-lg bg-green-900 px-4 text-sm font-semibold text-white">เพิ่มลูกค้า</a>
        </div>

        <ul class="mt-6 divide-y divide-stone-200 border-y border-stone-200 bg-white">
            @foreach ($clients as $client)
                <li>
                    <a href="{{ route('clients.show', $client) }}" class="block px-4 py-4 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-700">
                        <div class="flex items-start justify-between gap-4">
                            <h2 class="min-w-0 truncate text-base font-semibold">{{ $client->name }}</h2>
                            <span class="shrink-0 text-sm font-medium text-green-800">{{ $client->transactionLabel() }}</span>
                        </div>
                        <p class="mt-1 text-sm text-stone-600">งบ {{ $client->formattedBudget() }}</p>
                        <p class="mt-1 text-sm text-stone-600">{{ $client->locations }}</p>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
