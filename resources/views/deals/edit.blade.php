@extends('layouts.app')

@section('title', 'แก้ไขดีล | AgencySuit')

@section('content')
    <a href="{{ route('deals.show', $deal) }}" class="as-back-link"><x-icon name="chevron-right" size="18" class="rotate-180" />รายละเอียดดีล</a>
    <h1 class="as-detail-title">แก้ไขดีล</h1>
    @if(session('limit_reached'))<p role="alert" class="as-alert as-alert--warning mt-5">{{ session('limit_reached') }}</p>@endif
    <form method="POST" action="{{ route('deals.update', $deal) }}" class="as-form mt-7" novalidate>@csrf @method('PUT')
        <div class="as-field"><label for="stage" class="as-field-label">สถานะ</label><select id="stage" name="stage" required class="as-select">@foreach($stages as $value=>$label)<option value="{{ $value }}" @selected(old('stage',$deal->stage)===$value)>{{ $label }}</option>@endforeach</select></div>
        <div class="as-field"><label for="property_id" class="as-field-label">ทรัพย์ (ถ้ามี)</label><select id="property_id" name="property_id" class="as-select"><option value="">ยังไม่เลือก</option>@foreach($properties as $property)<option value="{{ $property->id }}" @selected((string) old('property_id',$deal->property_id)===(string)$property->id)>{{ $property->name }}</option>@endforeach</select></div>
        <div class="as-field"><label for="amount" class="as-field-label">มูลค่าดีล (บาท)</label><input id="amount" name="amount" type="number" min="0" step="0.01" value="{{ old('amount',$deal->amount) }}" class="as-input"></div>
        <div class="as-field"><label for="commission_rate" class="as-field-label">คอมมิชชัน (%)</label><input id="commission_rate" name="commission_rate" type="number" min="0" max="100" step="0.01" value="{{ old('commission_rate',$deal->commission_rate) }}" class="as-input"></div>
        <div class="as-field"><label for="co_agent_split" class="as-field-label">ส่วนแบ่งโคเอเจนต์ (%)</label><input id="co_agent_split" name="co_agent_split" type="number" min="0" max="100" step="0.01" value="{{ old('co_agent_split',$deal->co_agent_split) }}" class="as-input"></div>
        <button type="submit" class="as-action-primary">บันทึกการแก้ไข</button>
    </form>
@endsection
