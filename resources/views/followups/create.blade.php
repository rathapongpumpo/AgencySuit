@extends('layouts.app')

@section('title', 'ติดตามอีกครั้ง | AgencySuit')

@section('content')
    <a href="{{ route('today') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-green-800">← วันนี้</a>
    <h1 class="mt-3 text-2xl font-bold tracking-tight">ติดตามอีกครั้ง</h1>
    <p class="mt-2 text-sm leading-6 text-stone-600">เลือก ลูกค้า และช่วงเวลาสั้น ๆ ระบบจะนำไปแสดงบนวันนี้</p>
    @if ($clients->isEmpty())
        <p role="alert" class="mt-6 border-y border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-900">เพิ่มลูกค้าก่อนจึงจะตั้งการติดตามได้</p>
        <a href="{{ route('clients.create') }}" class="mt-5 inline-flex min-h-12 items-center rounded-xl bg-green-900 px-5 text-base font-semibold text-white">เพิ่มลูกค้า</a>
    @else
        <form method="POST" action="{{ route('followups.store') }}" class="mt-7 space-y-5">
            @csrf
            <div><label for="client_id" class="text-sm font-medium">ลูกค้า</label><select id="client_id" name="client_id" required class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 bg-white px-4 text-base">@foreach($clients as $client)<option value="{{ $client->id }}">{{ $client->name }}</option>@endforeach</select>@error('client_id')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</div>
            <div class="grid grid-cols-3 gap-2">@foreach([1=>'พรุ่งนี้',3=>'3 วัน',7=>'7 วัน'] as $days=>$label)<button type="submit" name="days" value="{{ $days }}" class="min-h-11 rounded-lg border border-stone-300 px-2 text-sm font-semibold">{{ $label }}</button>@endforeach</div>
            <div><label for="due_date" class="text-sm font-medium">หรือเลือกวันที่</label><input id="due_date" name="due_date" type="date" class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base">@error('due_date')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</div>
            <div><label for="note" class="text-sm font-medium">หมายเหตุ (ถ้ามี)</label><input id="note" name="note" type="text" maxlength="300" class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base"></div>
            <button type="submit" class="min-h-12 w-full rounded-xl bg-green-900 px-5 text-base font-semibold text-white">บันทึกการติดตาม</button>
        </form>
    @endif
@endsection
