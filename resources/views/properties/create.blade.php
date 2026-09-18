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

            <button type="submit" class="as-action-primary">บันทึกทรัพย์</button>
        </form>
    @endif
@endsection
