@extends('layouts.app')

@section('title', $client->name.' | AgencySuit')

@section('content')
    <a href="{{ route('clients.index') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-green-800">← ลูกค้า</a>

    @if (session('success'))
        <p role="status" class="mt-4 border-y border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900">{{ session('success') }}</p>
    @endif

    <h1 class="mt-4 text-2xl font-bold tracking-tight">{{ $client->name }}</h1>
    <p class="mt-2 text-sm text-stone-600">{{ $client->transactionLabel() }}</p>

    <dl class="mt-6 divide-y divide-stone-200 border-y border-stone-200 bg-white">
        <div class="flex min-h-14 items-center justify-between gap-4 px-4">
            <dt class="text-sm text-stone-600">งบประมาณ</dt>
            <dd class="text-right text-base font-semibold">{{ $client->formattedBudget() }}</dd>
        </div>
        <div class="flex min-h-14 items-center justify-between gap-4 px-4">
            <dt class="text-sm text-stone-600">ทำเลที่สนใจ</dt>
            <dd class="max-w-[65%] text-right text-base font-medium">{{ $client->locations }}</dd>
        </div>
        @if ($client->bedrooms !== null)
            <div class="flex min-h-14 items-center justify-between gap-4 px-4">
                <dt class="text-sm text-stone-600">ห้องนอน</dt>
                <dd class="text-base font-medium">{{ $client->bedrooms }} ห้อง</dd>
            </div>
        @endif
        @if ($client->minimum_size !== null)
            <div class="flex min-h-14 items-center justify-between gap-4 px-4">
                <dt class="text-sm text-stone-600">ขนาดขั้นต่ำ</dt>
                <dd class="text-base font-medium">{{ number_format((float) $client->minimum_size, 2) }} ตร.ม.</dd>
            </div>
        @endif
        @if (filled($client->transit_preference))
            <div class="flex min-h-14 items-center justify-between gap-4 px-4">
                <dt class="text-sm text-stone-600">เงื่อนไขทำเล</dt>
                <dd class="max-w-[65%] text-right text-base font-medium">{{ $client->transit_preference }}</dd>
            </div>
        @endif
    </dl>

    <section class="mt-6" aria-labelledby="property-matches-heading">
        <div class="flex items-end justify-between gap-3">
            <h2 id="property-matches-heading" class="text-lg font-semibold">ทรัพย์ที่ตรงกับลูกค้าคนนี้</h2>
            <span class="text-sm text-stone-500">{{ $propertyMatches->count() }} รายการ</span>
        </div>
        @if ($propertyMatches->isEmpty())
            <p class="mt-3 border-y border-stone-200 bg-white px-4 py-4 text-sm leading-6 text-stone-600">ยังไม่มีทรัพย์ของคุณที่ตรงกับความต้องการนี้</p>
        @else
            <ul class="mt-3 divide-y divide-stone-200 border-y border-stone-200 bg-white">
                @foreach ($propertyMatches as $match)
                    @php($property = $match['property'])
                    <li>
                        <a href="{{ route('properties.show', $property) }}" class="block px-4 py-4 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-700">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="min-w-0 truncate text-base font-semibold">{{ $property->name }}</h3>
                                <span class="shrink-0 text-sm font-semibold text-green-800">Match {{ $match['percentage'] }}%</span>
                            </div>
                            <p class="mt-1 text-sm text-stone-600">{{ $property->transaction_label }} · {{ $property->formattedPrice() }}</p>
                            <p class="mt-1 text-sm text-stone-600">{{ $property->bedrooms }} ห้องนอน · {{ $property->location }}</p>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="mt-6 border-y border-stone-200 bg-white px-4 py-4" aria-labelledby="share-heading">
        <h2 id="share-heading" class="text-lg font-semibold">ส่งทรัพย์ให้ลูกค้า</h2>
        @if ($propertyMatches->isEmpty())
            <p class="mt-2 text-sm text-stone-600">ยังไม่มีทรัพย์ให้เลือกส่ง</p>
        @else
            <form class="mt-3 space-y-3" data-share-form>
                @foreach ($propertyMatches as $match)
                    @php($shareProperty = $match['property'])
                    <label class="flex min-h-12 items-center gap-3 border-b border-stone-100 py-2 text-sm">
                        <input type="checkbox" class="size-5 rounded border-stone-300 text-green-900 focus:ring-green-700" data-share-property data-share-name="{{ $shareProperty->name }}" data-share-type="{{ $shareProperty->transaction_label }}" data-share-price="{{ $shareProperty->formattedPrice() }}" data-share-bedrooms="{{ $shareProperty->bedrooms }}" data-share-location="{{ $shareProperty->location }}">
                        <span class="min-w-0"><span class="block truncate font-medium">{{ $shareProperty->name }}</span><span class="block text-stone-600">{{ $shareProperty->formattedPrice() }} · {{ $shareProperty->location }}</span></span>
                    </label>
                @endforeach
                <button type="submit" class="min-h-12 w-full rounded-xl bg-green-900 px-5 text-base font-semibold text-white">เตรียมข้อความแชร์</button>
                <p class="break-words text-sm text-stone-600" data-share-feedback aria-live="polite"></p>
            </form>
        @endif
    </section>

    <section class="mt-6 border-y border-stone-200 bg-white px-4 py-4" aria-labelledby="follow-up-heading">
        <h2 id="follow-up-heading" class="text-lg font-semibold">ติดตามอีกครั้ง</h2>
        <form method="POST" action="{{ route('clients.followups.store', $client) }}" class="mt-3 space-y-3">
            @csrf
            <div class="grid grid-cols-3 gap-2">
                @foreach ([1 => 'พรุ่งนี้', 3 => '3 วัน', 7 => '7 วัน'] as $days => $label)
                    <button type="submit" name="days" value="{{ $days }}" class="min-h-11 rounded-lg border border-stone-300 px-2 text-sm font-semibold text-stone-800">{{ $label }}</button>
                @endforeach
            </div>
            <label for="due_date" class="text-sm font-medium">หรือเลือกวันที่</label>
            <input id="due_date" name="due_date" type="date" class="min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base focus:border-green-700 focus:ring-green-700">
            <input name="note" type="text" maxlength="300" placeholder="หมายเหตุสั้น ๆ (ถ้ามี)" class="min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base focus:border-green-700 focus:ring-green-700">
            <button type="submit" class="min-h-12 w-full rounded-xl border border-stone-300 bg-white px-5 text-base font-semibold text-stone-800">ตั้งตามวันที่เลือก</button>
            @error('due_date')<p class="text-sm text-red-700">{{ $message }}</p>@enderror
        </form>
        @if ($client->followUps->isNotEmpty())
            <ul class="mt-4 divide-y divide-stone-100 border-t border-stone-100">
                @foreach ($client->followUps->take(5) as $followUp)
                    <li class="flex items-center justify-between gap-3 py-3 text-sm"><span class="{{ $followUp->status === 'completed' ? 'text-stone-500 line-through' : 'text-stone-800' }}">{{ $followUp->due_date->format('d/m/Y') }}{{ $followUp->note ? ' · '.$followUp->note : '' }}</span>@if ($followUp->status === 'pending')<form method="POST" action="{{ route('followups.complete', $followUp) }}">@csrf @method('PATCH')<button class="min-h-11 text-sm font-semibold text-green-800">ทำแล้ว</button></form>@else<span class="text-stone-500">เสร็จแล้ว</span>@endif</li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="mt-6 border-y border-stone-200 bg-white px-4 py-4" aria-labelledby="appointment-heading">
        <div class="flex items-center justify-between gap-3"><h2 id="appointment-heading" class="text-lg font-semibold">นัดดู</h2><a href="{{ route('clients.appointments.create', $client) }}" class="min-h-11 inline-flex items-center text-sm font-semibold text-green-800">สร้างนัดดู</a></div>
        <p class="mt-2 text-sm text-stone-600">เลือกทรัพย์ วันที่ และเวลา เพื่อเพิ่มนัดในวันนี้</p>
    </section>

    <section class="mt-6 border-y border-stone-200 bg-white px-4 py-4" aria-labelledby="deal-heading">
        <div class="flex items-center justify-between gap-3"><h2 id="deal-heading" class="text-lg font-semibold">ดีล</h2><a href="{{ route('clients.deals.create', $client) }}" class="min-h-11 inline-flex items-center text-sm font-semibold text-green-800">สร้างดีล</a></div>
        @if ($client->deals->isEmpty())
            <p class="mt-2 text-sm text-stone-600">ยังไม่มีดีลของลูกค้าคนนี้</p>
        @else
            <ul class="mt-2 divide-y divide-stone-100 border-t border-stone-100">@foreach ($client->deals->take(5) as $deal)<li><a href="{{ route('deals.show', $deal) }}" class="block py-3 text-sm"><span class="font-semibold">{{ $deal->stageLabel() }}</span>{{ $deal->amount ? ' · '.number_format((float) $deal->amount, 0).' บาท' : '' }}</a></li>@endforeach</ul>
        @endif
    </section>

    @if (filled($client->phone) || filled($client->contact_channel) || filled($client->notes))
        <section class="mt-5 border-y border-stone-200 bg-white px-4 py-4" aria-labelledby="contact-heading">
            <h2 id="contact-heading" class="text-base font-semibold">ข้อมูลติดต่อ</h2>
            @if (filled($client->phone))
                <a href="tel:{{ preg_replace('/[^+0-9]/', '', $client->phone) }}" class="mt-3 inline-flex min-h-11 items-center rounded-lg border border-stone-300 px-4 text-sm font-semibold text-stone-800">โทร {{ $client->phone }}</a>
            @endif
            @if (filled($client->contact_channel))
                <p class="mt-3 text-sm text-stone-700">LINE ID / ช่องทางติดต่อ: {{ $client->contact_channel }}</p>
            @endif
            @if (filled($client->notes))
                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-stone-700">{{ $client->notes }}</p>
            @endif
        </section>
    @endif

    <a href="{{ route('clients.edit', $client) }}" class="mt-6 flex min-h-12 w-full items-center justify-center rounded-xl bg-green-900 px-5 text-base font-semibold text-white">แก้ไข</a>
@endsection
