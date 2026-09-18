@extends('layouts.app')

@section('title', $property->name.' | AgencySuit')

@section('content')
    <a href="{{ route('properties.index') }}" class="as-back-link"><x-icon name="chevron-right" size="18" class="rotate-180" />ทรัพย์</a>

    @if (session('success'))
        <p role="status" class="as-alert as-alert--success mb-5">{{ session('success') }}</p>
    @endif

    <div class="as-detail-hero">
        <h1 class="as-detail-title">{{ $property->name }}</h1>
        <p class="as-detail-subtitle">{{ $property->transaction_label }} · {{ $property->status_label }}</p>
    </div>

    @php($photoErrors = collect($errors->messages())
        ->filter(fn (array $messages, string $key): bool => $key === 'photos' || str_starts_with($key, 'photos.'))
        ->flatten()
        ->unique()
        ->values()
        ->all())
    <section class="as-surface mt-6 px-4 py-4" aria-labelledby="photos-heading">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 id="photos-heading" class="as-section-title">รูปทรัพย์</h2>
                <p class="mt-1 text-xs text-stone-500">{{ $property->photos->count() }}/{{ $photoLimit }} รูป · รูปแรกเป็นภาพหลักอัตโนมัติ</p>
            </div>
        </div>

        @if ($property->photos->isNotEmpty())
            <div class="mt-4 grid grid-cols-3 gap-2" data-photo-grid>
                @foreach ($property->photos as $photo)
                    <figure class="min-w-0">
                        <img src="{{ route('properties.photos.thumbnail', [$property, $photo]) }}" alt="รูป {{ $loop->iteration }} ของ {{ $property->name }}" class="aspect-square w-full rounded-lg object-cover" loading="lazy">
                        <figcaption class="mt-1 space-y-1">
                            @if ($photo->is_primary)
                                <p class="as-text-link text-[11px]">ภาพหลัก</p>
                            @else
                                <form method="POST" action="{{ route('properties.photos.primary', [$property, $photo]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="as-text-link min-h-9 text-left text-xs underline underline-offset-2">ตั้งเป็นภาพหลัก</button>
                                </form>
                            @endif
                            <button type="button" class="min-h-9 text-left text-xs font-medium text-red-700 underline underline-offset-2" data-photo-delete-open="photo-delete-{{ $photo->id }}">ลบรูป</button>
                            <dialog id="photo-delete-{{ $photo->id }}" aria-labelledby="photo-delete-title-{{ $photo->id }}" aria-describedby="photo-delete-copy-{{ $photo->id }}" class="as-confirm-dialog">
                                <h3 id="photo-delete-title-{{ $photo->id }}" class="as-section-title">ลบรูปนี้?</h3>
                                <p id="photo-delete-copy-{{ $photo->id }}" class="as-page-subtitle">รูปจะถูกนำออกจากทรัพย์ {{ $property->name }}</p>
                                <div class="mt-5 grid grid-cols-2 gap-2">
                                    <button type="button" class="as-action-secondary" data-photo-delete-close autofocus>ยกเลิก</button>
                                    <form method="POST" action="{{ route('properties.photos.destroy', [$property, $photo]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="as-action-danger">ลบรูป</button>
                                    </form>
                                </div>
                            </dialog>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        @endif

        @if ($photoLimit === null || $property->photos->count() < $photoLimit)
            <form method="POST" action="{{ route('properties.photos.store', $property) }}" enctype="multipart/form-data" class="mt-5 space-y-3" data-photo-upload>
                @csrf
                <label for="photos" class="block text-sm font-medium text-stone-800">เพิ่มรูป</label>
                <input id="photos" name="photos[]" type="file" accept="image/jpeg,image/png,image/webp" multiple data-photo-input class="block min-h-12 w-full rounded-xl border border-stone-300 bg-white px-3 py-3 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-stone-100 file:px-3 file:py-2 file:text-sm file:font-semibold">
                <div class="grid grid-cols-3 gap-2" data-photo-preview aria-live="polite"></div>
                @if ($photoErrors)
                    <div role="alert" class="border-l-4 border-red-600 bg-red-50 px-3 py-3 text-sm text-red-800">
                        @foreach ($photoErrors as $photoError)
                            <p>{{ $photoError }}</p>
                        @endforeach
                    </div>
                @endif
                <p class="text-xs text-stone-500">JPG, PNG หรือ WebP · ไม่เกิน {{ (int) config('photos.max_upload_kb') / 1024 }} MB ต่อไฟล์</p>
                <button type="submit" class="as-action-primary">อัปโหลดรูป</button>
            </form>
        @else
            <p class="mt-5 rounded-lg bg-stone-50 px-3 py-3 text-sm text-stone-700">ครบ {{ $photoLimit }} รูปแล้ว รูปเดิมยังอยู่ครบ ลบรูปที่ไม่ใช้ก่อนหากต้องการเพิ่มรูปใหม่</p>
        @endif
    </section>

    <dl class="as-detail-list mt-6">
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
            <dd class="as-text-link text-base">{{ $property->status_label }}</dd>
        </div>
    </dl>

    <section class="as-work-section mt-6" aria-labelledby="client-matches-heading">
        <div class="flex items-end justify-between gap-3">
            <h2 id="client-matches-heading" class="as-section-title">ลูกค้าที่ตรงกับทรัพย์นี้</h2>
            <span class="text-sm text-stone-500">{{ $clientMatches->count() }} รายการ</span>
        </div>
        @if ($clientMatches->isEmpty())
            <p class="as-empty-copy px-4 py-4">ยังไม่มีลูกค้าของคุณที่ตรงกับทรัพย์นี้</p>
        @else
            <ul class="divide-y divide-stone-200">
                @foreach ($clientMatches as $match)
                    @php($client = $match['client'])
                    <li>
                        <a href="{{ route('clients.show', $client) }}" class="as-list-row">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="as-list-title">{{ $client->name }}</h3>
                                <span class="as-status">Match {{ $match['percentage'] }}%</span>
                            </div>
                            <p class="mt-1 text-sm text-stone-600">{{ $client->transactionLabel() }} · งบ {{ $client->formattedBudget() }}</p>
                            <p class="mt-1 text-sm text-stone-600">{{ $client->locations }}</p>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <div class="mt-6 space-y-3">
        <a href="{{ route('properties.edit', $property) }}" class="as-action-primary">แก้ไข</a>

        <details class="as-surface">
            <summary class="flex min-h-12 cursor-pointer list-none items-center justify-between px-4 text-base font-bold text-stone-800">เปลี่ยนสถานะ <span aria-hidden="true">⌄</span></summary>
            <form method="POST" action="{{ route('properties.status.update', $property) }}" class="space-y-3 border-t border-stone-100 px-4 py-4">
                @csrf
                @method('PATCH')
                <label for="status" class="text-sm font-medium">สถานะใหม่</label>
                <select id="status" name="status" class="as-select">
                    @foreach ($statusOptions as $value => $label)
                        @php($optionLabel = is_array($label) ? $label[$property->transaction_type] : $label)
                        <option value="{{ $value }}" @selected($property->status === $value)>{{ $optionLabel }}</option>
                    @endforeach
                </select>
                @error('status')<p class="text-sm text-red-700">{{ $message }}</p>@enderror
                <button type="submit" class="as-action-secondary">บันทึกสถานะ</button>
            </form>
        </details>
    </div>
@endsection
