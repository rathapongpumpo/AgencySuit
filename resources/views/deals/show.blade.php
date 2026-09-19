@extends('layouts.app')

@section('title', 'รายละเอียดดีล | AgencySuit')

@section('content')
    <x-back-button :fallback="route('clients.show', $deal->client)" label="ย้อนกลับ" />

    @if(session('success'))
        <p role="status" class="as-alert as-alert--success mb-5">{{ session('success') }}</p>
    @endif

    <div class="as-detail-hero">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h1 class="as-detail-title">{{ $deal->stageLabel() }}</h1>
                <p class="as-detail-subtitle">
                    <a href="{{ route('clients.show', $deal->client) }}" class="as-text-link font-bold">{{ $deal->client->name }}</a>
                    @if($deal->property) · <a href="{{ route('properties.show', $deal->property) }}" class="as-text-link">{{ $deal->property->name }}</a>@endif
                </p>
            </div>
            <span class="as-status shrink-0 font-bold">{{ $deal->stageLabel() }}</span>
        </div>
    </div>

    <dl class="as-detail-list mt-4">
        <div>
            <dt>ลูกค้า</dt>
            <dd><a href="{{ route('clients.show', $deal->client) }}" class="as-text-link font-bold">{{ $deal->client->name }}</a></dd>
        </div>
        @if($deal->property)
            <div>
                <dt>ทรัพย์</dt>
                <dd><a href="{{ route('properties.show', $deal->property) }}" class="as-text-link font-bold">{{ $deal->property->name }}</a></dd>
            </div>
        @endif
        <div>
            <dt>มูลค่าดีล</dt>
            <dd class="text-base font-bold">{{ $deal->amount !== null ? number_format((float)$deal->amount, 0).' บาท' : 'ยังไม่ระบุ' }}</dd>
        </div>
        <div>
            <dt>คอมมิชชันรวม</dt>
            <dd>{{ number_format($deal->grossCommission(), 2) }} บาท</dd>
        </div>
        <div>
            <dt>คอมมิชชันของฉัน</dt>
            <dd class="text-base font-extrabold text-[var(--as-teal)]">{{ number_format($deal->agentCommission(), 2) }} บาท</dd>
        </div>
    </dl>

    <div class="mt-6">
        <a href="{{ route('deals.edit', $deal) }}" class="as-action-primary">แก้ไขดีล</a>
    </div>

    <div class="mt-8 border-t border-stone-200 pt-6">
        <button type="button" class="as-action-danger" onclick="document.getElementById('del-deal-dialog').showModal()">ลบดีลนี้</button>
    </div>

    <dialog id="del-deal-dialog" class="as-confirm-dialog" aria-labelledby="del-deal-title">
        <h3 id="del-deal-title" class="as-section-title">ต้องการลบดีลนี้?</h3>
        <p class="as-page-subtitle">ดีลนี้จะถูกลบออกจากระบบอย่างถาวร</p>
        <div class="mt-5 grid grid-cols-2 gap-2">
            <button type="button" class="as-action-secondary" onclick="document.getElementById('del-deal-dialog').close()">ยกเลิก</button>
            <form method="POST" action="{{ route('deals.destroy', $deal) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="as-action-danger">ยืนยันลบ</button>
            </form>
        </div>
    </dialog>
@endsection
