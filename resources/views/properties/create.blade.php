@extends('layouts.app')

@section('title', 'เพิ่มทรัพย์ | AgencySuit')

@section('content')
    <a href="{{ route('properties.index') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-green-800">← ทรัพย์</a>
    <h1 class="mt-3 text-2xl font-bold tracking-tight">เพิ่มทรัพย์</h1>
    <p class="mt-2 text-sm leading-6 text-stone-600">กรอกข้อมูลหลัก 5 ช่อง แล้วแก้ไขรายละเอียดเพิ่มเติมได้ภายหลัง</p>

    @if ($limitReached)
        <div role="alert" class="mt-6 border-y border-amber-200 bg-amber-50 px-4 py-4 text-sm leading-6 text-amber-900">
            แพ็กเกจฟรีเพิ่มทรัพย์ได้สูงสุด {{ $limit }} รายการ ข้อมูลเดิมยังอยู่ครบ
        </div>
        <a href="{{ route('properties.index') }}" class="mt-5 inline-flex min-h-12 items-center rounded-xl border border-stone-300 bg-white px-5 text-base font-semibold text-stone-800">กลับไปที่ทรัพย์</a>
    @else
        <form method="POST" action="{{ route('properties.store') }}" class="mt-7 space-y-5">
            @csrf

            <fieldset>
                <legend class="text-sm font-medium">ประเภท</legend>
                <div class="mt-2 grid grid-cols-2 gap-2">
                    @foreach (config('properties.transaction_types') as $value => $label)
                        <label class="flex min-h-12 cursor-pointer items-center justify-center rounded-xl border border-stone-300 px-3 text-base font-medium has-[:checked]:border-green-800 has-[:checked]:bg-green-50 has-[:checked]:text-green-900 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-green-700">
                            <input type="radio" name="transaction_type" value="{{ $value }}" @checked(old('transaction_type', 'sale') === $value) class="sr-only" required>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                @error('transaction_type')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
            </fieldset>

            <div>
                <label for="name" class="text-sm font-medium">ชื่อโครงการหรือชื่อทรัพย์</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" autocomplete="off" class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base focus:border-green-700 focus:ring-green-700">
                @error('name')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="price" class="text-sm font-medium">ราคา (บาท)</label>
                <input id="price" name="price" type="number" value="{{ old('price') }}" required min="0" step="0.01" inputmode="decimal" class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base focus:border-green-700 focus:ring-green-700">
                @error('price')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="bedrooms" class="text-sm font-medium">ห้องนอน</label>
                <input id="bedrooms" name="bedrooms" type="number" value="{{ old('bedrooms') }}" required min="0" max="50" step="1" inputmode="numeric" class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base focus:border-green-700 focus:ring-green-700">
                @error('bedrooms')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="location" class="text-sm font-medium">ทำเล</label>
                <input id="location" name="location" type="text" value="{{ old('location') }}" required maxlength="255" autocomplete="address-level2" class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base focus:border-green-700 focus:ring-green-700">
                @error('location')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="min-h-12 w-full rounded-xl bg-green-900 px-5 text-base font-semibold text-white">บันทึกทรัพย์</button>
        </form>
    @endif
@endsection
