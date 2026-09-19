@extends('layouts.app')

@section('title', $client->name.' | AgencySuit')

@section('content')
    <x-back-button :fallback="route('clients.index')" label="ย้อนกลับ" />

    @if (session('success'))
        <p role="status" class="as-alert as-alert--success mb-5">{{ session('success') }}</p>
    @endif

    {{-- Hero & Contact --}}
    <div class="as-detail-hero">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h1 class="as-detail-title">{{ $client->name }}</h1>
                <p class="as-detail-subtitle">{{ $client->transactionLabel() }} · งบ {{ $client->formattedBudget() }}</p>
            </div>
            <a href="{{ route('clients.edit', $client) }}" class="as-inline-action shrink-0 text-xs">แก้ไข</a>
        </div>
    </div>

    {{-- 1-Tap Quick Contact Bar --}}
    @if (filled($client->phone) || filled($client->contact_channel))
        <div class="mt-4 grid grid-cols-1 gap-2 @if(filled($client->phone) && filled($client->contact_channel)) sm:grid-cols-2 @endif">
            @if (filled($client->phone))
                <a href="tel:{{ preg_replace('/[^+0-9]/', '', $client->phone) }}" class="as-action-primary flex items-center justify-center gap-2">
                    <x-icon name="phone" size="18" />
                    <span>โทร {{ $client->phone }}</span>
                </a>
            @endif
            @if (filled($client->contact_channel))
                <div class="as-surface flex min-h-12 items-center justify-between px-3 py-2 text-sm">
                    <span class="text-stone-500">LINE / ติดต่อ:</span>
                    <span class="font-bold text-stone-800">{{ $client->contact_channel }}</span>
                </div>
            @endif
        </div>
    @endif

    {{-- Specs Summary Card --}}
    <dl class="as-detail-list mt-4">
        <div>
            <dt>งบประมาณ</dt>
            <dd class="text-base font-extrabold text-[var(--as-teal)]">{{ $client->formattedBudget() }}</dd>
        </div>
        <div>
            <dt>ทำเลที่สนใจ</dt>
            <dd>{{ $client->locations }}</dd>
        </div>
        @if ($client->bedrooms !== null)
            <div>
                <dt>ห้องนอน</dt>
                <dd>{{ $client->bedrooms }} ห้อง</dd>
            </div>
        @endif
        @if ($client->minimum_size !== null)
            <div>
                <dt>ขนาดขั้นต่ำ</dt>
                <dd>{{ number_format((float) $client->minimum_size, 2) }} ตร.ม.</dd>
            </div>
        @endif
        @if (filled($client->transit_preference))
            <div>
                <dt>เงื่อนไขทำเล</dt>
                <dd>{{ $client->transit_preference }}</dd>
            </div>
        @endif
        @if (filled($client->notes))
            <div>
                <dt>หมายเหตุ</dt>
                <dd>{{ $client->notes }}</dd>
            </div>
        @endif
    </dl>

    {{-- Quick Action Buttons --}}
    <div class="mt-4 grid grid-cols-2 gap-2">
        <a href="{{ route('clients.appointments.create', $client) }}" class="as-action-secondary">
            <x-icon name="calendar" size="16" />
            <span>นัดดูทรัพย์</span>
        </a>
        <a href="{{ route('clients.deals.create', $client) }}" class="as-action-secondary">
            <x-icon name="briefcase" size="16" />
            <span>สร้างดีล</span>
        </a>
    </div>

    {{-- Matching Properties --}}
    <section class="as-work-section mt-6" aria-labelledby="property-matches-heading">
        <div class="as-section-head">
            <h2 id="property-matches-heading" class="as-section-title">ทรัพย์ที่ตรงกับลูกค้านี้</h2>
            <span class="as-count">{{ $propertyMatches->count() }}</span>
        </div>
        @if ($propertyMatches->isEmpty())
            <p class="as-empty-copy px-4 py-4 text-sm text-stone-500">ยังไม่มีทรัพย์ในระบบที่ตรงกับความต้องการนี้</p>
        @else
            <ul>
                @foreach ($propertyMatches as $match)
                    @php
                        $property = $match['property'];
                        $primaryPhoto = $property->primaryPhoto;
                    @endphp
                    <li>
                        <a href="{{ route('properties.show', $property) }}" class="as-work-row">
                            @if ($primaryPhoto)
                                <img src="{{ route('properties.photos.thumbnail', [$property, $primaryPhoto]) }}" alt="" class="as-media-thumb" loading="lazy">
                            @else
                                <span class="as-media-placeholder"><x-icon name="building" size="20" /></span>
                            @endif
                            <div class="as-row-body">
                                <span class="as-row-title">{{ $property->name }}</span>
                                <span class="as-row-meta">{{ $property->transaction_label }} · {{ $property->formattedPrice() }} · {{ $property->location }}</span>
                            </div>
                            <span class="as-status shrink-0 font-bold">Match {{ $match['percentage'] }}%</span>
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- Quick Share Drawer/Form --}}
            <div class="border-t border-stone-100 p-4">
                <form data-share-form class="space-y-3">
                    <p class="text-xs font-bold text-stone-700">เลือกทรัพย์ที่ต้องการส่งให้ลูกค้า:</p>
                    @foreach ($propertyMatches as $match)
                        @php
                            $shareProperty = $match['property'];
                        @endphp
                        <label class="flex items-center gap-2 text-sm text-stone-700">
                            <input type="checkbox" class="size-4 rounded accent-[var(--as-teal)] text-[var(--as-teal)]" data-share-property data-share-name="{{ $shareProperty->name }}" data-share-type="{{ $shareProperty->transaction_label }}" data-share-price="{{ $shareProperty->formattedPrice() }}" data-share-bedrooms="{{ $shareProperty->bedrooms }}" data-share-location="{{ $shareProperty->location }}">
                            <span class="truncate">{{ $shareProperty->name }} ({{ $shareProperty->formattedPrice() }})</span>
                        </label>
                    @endforeach
                    <button type="submit" class="as-action-secondary mt-2">เตรียมข้อความแชร์</button>
                    <p class="text-xs text-stone-600" data-share-feedback aria-live="polite"></p>
                </form>
            </div>
        @endif
    </section>

    {{-- Follow-Up Section --}}
    <section class="as-surface mt-6 p-4" aria-labelledby="followup-heading">
        <h2 id="followup-heading" class="as-section-title">ตั้งเวลาติดตามงาน</h2>
        <form method="POST" action="{{ route('clients.followups.store', $client) }}" class="mt-3 space-y-3" id="add-followup-form">
            @csrf
            <div class="grid grid-cols-3 gap-2">
                @foreach ([1 => 'พรุ่งนี้', 3 => '3 วัน', 7 => '7 วัน'] as $days => $label)
                    <button type="submit" name="days" value="{{ $days }}" class="as-choice font-semibold">{{ $label }}</button>
                @endforeach
            </div>
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                <div>
                    <label for="due_date" class="as-field-label text-xs">หรือระบุวันที่</label>
                    <input id="due_date" name="due_date" type="date" class="as-input mt-1">
                </div>
                <div>
                    <label for="note" class="as-field-label text-xs">หมายเหตุสั้นๆ</label>
                    <input id="note" name="note" type="text" maxlength="300" placeholder="เช่น โทรสอบถามการตัดสินใจ" class="as-input mt-1">
                </div>
            </div>
            <button type="submit" class="as-action-secondary">บันทึกวันติดตาม</button>
            @error('due_date')<p class="text-xs text-red-700">{{ $message }}</p>@enderror
        </form>

        @php
            $pendingFollowUps = $client->followUps->where('status', 'pending');
            $completedFollowUps = $client->followUps->where('status', 'completed');
        @endphp

        <div class="mt-4 border-t border-stone-100 pt-3" id="followup-list-container">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-stone-700">รายการที่ต้องติดตาม (<span id="followup-pending-count">{{ $pendingFollowUps->count() }}</span>)</p>
                <form method="POST" action="{{ route('clients.followups.destroy-all', $client) }}" id="destroy-all-followups-form" data-followup-destroy-all @if($pendingFollowUps->isEmpty() && $completedFollowUps->isEmpty()) style="display: none;" @endif>
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="text-xs font-medium text-[var(--as-coral)] hover:text-red-400 p-1"
                    >
                        ล้างทั้งหมด
                    </button>
                </form>
            </div>

            <p id="no-pending-followup" class="mt-2 text-xs text-stone-400 @if($pendingFollowUps->isNotEmpty()) hidden @endif">ไม่มีรายการค้างติดตาม</p>

            <ul id="pending-followup-list" class="mt-2 divide-y divide-stone-100">
                @foreach ($pendingFollowUps as $followUp)
                    <li class="flex items-center justify-between py-2 text-sm" id="followup-row-{{ $followUp->id }}" data-followup-id="{{ $followUp->id }}" data-due-date="{{ $followUp->due_date->toDateString() }}">
                        <div class="min-w-0 pr-2">
                            <span class="font-semibold text-stone-800 followup-date">{{ $followUp->due_date->format('d/m/Y') }}</span>
                            @if($followUp->note)<span class="block truncate text-xs text-stone-500 followup-note">{{ $followUp->note }}</span>@endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <form method="POST" action="{{ route('followups.complete', $followUp) }}" data-followup-complete>
                                @csrf
                                @method('PATCH')
                                <button class="as-inline-action px-2 py-1 text-xs" type="submit">ทำแล้ว</button>
                            </form>
                            <form method="POST" action="{{ route('followups.destroy', $followUp) }}" data-followup-destroy>
                                @csrf
                                @method('DELETE')
                                <button class="text-xs text-[var(--as-coral)] hover:text-red-400 p-1" type="submit" title="ลบรายการนี้">
                                    <x-icon name="trash" size="14" />
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>

            @if ($completedFollowUps->isNotEmpty())
                <details class="mt-3 border-t border-stone-100 pt-2 text-xs" id="completed-followups-details">
                    <summary class="cursor-pointer font-medium text-stone-500 hover:text-stone-700 py-1">
                        ดูประวัติที่เสร็จแล้ว (<span id="followup-completed-count">{{ $completedFollowUps->count() }}</span> รายการ)
                    </summary>
                    <ul id="completed-followup-list" class="mt-1 divide-y divide-stone-100 text-stone-400">
                        @foreach ($completedFollowUps as $followUp)
                            <li class="flex items-center justify-between py-1.5" id="followup-row-{{ $followUp->id }}" data-followup-id="{{ $followUp->id }}">
                                <div class="min-w-0 pr-2">
                                    <span class="line-through text-stone-400">{{ $followUp->due_date->format('d/m/Y') }}</span>
                                    @if($followUp->note)<span class="block truncate text-[11px] text-stone-400">{{ $followUp->note }}</span>@endif
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-[11px] text-emerald-600 font-medium">เสร็จแล้ว</span>
                                    <form method="POST" action="{{ route('followups.destroy', $followUp) }}" data-followup-destroy>
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-stone-400 hover:text-[var(--as-coral)] p-1" type="submit" title="ลบรายการนี้">
                                            <x-icon name="trash" size="13" />
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </details>
            @endif
        </div>
    </section>

    {{-- Deals Section --}}
    @if ($client->deals->isNotEmpty())
        <section class="as-work-section mt-6" aria-labelledby="client-deals-heading">
            <div class="as-section-head">
                <h2 id="client-deals-heading" class="as-section-title">ดีลของลูกค้านี้</h2>
                <span class="as-count">{{ $client->deals->count() }}</span>
            </div>
            <ul>
                @foreach ($client->deals as $deal)
                    <li>
                        <a href="{{ route('deals.show', $deal) }}" class="as-work-row">
                            <span class="as-check"><x-icon name="briefcase" size="15" /></span>
                            <div class="as-row-body">
                                <span class="as-row-title">{{ $deal->stageLabel() }}</span>
                                <span class="as-row-meta">{{ $deal->property?->name }} @if($deal->amount) · มูลค่า {{ number_format((float) $deal->amount, 0) }} บาท @endif</span>
                            </div>
                            <x-icon name="chevron-right" size="18" class="as-row-chevron" />
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
@endsection
