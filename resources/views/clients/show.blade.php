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
