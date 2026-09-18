@extends('layouts.app')

@section('title', 'เพิ่มลูกค้า | AgencySuit')

@section('content')
    <a href="{{ route('clients.index') }}" class="as-back-link"><x-icon name="chevron-right" size="18" class="rotate-180" />ลูกค้า</a>
    <h1 class="as-detail-title">เพิ่มลูกค้า</h1>
    <p class="as-page-subtitle">บันทึกข้อมูลหลัก 4 ช่องก่อน รายละเอียดอื่นเพิ่มทีหลังได้</p>

    @if ($limitReached)
        <div role="alert" class="as-alert as-alert--warning mt-6">
            แพ็กเกจฟรีเพิ่มลูกค้าได้สูงสุด {{ $limit }} รายการ ข้อมูลเดิมยังอยู่ครบ
        </div>
        <a href="{{ route('clients.index') }}" class="as-action-secondary mt-5">กลับไปที่ลูกค้า</a>
    @else
        @if (session('limit_reached'))
            <p role="alert" class="as-alert as-alert--warning mt-5">{{ session('limit_reached') }}</p>
        @endif

        <form method="POST" action="{{ route('clients.store') }}" class="as-form mt-7" novalidate>
            @csrf

            <div class="as-field">
                <label for="name" class="as-field-label">ชื่อ/ชื่อเล่น</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" autocomplete="name" class="as-input">
                @error('name')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>

            <fieldset class="as-field">
                <legend class="as-field-label">ต้องการ</legend>
                <div class="as-choice-grid">
                    @foreach (config('clients.transaction_types') as $value => $label)
                        <label class="as-choice">
                            <input type="radio" name="transaction_type" value="{{ $value }}" @checked(old('transaction_type', 'buy') === $value) class="sr-only" required>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                @error('transaction_type')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
            </fieldset>

            <div class="as-field">
                <label for="budget" class="as-field-label">งบประมาณ (บาท)</label>
                <input id="budget" name="budget" type="number" value="{{ old('budget') }}" required min="0" step="0.01" inputmode="decimal" class="as-input">
                @error('budget')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>

            <div class="as-field">
                <label for="locations" class="as-field-label">ทำเลที่สนใจ</label>
                <textarea id="locations" name="locations" rows="2" required maxlength="1000" class="as-textarea">{{ old('locations') }}</textarea>
                @error('locations')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="as-action-primary">บันทึกลูกค้า</button>
        </form>
    @endif
@endsection
