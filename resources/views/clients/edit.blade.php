@extends('layouts.app')

@section('title', 'แก้ไขลูกค้า | AgencySuit')

@section('content')
    <a href="{{ route('clients.show', $client) }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-green-800">← รายละเอียดลูกค้า</a>
    <h1 class="mt-3 text-2xl font-bold tracking-tight">แก้ไขลูกค้า</h1>
    <p class="mt-2 text-sm leading-6 text-stone-600">แก้ข้อมูลหลักได้ทันที หรือเปิดรายละเอียดเพิ่มเติมเมื่อจำเป็น</p>

    <form method="POST" action="{{ route('clients.update', $client) }}" class="mt-7 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="text-sm font-medium">ชื่อ/ชื่อเล่น</label>
            <input id="name" name="name" type="text" value="{{ old('name', $client->name) }}" required maxlength="255" autocomplete="name" class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base focus:border-green-700 focus:ring-green-700">
            @error('name')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>

        <fieldset>
            <legend class="text-sm font-medium">ต้องการ</legend>
            <div class="mt-2 grid grid-cols-2 gap-2">
                @foreach (config('clients.transaction_types') as $value => $label)
                    <label class="flex min-h-12 cursor-pointer items-center justify-center rounded-xl border border-stone-300 px-3 text-base font-medium has-[:checked]:border-green-800 has-[:checked]:bg-green-50 has-[:checked]:text-green-900 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-green-700">
                        <input type="radio" name="transaction_type" value="{{ $value }}" @checked(old('transaction_type', $client->transaction_type) === $value) class="sr-only" required>
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            @error('transaction_type')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
        </fieldset>

        <div>
            <label for="budget" class="text-sm font-medium">งบประมาณ (บาท)</label>
            <input id="budget" name="budget" type="number" value="{{ old('budget', $client->budget) }}" required min="0" step="0.01" inputmode="decimal" class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base focus:border-green-700 focus:ring-green-700">
            @error('budget')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="locations" class="text-sm font-medium">ทำเลที่สนใจ</label>
            <textarea id="locations" name="locations" rows="2" required maxlength="1000" class="mt-1.5 w-full rounded-xl border border-stone-300 px-4 py-3 text-base focus:border-green-700 focus:ring-green-700">{{ old('locations', $client->locations) }}</textarea>
            @error('locations')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>

        <details class="border-y border-stone-200 bg-white px-4" @if ($errors->hasAny(['phone', 'contact_channel', 'bedrooms', 'minimum_size', 'transit_preference', 'notes'])) open @endif>
            <summary class="flex min-h-12 cursor-pointer list-none items-center justify-between text-base font-semibold text-stone-800">รายละเอียดเพิ่มเติม <span aria-hidden="true">⌄</span></summary>
            <div class="space-y-5 border-t border-stone-100 py-4">
                <div>
                    <label for="phone" class="text-sm font-medium">เบอร์โทร (ถ้ามี)</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone', $client->phone) }}" maxlength="30" autocomplete="tel" class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base focus:border-green-700 focus:ring-green-700">
                    @error('phone')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="contact_channel" class="text-sm font-medium">LINE ID / ช่องทางติดต่อ</label>
                    <input id="contact_channel" name="contact_channel" type="text" value="{{ old('contact_channel', $client->contact_channel) }}" maxlength="100" class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base focus:border-green-700 focus:ring-green-700">
                    @error('contact_channel')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="bedrooms" class="text-sm font-medium">จำนวนห้องนอนที่ต้องการ</label>
                    <input id="bedrooms" name="bedrooms" type="number" value="{{ old('bedrooms', $client->bedrooms) }}" min="0" max="50" step="1" inputmode="numeric" class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base focus:border-green-700 focus:ring-green-700">
                    @error('bedrooms')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="minimum_size" class="text-sm font-medium">ขนาดขั้นต่ำ (ตร.ม.)</label>
                    <input id="minimum_size" name="minimum_size" type="number" value="{{ old('minimum_size', $client->minimum_size) }}" min="0" step="0.01" inputmode="decimal" class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base focus:border-green-700 focus:ring-green-700">
                    @error('minimum_size')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="transit_preference" class="text-sm font-medium">BTS/MRT หรือเงื่อนไขทำเลเพิ่มเติม</label>
                    <textarea id="transit_preference" name="transit_preference" rows="2" maxlength="1000" class="mt-1.5 w-full rounded-xl border border-stone-300 px-4 py-3 text-base focus:border-green-700 focus:ring-green-700">{{ old('transit_preference', $client->transit_preference) }}</textarea>
                    @error('transit_preference')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="notes" class="text-sm font-medium">หมายเหตุ</label>
                    <textarea id="notes" name="notes" rows="3" maxlength="2000" class="mt-1.5 w-full rounded-xl border border-stone-300 px-4 py-3 text-base focus:border-green-700 focus:ring-green-700">{{ old('notes', $client->notes) }}</textarea>
                    @error('notes')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
            </div>
        </details>

        <button type="submit" class="min-h-12 w-full rounded-xl bg-green-900 px-5 text-base font-semibold text-white">บันทึกการแก้ไข</button>
    </form>
@endsection
