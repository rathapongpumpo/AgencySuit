@extends('layouts.app')

@section('title', 'แก้ไขนัดดู | AgencySuit')

@section('content')
    <a href="{{ route('appointments.show', $appointment) }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-green-800">← รายละเอียดนัดดู</a>
    <h1 class="mt-3 text-2xl font-bold tracking-tight">แก้ไขนัดดู</h1>
    <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="mt-7 space-y-5">
        @csrf @method('PUT')
        <div><label for="client_id" class="text-sm font-medium">ลูกค้า</label><select id="client_id" name="client_id" required class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 bg-white px-4 text-base">@foreach($clients as $client)<option value="{{ $client->id }}" @selected((int) old('client_id', $appointment->client_id) === $client->id)>{{ $client->name }}</option>@endforeach</select></div>
        <div><label for="property_id" class="text-sm font-medium">ทรัพย์</label><select id="property_id" name="property_id" required class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 bg-white px-4 text-base">@foreach($properties as $property)<option value="{{ $property->id }}" @selected((int) old('property_id', $appointment->property_id) === $property->id)>{{ $property->name }}</option>@endforeach</select></div>
        <div><label for="appointment_date" class="text-sm font-medium">วันที่</label><input id="appointment_date" name="appointment_date" type="date" value="{{ old('appointment_date', $appointment->appointment_date->toDateString()) }}" required class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base"></div>
        <div><label for="appointment_time" class="text-sm font-medium">เวลา</label><input id="appointment_time" name="appointment_time" type="time" value="{{ old('appointment_time', substr($appointment->appointment_time, 0, 5)) }}" required class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base"></div>
        <div><label for="note" class="text-sm font-medium">หมายเหตุ (ถ้ามี)</label><textarea id="note" name="note" rows="2" maxlength="300" class="mt-1.5 w-full rounded-xl border border-stone-300 px-4 py-3 text-base">{{ old('note', $appointment->note) }}</textarea></div>
        <button type="submit" class="min-h-12 w-full rounded-xl bg-green-900 px-5 text-base font-semibold text-white">บันทึกการแก้ไข</button>
    </form>
@endsection
