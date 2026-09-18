@extends('layouts.app')

@section('title', 'สร้างนัดดู | AgencySuit')

@section('content')
    <a href="{{ request('client_id') ? route('clients.show', request('client_id')) : route('today') }}" class="as-back-link"><x-icon name="chevron-right" size="18" class="rotate-180" />กลับ</a>
    <h1 class="as-detail-title">สร้างนัดดู</h1>
    <p class="as-page-subtitle">เลือกคน ทรัพย์ วันที่ และเวลา แล้วนัดจะขึ้นในวันนี้เมื่อถึงกำหนด</p>
    @if ($clients->isEmpty() || $properties->isEmpty())
        <p role="alert" class="as-alert as-alert--warning mt-6">ต้องมีทั้งลูกค้าและทรัพย์ก่อนจึงจะสร้างนัดดูได้</p>
    @else
        <form method="POST" action="{{ route('appointments.store') }}" class="as-form mt-7" novalidate>
            @csrf
            <div class="as-field"><label for="client_id" class="as-field-label">ลูกค้า</label><select id="client_id" name="client_id" required class="as-select">@foreach($clients as $client)<option value="{{ $client->id }}" @selected((int) old('client_id', $selectedClient) === $client->id)>{{ $client->name }}</option>@endforeach</select>@error('client_id')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</div>
            <div class="as-field"><label for="property_id" class="as-field-label">ทรัพย์</label><select id="property_id" name="property_id" required class="as-select">@foreach($properties as $property)<option value="{{ $property->id }}" @selected((int) old('property_id', $selectedProperty) === $property->id)>{{ $property->name }}</option>@endforeach</select>@error('property_id')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</div>
            <div class="as-field"><label for="appointment_date" class="as-field-label">วันที่</label><input id="appointment_date" name="appointment_date" type="date" value="{{ old('appointment_date', today()->toDateString()) }}" required class="as-input">@error('appointment_date')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</div>
            <div class="as-field"><label for="appointment_time" class="as-field-label">เวลา</label><input id="appointment_time" name="appointment_time" type="time" value="{{ old('appointment_time', '10:00') }}" required class="as-input">@error('appointment_time')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</div>
            <div class="as-field"><label for="note" class="as-field-label">หมายเหตุ (ถ้ามี)</label><textarea id="note" name="note" rows="2" maxlength="300" class="as-textarea">{{ old('note') }}</textarea>@error('note')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</div>
            <button type="submit" class="as-action-primary">บันทึกนัดดู</button>
        </form>
    @endif
@endsection
