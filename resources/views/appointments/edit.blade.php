@extends('layouts.app')

@section('title', 'แก้ไขนัดดู | AgencySuit')

@section('content')
    <a href="{{ route('appointments.show', $appointment) }}" class="as-back-link"><x-icon name="chevron-right" size="18" class="rotate-180" />รายละเอียดนัดดู</a>
    <h1 class="as-detail-title">แก้ไขนัดดู</h1>
    <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="as-form mt-7" novalidate>
        @csrf @method('PUT')
        <div class="as-field"><label for="client_id" class="as-field-label">ลูกค้า</label><select id="client_id" name="client_id" required class="as-select">@foreach($clients as $client)<option value="{{ $client->id }}" @selected((int) old('client_id', $appointment->client_id) === $client->id)>{{ $client->name }}</option>@endforeach</select></div>
        <div class="as-field"><label for="property_id" class="as-field-label">ทรัพย์</label><select id="property_id" name="property_id" required class="as-select">@foreach($properties as $property)<option value="{{ $property->id }}" @selected((int) old('property_id', $appointment->property_id) === $property->id)>{{ $property->name }}</option>@endforeach</select></div>
        <div class="as-field"><label for="appointment_date" class="as-field-label">วันที่</label><input id="appointment_date" name="appointment_date" type="date" value="{{ old('appointment_date', $appointment->appointment_date->toDateString()) }}" required class="as-input"></div>
        <div class="as-field"><label for="appointment_time" class="as-field-label">เวลา</label><input id="appointment_time" name="appointment_time" type="time" value="{{ old('appointment_time', substr($appointment->appointment_time, 0, 5)) }}" required class="as-input"></div>
        <div class="as-field"><label for="note" class="as-field-label">หมายเหตุ (ถ้ามี)</label><textarea id="note" name="note" rows="2" maxlength="300" class="as-textarea">{{ old('note', $appointment->note) }}</textarea></div>
        <button type="submit" class="as-action-primary">บันทึกการแก้ไข</button>
    </form>
@endsection
