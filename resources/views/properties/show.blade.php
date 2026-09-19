@extends('layouts.app')

@section('title', $property->name.' | AgencySuit')

@section('content')
    <x-back-button :fallback="route('properties.index')" label="ย้อนกลับ" />

    @if (session('success'))
        <p role="status" class="as-alert as-alert--success mb-5">{{ session('success') }}</p>
    @endif

    @php($primaryPhoto = $property->primaryPhoto ?? $property->photos->first())
    @if ($primaryPhoto)
        <div class="mb-4 overflow-hidden rounded-2xl border border-stone-200 shadow-sm">
            <img src="{{ route('properties.photos.file', [$property, $primaryPhoto]) }}" alt="{{ $property->name }}" class="h-48 w-full object-cover">
        </div>
    @endif

    <div class="as-detail-hero">
        <div class="flex items-start justify-between gap-3">
            <h1 class="as-detail-title">{{ $property->name }}</h1>
            <span class="as-status shrink-0 text-sm font-bold">{{ $property->status_label }}</span>
        </div>
        <p class="as-detail-subtitle">{{ $property->transaction_label }} · {{ $property->location }}</p>
    </div>

    {{-- Owner Contact Bar --}}
    @if ($property->owner_phone || $property->owner_line || $property->owner_name)
        <div class="mt-4 rounded-xl border border-stone-200 bg-white p-3.5 shadow-sm">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <span class="text-xs font-semibold text-stone-500">เจ้าของทรัพย์</span>
                    <p class="truncate text-sm font-bold text-stone-900">{{ $property->owner_name ?: 'ไม่ระบุชื่อ' }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    @if ($property->owner_phone)
                        <a href="tel:{{ $property->owner_phone }}" class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-teal-800 px-3 text-xs font-bold text-white hover:bg-teal-900 active:scale-95">
                            <x-icon name="phone" size="14" />
                            <span>โทร</span>
                        </a>
                    @endif
                    @if ($property->owner_line)
                        <a href="https://line.me/R/ti/p/~{{ urlencode($property->owner_line) }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-stone-300 bg-stone-50 px-3 text-xs font-bold text-stone-800 hover:bg-stone-100 active:scale-95">
                            <x-icon name="chat" size="14" />
                            <span>LINE</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Specs Card --}}
    <dl class="as-detail-list mt-4">
        <div>
            <dt>ราคา</dt>
            <dd class="text-base font-extrabold text-[var(--as-teal)]">{{ $property->formattedPrice() }}</dd>
        </div>
        <div>
            <dt>ห้องนอน</dt>
            <dd>{{ $property->bedrooms }} ห้องนอน</dd>
        </div>
        @if ($property->size)
            <div>
                <dt>ขนาดห้อง</dt>
                <dd>{{ $property->formattedSize() }}</dd>
            </div>
        @endif
        @if ($property->floor || $property->unit_number)
            <div>
                <dt>ชั้น / เลขที่ห้อง</dt>
                <dd>{{ $property->floor ? 'ชั้น '.$property->floor : '' }}{{ $property->floor && $property->unit_number ? ' · ' : '' }}{{ $property->unit_number ? 'ห้อง '.$property->unit_number : '' }}</dd>
            </div>
        @endif
        <div>
            <dt>ทำเล</dt>
            <dd>{{ $property->location }}</dd>
        </div>
        @if ($property->notes)
            <div>
                <dt>หมายเหตุ</dt>
                <dd class="text-stone-700">{{ $property->notes }}</dd>
            </div>
        @endif
        <div>
            <dt>สถานะ</dt>
            <dd>
                <form method="POST" action="{{ route('properties.status.update', $property) }}" class="inline-flex items-center gap-2">
                    @csrf
                    @method('PATCH')
                    <select name="status" aria-label="เปลี่ยนสถานะ" onchange="this.form.submit()" class="rounded-lg border border-stone-300 bg-stone-50 px-2.5 py-1 text-xs font-semibold text-stone-800">
                        @foreach ($statusOptions as $value => $label)
                            @php($optionLabel = is_array($label) ? $label[$property->transaction_type] : $label)
                            <option value="{{ $value }}" @selected($property->status === $value)>{{ $optionLabel }}</option>
                        @endforeach
                    </select>
                </form>
            </dd>
        </div>
    </dl>

    {{-- Action Buttons --}}
    <div class="mt-4 grid grid-cols-2 gap-2">
        <a href="{{ route('properties.edit', $property) }}" class="as-action-primary">แก้ไขทรัพย์</a>
        <a href="{{ route('appointments.create', ['property_id' => $property->id]) }}" class="as-action-secondary">สร้างนัดดู</a>
    </div>

    <div class="mt-2">
        <button type="button" data-share-single-property
            data-name="{{ $property->name }}"
            data-type="{{ $property->transaction_label }}"
            data-price="{{ $property->formattedPrice() }}"
            data-bedrooms="{{ $property->bedrooms }} ห้องนอน"
            data-size="{{ $property->formattedSize() ?? '' }}"
            data-location="{{ $property->location }}"
            data-notes="{{ $property->notes ?? '' }}"
            class="as-action-secondary flex w-full items-center justify-center gap-2 font-semibold">
            <x-icon name="share" size="16" />
            <span>แชร์ข้อมูลทรัพย์นี้</span>
        </button>
        <p class="mt-1.5 text-center text-xs text-stone-600" data-share-single-feedback aria-live="polite"></p>
    </div>

    {{-- Matching Clients --}}
    <section class="as-work-section mt-6" aria-labelledby="client-matches-heading">
        <div class="as-section-head">
            <h2 id="client-matches-heading" class="as-section-title">ลูกค้าที่ตรงกับทรัพย์นี้</h2>
            <span class="as-count">{{ $clientMatches->count() }}</span>
        </div>
        @if ($clientMatches->isEmpty())
            <p class="as-empty-copy px-4 py-4 text-sm text-stone-500">ยังไม่มีลูกค้าที่ตรงกับเงื่อนไขของทรัพย์นี้</p>
        @else
            <ul>
                @foreach ($clientMatches as $match)
                    @php($client = $match['client'])
                    <li>
                        <a href="{{ route('clients.show', $client) }}" class="as-work-row">
                            <span class="as-check"><x-icon name="users" size="15" /></span>
                            <div class="as-row-body">
                                <span class="as-row-title">{{ $client->name }}</span>
                                <span class="as-row-meta">{{ $client->transactionLabel() }} · งบ {{ $client->formattedBudget() }} · {{ $client->locations }}</span>
                            </div>
                            <span class="as-status shrink-0 font-bold">Match {{ $match['percentage'] }}%</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    {{-- Photos Section (Collapsible) --}}
    @php($photoErrors = collect($errors->messages())
        ->filter(fn (array $messages, string $key): bool => $key === 'photos' || str_starts_with($key, 'photos.'))
        ->flatten()
        ->unique()
        ->values()
        ->all())
    <details class="as-surface mt-6 overflow-hidden" @if($photoErrors || $property->photos->isEmpty()) open @endif>
        <summary class="flex min-h-12 cursor-pointer list-none items-center justify-between px-4 py-3 text-base font-bold text-stone-800">
            <span>รูปภาพทรัพย์ ({{ $property->photos->count() }}/{{ $photoLimit }})</span>
            <span class="text-xs font-normal text-stone-500">แตะเพื่อจัดการ ⌄</span>
        </summary>
        <div class="border-t border-stone-100 px-4 py-4">
            @if ($property->photos->isNotEmpty())
                <div class="grid grid-cols-3 gap-2" data-photo-grid>
                    @foreach ($property->photos as $photo)
                        <figure class="min-w-0">
                            <img src="{{ route('properties.photos.thumbnail', [$property, $photo]) }}" alt="รูป {{ $loop->iteration }} ของ {{ $property->name }}" class="aspect-square w-full rounded-lg object-cover" loading="lazy">
                            <figcaption class="mt-1 space-y-1">
                                @if ($photo->is_primary)
                                    <p class="as-text-link text-[11px] font-bold">ภาพหลัก</p>
                                @else
                                    <form method="POST" action="{{ route('properties.photos.primary', [$property, $photo]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="as-text-link min-h-8 text-left text-xs underline underline-offset-2">ตั้งเป็นหลัก</button>
                                    </form>
                                @endif
                                <button type="button" class="min-h-8 text-left text-xs font-medium text-red-600 underline underline-offset-2" data-photo-delete-open="photo-delete-{{ $photo->id }}">ลบรูป</button>
                                <dialog id="photo-delete-{{ $photo->id }}" class="as-confirm-dialog">
                                    <h3 class="as-section-title">ลบรูปนี้?</h3>
                                    <p class="as-page-subtitle">รูปจะถูกนำออกจากทรัพย์ {{ $property->name }}</p>
                                    <div class="mt-5 grid grid-cols-2 gap-2">
                                        <button type="button" class="as-action-secondary" data-photo-delete-close autofocus>ยกเลิก</button>
                                        <form method="POST" action="{{ route('properties.photos.destroy', [$property, $photo]) }}" data-photo-destroy>
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
                <form method="POST" action="{{ route('properties.photos.store', $property) }}" enctype="multipart/form-data" class="mt-4 space-y-3" data-photo-upload>
                    @csrf
                    <label for="photos" class="block text-sm font-medium text-stone-800">เพิ่มรูปภาพใหม่</label>
                    <input id="photos" name="photos[]" type="file" accept="image/jpeg,image/png,image/webp" multiple data-photo-input class="block min-h-12 w-full rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-stone-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold">
                    <div class="grid grid-cols-3 gap-2" data-photo-preview aria-live="polite"></div>
                    @if ($photoErrors)
                        <div role="alert" class="border-l-4 border-red-600 bg-red-50 px-3 py-2 text-sm text-red-800">
                            @foreach ($photoErrors as $photoError)
                                <p>{{ $photoError }}</p>
                            @endforeach
                        </div>
                    @endif
                    <p class="text-xs text-stone-500">JPG, PNG หรือ WebP · ไม่เกิน {{ (int) config('photos.max_upload_kb') / 1024 }} MB ต่อไฟล์</p>
                    <button type="submit" class="as-action-secondary">อัปโหลดรูป</button>
                </form>
            @else
                <p class="mt-3 rounded-lg bg-stone-50 px-3 py-2 text-xs text-stone-600">ครบ {{ $photoLimit }} รูปแล้วตามแพ็กเกจ</p>
            @endif
        </div>
    </details>
@endsection
