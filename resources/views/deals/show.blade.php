@extends('layouts.app')

@section('title', 'รายละเอียดดีล | AgencySuit')

@section('content')
    <a href="{{ route('clients.show', $deal->client) }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-green-800">← ลูกค้า</a>
    @if(session('success'))<p role="status" class="mt-4 border-y border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900">{{ session('success') }}</p>@endif
    <h1 class="mt-4 text-2xl font-bold tracking-tight">{{ $deal->stageLabel() }}</h1>
    <p class="mt-2 text-sm text-stone-600">{{ $deal->client->name }}{{ $deal->property ? ' · '.$deal->property->name : '' }}</p>
    <dl class="mt-6 divide-y divide-stone-200 border-y border-stone-200 bg-white"><div class="flex min-h-14 items-center justify-between gap-4 px-4"><dt class="text-sm text-stone-600">มูลค่าดีล</dt><dd class="font-semibold">{{ $deal->amount !== null ? number_format((float)$deal->amount,0).' บาท' : 'ยังไม่ระบุ' }}</dd></div><div class="flex min-h-14 items-center justify-between gap-4 px-4"><dt class="text-sm text-stone-600">คอมมิชชันรวม</dt><dd class="font-semibold">{{ number_format($deal->grossCommission(), 2) }} บาท</dd></div><div class="flex min-h-14 items-center justify-between gap-4 px-4"><dt class="text-sm text-stone-600">คอมมิชชันของฉัน</dt><dd class="font-semibold text-green-800">{{ number_format($deal->agentCommission(), 2) }} บาท</dd></div></dl>
    <a href="{{ route('deals.edit', $deal) }}" class="mt-6 flex min-h-12 w-full items-center justify-center rounded-xl bg-green-900 px-5 text-base font-semibold text-white">แก้ไขดีล</a>
@endsection
