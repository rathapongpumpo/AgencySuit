@extends('layouts.app')

@section('title', 'แก้ไขลูกค้า | AgencySuit')

@section('content')
    <x-back-button :fallback="route('clients.show', $client)" label="ย้อนกลับ" />
    <h1 class="as-detail-title">แก้ไขลูกค้า</h1>
    <p class="as-page-subtitle">แก้ข้อมูลหลักได้ทันที หรือเปิดรายละเอียดเพิ่มเติมเมื่อจำเป็น</p>

    <form method="POST" action="{{ route('clients.update', $client) }}" class="as-form mt-7" novalidate>
        @csrf
        @method('PUT')

        <div class="as-field">
            <label for="name" class="as-field-label">ชื่อ/ชื่อเล่น</label>
            <input id="name" name="name" type="text" value="{{ old('name', $client->name) }}" required maxlength="255" autocomplete="name" class="as-input">
            @error('name')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>

        <fieldset class="as-field">
            <legend class="as-field-label">ต้องการ</legend>
            <div class="as-choice-grid">
                @foreach (config('clients.transaction_types') as $value => $label)
                    <label class="as-choice">
                        <input type="radio" name="transaction_type" value="{{ $value }}" @checked(old('transaction_type', $client->transaction_type) === $value) class="sr-only" required>
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            @error('transaction_type')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
        </fieldset>

        <div class="as-field">
            <label for="budget" class="as-field-label">งบประมาณ (บาท)</label>
            <input id="budget" name="budget" type="number" value="{{ old('budget', $client->budget) }}" required min="0" step="0.01" inputmode="decimal" class="as-input">
            @error('budget')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>

        <div class="as-field">
            <label for="locations" class="as-field-label">ทำเลที่สนใจ</label>
            <textarea id="locations" name="locations" rows="2" required maxlength="1000" class="as-textarea">{{ old('locations', $client->locations) }}</textarea>
            @error('locations')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>

        <details class="as-surface px-4" @if ($errors->hasAny(['phone', 'contact_channel', 'bedrooms', 'minimum_size', 'transit_preference', 'notes'])) open @endif>
            <summary class="flex min-h-12 cursor-pointer list-none items-center justify-between text-base font-bold text-stone-800">รายละเอียดเพิ่มเติม <span aria-hidden="true">⌄</span></summary>
            <div class="space-y-5 border-t border-stone-100 py-4">
                <div>
                    <label for="phone" class="as-field-label">เบอร์โทร (ถ้ามี)</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone', $client->phone) }}" maxlength="30" autocomplete="tel" class="as-input">
                    @error('phone')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="contact_channel" class="as-field-label">LINE ID / ช่องทางติดต่อ</label>
                    <input id="contact_channel" name="contact_channel" type="text" value="{{ old('contact_channel', $client->contact_channel) }}" maxlength="100" class="as-input">
                    @error('contact_channel')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="bedrooms" class="as-field-label">จำนวนห้องนอนที่ต้องการ</label>
                    <input id="bedrooms" name="bedrooms" type="number" value="{{ old('bedrooms', $client->bedrooms) }}" min="0" max="50" step="1" inputmode="numeric" class="as-input">
                    @error('bedrooms')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="minimum_size" class="as-field-label">ขนาดขั้นต่ำ (ตร.ม.)</label>
                    <input id="minimum_size" name="minimum_size" type="number" value="{{ old('minimum_size', $client->minimum_size) }}" min="0" step="0.01" inputmode="decimal" class="as-input">
                    @error('minimum_size')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="transit_preference" class="as-field-label">BTS/MRT หรือเงื่อนไขทำเลเพิ่มเติม</label>
                    <textarea id="transit_preference" name="transit_preference" rows="2" maxlength="1000" class="as-textarea">{{ old('transit_preference', $client->transit_preference) }}</textarea>
                    @error('transit_preference')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="notes" class="as-field-label">หมายเหตุ</label>
                    <textarea id="notes" name="notes" rows="3" maxlength="2000" class="as-textarea">{{ old('notes', $client->notes) }}</textarea>
                    @error('notes')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
            </div>
        </details>

        <button type="submit" class="as-action-primary">บันทึกการแก้ไข</button>
    </form>

    <div class="mt-8 border-t border-stone-200 pt-6">
        <button type="button" class="as-action-danger" onclick="document.getElementById('delete-client-dialog').showModal()">ลบข้อมูลลูกค้านี้</button>
    </div>

    <dialog id="delete-client-dialog" class="as-confirm-dialog" aria-labelledby="del-client-title">
        <h3 id="del-client-title" class="as-section-title">ต้องการลบลูกค้ารายนี้?</h3>
        <p class="as-page-subtitle">ข้อมูลของ "{{ $client->name }}" นัดหมาย และรายการติดตามทั้งหมดจะถูกลบออกจากระบบ</p>
        <div class="mt-5 grid grid-cols-2 gap-2">
            <button type="button" class="as-action-secondary" onclick="document.getElementById('delete-client-dialog').close()">ยกเลิก</button>
            <form method="POST" action="{{ route('clients.destroy', $client) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="as-action-danger">ยืนยันลบ</button>
            </form>
        </div>
    </dialog>
@endsection
