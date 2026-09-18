@extends('layouts.app')

@section('title', 'สร้างดีล | AgencySuit')

@section('content')
    <a href="{{ route('clients.show', $client) }}" class="as-back-link"><x-icon name="chevron-right" size="18" class="rotate-180" />ลูกค้า</a>
    <h1 class="as-detail-title">สร้างดีล</h1>
    @if(session('limit_reached'))<p role="alert" class="as-alert as-alert--warning mt-5">{{ session('limit_reached') }}</p>@endif
    <form method="POST" action="{{ route('clients.deals.store', $client) }}" class="as-form mt-7" novalidate>@csrf
        <div class="as-field"><label for="stage" class="as-field-label">สถานะ</label><select id="stage" name="stage" required class="as-select">@foreach($stages as $value=>$label)<option value="{{ $value }}" @selected(old('stage','new')===$value)>{{ $label }}</option>@endforeach</select>@error('stage')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</div>
        <div class="as-field"><label for="property_id" class="as-field-label">ทรัพย์ (ถ้ามี)</label><select id="property_id" name="property_id" class="as-select"><option value="">ยังไม่เลือก</option>@foreach($properties as $property)<option value="{{ $property->id }}" @selected((string) old('property_id') === (string) $property->id)>{{ $property->name }}</option>@endforeach</select></div>
        <div class="as-field"><label for="amount" class="as-field-label">มูลค่าดีล (บาท)</label><input id="amount" name="amount" type="number" min="0" step="0.01" inputmode="decimal" value="{{ old('amount') }}" class="as-input"></div>
        <div class="as-field"><label for="commission_rate" class="as-field-label">คอมมิชชัน (%)</label><input id="commission_rate" name="commission_rate" type="number" min="0" max="100" step="0.01" inputmode="decimal" value="{{ old('commission_rate') }}" class="as-input"></div>
        <div class="as-field"><label for="co_agent_split" class="as-field-label">ส่วนแบ่งโคเอเจนต์ (%)</label><input id="co_agent_split" name="co_agent_split" type="number" min="0" max="100" step="0.01" inputmode="decimal" value="{{ old('co_agent_split', 0) }}" class="as-input"></div>
        <button type="submit" class="as-action-primary">บันทึกดีล</button>
    </form>
@endsection
