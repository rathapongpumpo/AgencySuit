@extends('layouts.app')

@section('title', $property->name.' | AgencySuit')

@section('content')
    <a href="{{ route('properties.index') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-green-800">← ทรัพย์</a>

    @if (session('success'))
        <p role="status" class="mt-4 border-y border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900">{{ session('success') }}</p>
    @endif

    <h1 class="mt-4 text-2xl font-bold tracking-tight">{{ $property->name }}</h1>
    <p class="mt-2 text-sm text-stone-600">{{ $property->transaction_label }} · {{ $property->status_label }}</p>

    <dl class="mt-6 divide-y divide-stone-200 border-y border-stone-200 bg-white">
        <div class="flex min-h-14 items-center justify-between gap-4 px-4">
            <dt class="text-sm text-stone-600">ราคา</dt>
            <dd class="text-base font-semibold">{{ $property->formattedPrice() }}</dd>
        </div>
        <div class="flex min-h-14 items-center justify-between gap-4 px-4">
            <dt class="text-sm text-stone-600">ห้องนอน</dt>
            <dd class="text-base font-medium">{{ $property->bedrooms }} ห้อง</dd>
        </div>
        <div class="flex min-h-14 items-center justify-between gap-4 px-4">
            <dt class="text-sm text-stone-600">ทำเล</dt>
            <dd class="text-right text-base font-medium">{{ $property->location }}</dd>
        </div>
        <div class="flex min-h-14 items-center justify-between gap-4 px-4">
            <dt class="text-sm text-stone-600">สถานะ</dt>
            <dd class="text-base font-semibold text-green-800">{{ $property->status_label }}</dd>
        </div>
    </dl>

    <div class="mt-6 space-y-3">
        <a href="{{ route('properties.edit', $property) }}" class="flex min-h-12 w-full items-center justify-center rounded-xl bg-green-900 px-5 text-base font-semibold text-white">แก้ไข</a>

        <details class="border-y border-stone-200 bg-white">
            <summary class="flex min-h-12 cursor-pointer list-none items-center justify-between px-4 text-base font-semibold text-stone-800">เปลี่ยนสถานะ <span aria-hidden="true">⌄</span></summary>
            <form method="POST" action="{{ route('properties.status.update', $property) }}" class="space-y-3 border-t border-stone-100 px-4 py-4">
                @csrf
                @method('PATCH')
                <label for="status" class="text-sm font-medium">สถานะใหม่</label>
                <select id="status" name="status" class="min-h-12 w-full rounded-xl border border-stone-300 bg-white px-4 text-base focus:border-green-700 focus:ring-green-700">
                    @foreach ($statusOptions as $value => $label)
                        @php($optionLabel = is_array($label) ? $label[$property->transaction_type] : $label)
                        <option value="{{ $value }}" @selected($property->status === $value)>{{ $optionLabel }}</option>
                    @endforeach
                </select>
                @error('status')<p class="text-sm text-red-700">{{ $message }}</p>@enderror
                <button type="submit" class="min-h-12 w-full rounded-xl border border-stone-300 bg-white px-5 text-base font-semibold text-stone-800">บันทึกสถานะ</button>
            </form>
        </details>
    </div>
@endsection
