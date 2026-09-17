@extends('layouts.app')

@section('title', 'สร้างดีล | AgencySuit')

@section('content')
    <a href="{{ route('clients.show', $client) }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-green-800">← ลูกค้า</a>
    <h1 class="mt-3 text-2xl font-bold tracking-tight">สร้างดีล</h1>
    @if(session('limit_reached'))<p role="alert" class="mt-5 border-y border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">{{ session('limit_reached') }}</p>@endif
    <form method="POST" action="{{ route('clients.deals.store', $client) }}" class="mt-7 space-y-5">@csrf
        <div><label for="stage" class="text-sm font-medium">สถานะ</label><select id="stage" name="stage" required class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 bg-white px-4 text-base">@foreach($stages as $value=>$label)<option value="{{ $value }}" @selected(old('stage','new')===$value)>{{ $label }}</option>@endforeach</select>@error('stage')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</div>
        <div><label for="property_id" class="text-sm font-medium">ทรัพย์ (ถ้ามี)</label><select id="property_id" name="property_id" class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 bg-white px-4 text-base"><option value="">ยังไม่เลือก</option>@foreach($properties as $property)<option value="{{ $property->id }}" @selected((string) old('property_id') === (string) $property->id)>{{ $property->name }}</option>@endforeach</select></div>
        <div><label for="amount" class="text-sm font-medium">มูลค่าดีล (บาท)</label><input id="amount" name="amount" type="number" min="0" step="0.01" inputmode="decimal" value="{{ old('amount') }}" class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base"></div>
        <div><label for="commission_rate" class="text-sm font-medium">คอมมิชชัน (%)</label><input id="commission_rate" name="commission_rate" type="number" min="0" max="100" step="0.01" inputmode="decimal" value="{{ old('commission_rate') }}" class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base"></div>
        <div><label for="co_agent_split" class="text-sm font-medium">ส่วนแบ่งโคเอเจนต์ (%)</label><input id="co_agent_split" name="co_agent_split" type="number" min="0" max="100" step="0.01" inputmode="decimal" value="{{ old('co_agent_split', 0) }}" class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base"></div>
        <button type="submit" class="min-h-12 w-full rounded-xl bg-green-900 px-5 text-base font-semibold text-white">บันทึกดีล</button>
    </form>
@endsection
