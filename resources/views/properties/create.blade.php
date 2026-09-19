@extends('layouts.app')

@section('title', 'เพิ่มทรัพย์ | AgencySuit')

@section('content')
    <x-back-button :fallback="route('properties.index')" label="ย้อนกลับ" />
    <h1 class="as-detail-title">เพิ่มทรัพย์</h1>
    <p class="as-page-subtitle">กรอกข้อมูลหลัก 5 ช่อง แล้วแก้ไขรายละเอียดเพิ่มเติมได้ภายหลัง</p>

    @if ($limitReached)
        <div role="alert" class="as-alert as-alert--warning mt-6">
            แพ็กเกจฟรีเพิ่มทรัพย์ได้สูงสุด {{ $limit }} รายการ ข้อมูลเดิมยังอยู่ครบ
        </div>
        <a href="{{ route('properties.index') }}" class="as-action-secondary mt-5">กลับไปที่ทรัพย์</a>
    @else
        <form method="POST" action="{{ route('properties.store') }}" class="as-form mt-7" novalidate>
            @csrf

            <fieldset class="as-field">
                <legend class="as-field-label">ประเภท</legend>
                <div class="as-choice-grid">
                    @foreach (config('properties.transaction_types') as $value => $label)
                        <label class="as-choice">
                            <input type="radio" name="transaction_type" value="{{ $value }}" @checked(old('transaction_type', 'sale') === $value) class="sr-only" required>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                @error('transaction_type')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
            </fieldset>

            <div class="as-field">
                <label for="name" class="as-field-label">ชื่อโครงการหรือชื่อทรัพย์</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" autocomplete="off" class="as-input">
                @error('name')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>

            <div class="as-field">
                <label for="price" class="as-field-label">ราคา (บาท)</label>
                <input id="price" name="price" type="number" value="{{ old('price') }}" required min="0" step="0.01" inputmode="decimal" class="as-input">
                @error('price')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>

            <div class="as-field">
                <label for="bedrooms" class="as-field-label">ห้องนอน</label>
                <input id="bedrooms" name="bedrooms" type="number" value="{{ old('bedrooms') }}" required min="0" max="50" step="1" inputmode="numeric" class="as-input">
                @error('bedrooms')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>

            <div class="as-field">
                <label for="location" class="as-field-label">ทำเล</label>
                <input id="location" name="location" type="text" value="{{ old('location') }}" required maxlength="255" autocomplete="address-level2" class="as-input">
                @error('location')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>

            <details class="as-surface overflow-hidden rounded-xl border border-stone-200">
                <summary class="flex cursor-pointer items-center justify-between p-3.5 text-sm font-semibold text-[var(--as-teal)] select-none">
                    <span>+ เพิ่มข้อมูลเจ้าของและสเปกห้อง (ถ้ามี)</span>
                    <x-icon name="chevron-right" size="16" class="text-stone-400" />
                </summary>
                <div class="space-y-4 border-t border-stone-100 p-4 pt-3">
                    <p class="text-xs text-stone-500">ข้อมูลส่วนนี้ไม่บังคับ สามารถเติมหรือแก้ไขทีหลังได้</p>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="as-field">
                            <label for="owner_name" class="as-field-label text-xs">ชื่อเจ้าของทรัพย์</label>
                            <input id="owner_name" name="owner_name" type="text" value="{{ old('owner_name') }}" maxlength="255" class="as-input">
                        </div>
                        <div class="as-field">
                            <label for="owner_phone" class="as-field-label text-xs">เบอร์โทรเจ้าของ</label>
                            <input id="owner_phone" name="owner_phone" type="tel" value="{{ old('owner_phone') }}" maxlength="50" class="as-input" placeholder="08xxxxxxxx">
                        </div>
                    </div>
                    <div class="as-field">
                        <label for="owner_line" class="as-field-label text-xs">LINE ID เจ้าของ</label>
                        <input id="owner_line" name="owner_line" type="text" value="{{ old('owner_line') }}" maxlength="100" class="as-input">
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="as-field">
                            <label for="size" class="as-field-label text-xs">ขนาด (ตร.ม.)</label>
                            <input id="size" name="size" type="number" step="0.01" min="0" value="{{ old('size') }}" class="as-input" placeholder="เช่น 35">
                        </div>
                        <div class="as-field">
                            <label for="floor" class="as-field-label text-xs">ชั้น</label>
                            <input id="floor" name="floor" type="text" value="{{ old('floor') }}" maxlength="20" class="as-input" placeholder="เช่น 12A">
                        </div>
                        <div class="as-field">
                            <label for="unit_number" class="as-field-label text-xs">เลขที่ห้อง</label>
                            <input id="unit_number" name="unit_number" type="text" value="{{ old('unit_number') }}" maxlength="50" class="as-input" placeholder="เช่น 88/12">
                        </div>
                    </div>
                    <div class="as-field">
                        <label for="notes" class="as-field-label text-xs">หมายเหตุ / จุดเด่นของทรัพย์</label>
                        <textarea id="notes" name="notes" rows="2" maxlength="1000" class="as-input" placeholder="เช่น วิวแม่น้ำ เลี้ยงสัตว์ได้ รวมค่าส่วนกลางแล้ว">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </details>

            <button type="submit" class="as-action-primary">บันทึกทรัพย์</button>
        </form>
    @endif
@endsection
