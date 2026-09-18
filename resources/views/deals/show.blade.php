@extends('layouts.app')

@section('title', 'รายละเอียดดีล | AgencySuit')

@section('content')
    <a href="{{ route('clients.show', $deal->client) }}" class="as-back-link"><x-icon name="chevron-right" size="18" class="rotate-180" />ลูกค้า</a>
    @if(session('success'))<p role="status" class="as-alert as-alert--success mb-5">{{ session('success') }}</p>@endif
    <div class="as-detail-hero">
        <h1 class="as-detail-title">{{ $deal->stageLabel() }}</h1>
        <p class="as-detail-subtitle">{{ $deal->client->name }}{{ $deal->property ? ' · '.$deal->property->name : '' }}</p>
    </div>
    <dl class="as-detail-list">
        <div><dt>มูลค่าดีล</dt><dd>{{ $deal->amount !== null ? number_format((float)$deal->amount,0).' บาท' : 'ยังไม่ระบุ' }}</dd></div>
        <div><dt>คอมมิชชันรวม</dt><dd>{{ number_format($deal->grossCommission(), 2) }} บาท</dd></div>
        <div><dt>คอมมิชชันของฉัน</dt><dd class="text-teal-800">{{ number_format($deal->agentCommission(), 2) }} บาท</dd></div>
    </dl>
    <a href="{{ route('deals.edit', $deal) }}" class="as-action-primary mt-6">แก้ไขดีล</a>
@endsection
