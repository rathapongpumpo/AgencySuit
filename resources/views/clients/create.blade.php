@extends('layouts.app')

@section('title', 'เพิ่มลูกค้า | AgencySuit')

@section('content')
    <a href="{{ route('clients.index') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-green-800">← ลูกค้า</a>
    <h1 class="mt-3 text-2xl font-bold tracking-tight">เพิ่มลูกค้า</h1>
    <p class="mt-2 text-sm leading-6 text-stone-600">บันทึกข้อมูลหลัก 4 ช่องก่อน รายละเอียดอื่นเพิ่มทีหลังได้</p>

    @if ($limitReached)
        <div role="alert" class="mt-6 border-y border-amber-200 bg-amber-50 px-4 py-4 text-sm leading-6 text-amber-900">
            แพ็กเกจฟรีเพิ่มลูกค้าได้สูงสุด {{ $limit }} รายการ ข้อมูลเดิมยังอยู่ครบ
        </div>
        <a href="{{ route('clients.index') }}" class="mt-5 inline-flex min-h-12 items-center rounded-xl border border-stone-300 bg-white px-5 text-base font-semibold text-stone-800">กลับไปที่ลูกค้า</a>
    @else
        @if (session('limit_reached'))
            <p role="alert" class="mt-5 border-y border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-900">{{ session('limit_reached') }}</p>
        @endif

        <form method="POST" action="{{ route('clients.store') }}" class="mt-7 space-y-5">
            @csrf

            <div>
                <label for="name" class="text-sm font-medium">ชื่อ/ชื่อเล่น</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" autocomplete="name" class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base focus:border-green-700 focus:ring-green-700">
                @error('name')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>

            <fieldset>
                <legend class="text-sm font-medium">ต้องการ</legend>
                <div class="mt-2 grid grid-cols-2 gap-2">
                    @foreach (config('clients.transaction_types') as $value => $label)
                        <label class="flex min-h-12 cursor-pointer items-center justify-center rounded-xl border border-stone-300 px-3 text-base font-medium has-[:checked]:border-green-800 has-[:checked]:bg-green-50 has-[:checked]:text-green-900 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-green-700">
                            <input type="radio" name="transaction_type" value="{{ $value }}" @checked(old('transaction_type', 'buy') === $value) class="sr-only" required>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                @error('transaction_type')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
            </fieldset>

            <div>
                <label for="budget" class="text-sm font-medium">งบประมาณ (บาท)</label>
                <input id="budget" name="budget" type="number" value="{{ old('budget') }}" required min="0" step="0.01" inputmode="decimal" class="mt-1.5 min-h-12 w-full rounded-xl border border-stone-300 px-4 text-base focus:border-green-700 focus:ring-green-700">
                @error('budget')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="locations" class="text-sm font-medium">ทำเลที่สนใจ</label>
                <textarea id="locations" name="locations" rows="2" required maxlength="1000" class="mt-1.5 w-full rounded-xl border border-stone-300 px-4 py-3 text-base focus:border-green-700 focus:ring-green-700">{{ old('locations') }}</textarea>
                @error('locations')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="min-h-12 w-full rounded-xl bg-green-900 px-5 text-base font-semibold text-white">บันทึกลูกค้า</button>
        </form>
    @endif
@endsection
