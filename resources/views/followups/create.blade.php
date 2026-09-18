@extends('layouts.app')

@section('title', 'ติดตามอีกครั้ง | AgencySuit')

@section('content')
    <a href="{{ route('today') }}" class="as-back-link"><x-icon name="chevron-right" size="18" class="rotate-180" />วันนี้</a>
    <h1 class="as-detail-title">ติดตามอีกครั้ง</h1>
    <p class="as-page-subtitle">เลือก ลูกค้า และช่วงเวลาสั้น ๆ ระบบจะนำไปแสดงบนวันนี้</p>
    @if ($clients->isEmpty())
        <p role="alert" class="as-alert as-alert--warning mt-6">เพิ่มลูกค้าก่อนจึงจะตั้งการติดตามได้</p>
        <a href="{{ route('clients.create') }}" class="as-action-primary mt-5">เพิ่มลูกค้า</a>
    @else
        <form method="POST" action="{{ route('followups.store') }}" class="as-form mt-7" novalidate>
            @csrf
            <div class="as-field"><label for="client_id" class="as-field-label">ลูกค้า</label><select id="client_id" name="client_id" required class="as-select">@foreach($clients as $client)<option value="{{ $client->id }}">{{ $client->name }}</option>@endforeach</select>@error('client_id')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</div>
            <div class="as-choice-grid">@foreach([1=>'พรุ่งนี้',3=>'3 วัน',7=>'7 วัน'] as $days=>$label)<button type="submit" name="days" value="{{ $days }}" class="as-choice">{{ $label }}</button>@endforeach</div>
            <div class="as-field"><label for="due_date" class="as-field-label">หรือเลือกวันที่</label><input id="due_date" name="due_date" type="date" class="as-input">@error('due_date')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</div>
            <div class="as-field"><label for="note" class="as-field-label">หมายเหตุ (ถ้ามี)</label><input id="note" name="note" type="text" maxlength="300" class="as-input"></div>
            <button type="submit" class="as-action-primary">บันทึกการติดตาม</button>
        </form>
    @endif
@endsection
