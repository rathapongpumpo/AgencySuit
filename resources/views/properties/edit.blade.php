@extends('layouts.app')

@section('title', 'แก้ไขทรัพย์ | AgencySuit')

@section('content')
    <x-back-button :fallback="route('properties.show', $property)" label="ย้อนกลับ" />
    <h1 class="as-detail-title">แก้ไขทรัพย์</h1>
    <p class="as-page-subtitle">แก้เฉพาะข้อมูลหลักของทรัพย์รายการนี้</p>

    <form method="POST" action="{{ route('properties.update', $property) }}" class="as-form mt-7" novalidate>
        @csrf
        @method('PUT')

        <fieldset class="as-field">
            <legend class="as-field-label">ประเภท</legend>
            <div class="as-choice-grid">
                @foreach (config('properties.transaction_types') as $value => $label)
                    <label class="as-choice">
                        <input type="radio" name="transaction_type" value="{{ $value }}" @checked(old('transaction_type', $property->transaction_type) === $value) class="sr-only" required>
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            @error('transaction_type')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
        </fieldset>

        <div class="as-field">
            <label for="name" class="as-field-label">ชื่อโครงการหรือชื่อทรัพย์</label>
            <input id="name" name="name" type="text" value="{{ old('name', $property->name) }}" required maxlength="255" autocomplete="off" class="as-input">
            @error('name')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>

        <div class="as-field">
            <label for="price" class="as-field-label">ราคา (บาท)</label>
            <input id="price" name="price" type="number" value="{{ old('price', $property->price) }}" required min="0" step="0.01" inputmode="decimal" class="as-input">
            @error('price')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>

        <div class="as-field">
            <label for="bedrooms" class="as-field-label">ห้องนอน</label>
            <input id="bedrooms" name="bedrooms" type="number" value="{{ old('bedrooms', $property->bedrooms) }}" required min="0" max="50" step="1" inputmode="numeric" class="as-input">
            @error('bedrooms')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>

        <div class="as-field">
            <label for="location" class="as-field-label">ทำเล</label>
            <input id="location" name="location" type="text" value="{{ old('location', $property->location) }}" required maxlength="255" autocomplete="address-level2" class="as-input">
            @error('location')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>

        <div class="mt-6 border-t border-stone-100 pt-5">
            <h2 class="as-field-label text-base font-bold text-stone-800">ข้อมูลเจ้าของและสเปกห้อง</h2>
            <div class="mt-3 space-y-4">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="as-field">
                        <label for="owner_name" class="as-field-label">ชื่อเจ้าของทรัพย์</label>
                        <input id="owner_name" name="owner_name" type="text" value="{{ old('owner_name', $property->owner_name) }}" maxlength="255" class="as-input">
                    </div>
                    <div class="as-field">
                        <label for="owner_phone" class="as-field-label">เบอร์โทรเจ้าของ</label>
                        <input id="owner_phone" name="owner_phone" type="tel" value="{{ old('owner_phone', $property->owner_phone) }}" maxlength="50" class="as-input" placeholder="08xxxxxxxx">
                    </div>
                </div>
                <div class="as-field">
                    <label for="owner_line" class="as-field-label">LINE ID เจ้าของ</label>
                    <input id="owner_line" name="owner_line" type="text" value="{{ old('owner_line', $property->owner_line) }}" maxlength="100" class="as-input">
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div class="as-field">
                        <label for="size" class="as-field-label">ขนาด (ตร.ม.)</label>
                        <input id="size" name="size" type="number" step="0.01" min="0" value="{{ old('size', $property->size) }}" class="as-input" placeholder="เช่น 35">
                    </div>
                    <div class="as-field">
                        <label for="floor" class="as-field-label">ชั้น</label>
                        <input id="floor" name="floor" type="text" value="{{ old('floor', $property->floor) }}" maxlength="20" class="as-input" placeholder="เช่น 12A">
                    </div>
                    <div class="as-field">
                        <label for="unit_number" class="as-field-label">เลขที่ห้อง</label>
                        <input id="unit_number" name="unit_number" type="text" value="{{ old('unit_number', $property->unit_number) }}" maxlength="50" class="as-input" placeholder="เช่น 88/12">
                    </div>
                </div>
                <div class="as-field">
                    <label for="notes" class="as-field-label">หมายเหตุ / จุดเด่นของทรัพย์</label>
                    <textarea id="notes" name="notes" rows="2" maxlength="1000" class="as-input" placeholder="เช่น วิวแม่น้ำ เลี้ยงสัตว์ได้ รวมค่าส่วนกลางแล้ว">{{ old('notes', $property->notes) }}</textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="as-action-primary">บันทึกการแก้ไข</button>
    </form>

    <div class="mt-8 border-t border-stone-200 pt-6">
        <button type="button" class="as-action-danger" onclick="document.getElementById('delete-property-dialog').showModal()">ลบทรัพย์นี้</button>
    </div>

    <dialog id="delete-property-dialog" class="as-confirm-dialog" aria-labelledby="del-title">
        <h3 id="del-title" class="as-section-title">ต้องการลบทรัพย์นี้?</h3>
        <p class="as-page-subtitle">ทรัพย์ "{{ $property->name }}" และรูปภาพทั้งหมดจะถูกลบออกจากระบบ</p>
        <div class="mt-5 grid grid-cols-2 gap-2">
            <button type="button" class="as-action-secondary" onclick="document.getElementById('delete-property-dialog').close()">ยกเลิก</button>
            <form method="POST" action="{{ route('properties.destroy', $property) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="as-action-danger">ยืนยันลบ</button>
            </form>
        </div>
    </dialog>
@endsection
