@extends('layouts.app')

@section('title', 'แก้ไขทรัพย์ | AgencySuit')

@section('content')
    <a href="{{ route('properties.show', $property) }}" class="as-back-link"><x-icon name="chevron-right" size="18" class="rotate-180" />รายละเอียดทรัพย์</a>
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

        <button type="submit" class="as-action-primary">บันทึกการแก้ไข</button>
    </form>
@endsection
