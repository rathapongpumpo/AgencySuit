@extends('layouts.app')

@section('title', 'สร้างนัดดู | AgencySuit')

@section('content')
    <a href="{{ request('client_id') ? route('clients.show', request('client_id')) : route('today') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-green-800">← กลับ</a>
    <h1 class="mt-3 text-2xl font-bold tracking-tight">สร้างนัดดู</h1>
    <p class="mt-2 text-sm leading-6 text-stone-600">เลือกคน ทรัพย์ วันที่ และเวลา แล้วนัดจะขึ้นในวันนี้เมื่อถึงกำหนด</p>
    @if ($clients->isEmpty() || $properties->isEmpty())
        <p role="alert" class="mt-6 border-y border-amber-200 bg-amber-50 px-4 py-4 text-sm leading-6 text-amber-900">ต้องมีทั้งลูกค้าและทรัพย์ก่อนจึงจะสร้างนัดดูได้</p>
    @else
        <form method="POST" action="{{ route('appointments.store') }}" class="mt-7 space-y-5">
            @csrf
            <div><label for="client_id" class="text-sm font-medium">ลูกค้า</label><select id="client_id" name="client_id" required class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 bg-white px-4 text-base">@foreach($clients as $client)<option value="{{ $client->id }}" @selected((int) old('client_id', $selectedClient) === $client->id)>{{ $client->name }}</option>@endforeach</select>@error('client_id')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</div>
            <div><label for="property_id" class="text-sm font-medium">ทรัพย์</label><select id="property_id" name="property_id" required class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 bg-white px-4 text-base">@foreach($properties as $property)<option value="{{ $property->id }}" @selected((int) old('property_id', $selectedProperty) === $property->id)>{{ $property->name }}</option>@endforeach</select>@error('property_id')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</div>
            <div><label for="appointment_date" class="text-sm font-medium">วันที่</label><input id="appointment_date" name="appointment_date" type="date" value="{{ old('appointment_date', today()->toDateString()) }}" required class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base">@error('appointment_date')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</div>
            <div><label for="appointment_time" class="text-sm font-medium">เวลา</label><input id="appointment_time" name="appointment_time" type="time" value="{{ old('appointment_time', '10:00') }}" required class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base">@error('appointment_time')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</div>
            <div><label for="note" class="text-sm font-medium">หมายเหตุ (ถ้ามี)</label><textarea id="note" name="note" rows="2" maxlength="300" class="mt-1.5 w-full rounded-xl border border-stone-300 px-4 py-3 text-base">{{ old('note') }}</textarea>@error('note')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</div>
            <button type="submit" class="min-h-12 w-full rounded-xl bg-green-900 px-5 text-base font-semibold text-white">บันทึกนัดดู</button>
        </form>
    @endif
@endsection
