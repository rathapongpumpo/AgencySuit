@extends('layouts.app')

@section('title', 'จัดการข้อเสนอแนะ | AgencySuit Admin')

@section('content')
    <div class="as-page-head">
        <div class="flex items-center gap-3">
            <x-back-button fallback="{{ route('more') }}" />
            <div>
                <h1 class="as-page-title">จัดการข้อเสนอแนะ</h1>
                <p class="as-page-subtitle">ทั้งหมด {{ number_format($counts['total']) }} รายการ (ใหม่: {{ $counts['new'] }}, รอทำ: {{ $counts['planned'] }}, เสร็จแล้ว: {{ $counts['done'] }})</p>
            </div>
        </div>
    </div>

    {{-- Admin Navigation Tabs --}}
    <div class="mb-4 flex gap-2 border-b border-stone-200 pb-2">
        <a href="{{ route('admin.users.index') }}" class="as-pill flex items-center gap-1.5">
            <x-icon name="users" size="16" />
            <span>ผู้ใช้งาน</span>
        </a>
        <a href="{{ route('admin.feedback.index') }}" class="as-pill is-active flex items-center gap-1.5">
            <x-icon name="message" size="16" />
            <span>ข้อเสนอแนะ ({{ $counts['total'] }})</span>
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Search & Filters --}}
    <div class="mb-4 space-y-2">
        <form method="GET" action="{{ route('admin.feedback.index') }}" class="flex gap-2">
            <input
                type="search"
                name="q"
                value="{{ request('q') }}"
                placeholder="ค้นหาข้อความ, ผู้ส่ง หรือหน้าที่ส่ง..."
                class="as-input flex-1"
            />
            @if (request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}" />
            @endif
            @if (request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}" />
            @endif
            <button type="submit" class="as-action-secondary px-4 py-2">ค้นหา</button>
        </form>

        <div class="flex flex-wrap gap-1.5 pt-1">
            <a href="{{ route('admin.feedback.index', array_filter(['q' => request('q'), 'type' => request('type')])) }}"
               @class(['as-filter-chip', 'is-active' => ! request('status')])>
                ทั้งหมด ({{ $counts['total'] }})
            </a>
            <a href="{{ route('admin.feedback.index', array_filter(['status' => 'new', 'q' => request('q'), 'type' => request('type')])) }}"
               @class(['as-filter-chip', 'is-active' => request('status') === 'new'])>
                ใหม่ ({{ $counts['new'] }})
            </a>
            <a href="{{ route('admin.feedback.index', array_filter(['status' => 'reviewed', 'q' => request('q'), 'type' => request('type')])) }}"
               @class(['as-filter-chip', 'is-active' => request('status') === 'reviewed'])>
                รับเรื่อง ({{ $counts['reviewed'] }})
            </a>
            <a href="{{ route('admin.feedback.index', array_filter(['status' => 'planned', 'q' => request('q'), 'type' => request('type')])) }}"
               @class(['as-filter-chip', 'is-active' => request('status') === 'planned'])>
                วางแผน ({{ $counts['planned'] }})
            </a>
            <a href="{{ route('admin.feedback.index', array_filter(['status' => 'done', 'q' => request('q'), 'type' => request('type')])) }}"
               @class(['as-filter-chip', 'is-active' => request('status') === 'done'])>
                เสร็จแล้ว ({{ $counts['done'] }})
            </a>
        </div>
    </div>

    {{-- Feedback List Cards --}}
    @if ($feedbacks->isEmpty())
        <div class="rounded-2xl border border-dashed border-stone-300 bg-white p-6 text-center text-stone-500">
            <p>ไม่พบข้อเสนอแนะตามเงื่อนไขที่ค้นหา</p>
            @if (request('q') || request('status') || request('type'))
                <a href="{{ route('admin.feedback.index') }}" class="as-action-secondary mt-3 inline-block text-xs">ล้างตัวกรอง</a>
            @endif
        </div>
    @else
        <div class="space-y-3">
            @foreach ($feedbacks as $fb)
                <div class="as-card space-y-3 p-3.5">
                    {{-- Card Header --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-1.5">
                            @if ($fb->type === 'เจอปัญหา')
                                <span class="rounded bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-800">เจอปัญหา</span>
                            @elseif ($fb->type === 'ใช้งานยาก')
                                <span class="rounded bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">ใช้งานยาก</span>
                            @else
                                <span class="rounded bg-sky-100 px-2 py-0.5 text-xs font-semibold text-sky-800">อยากให้เพิ่ม</span>
                            @endif

                            @if ($fb->status === 'new')
                                <span class="rounded-full bg-amber-50 border border-amber-200 px-2 py-0.5 text-[11px] font-bold text-amber-800">ใหม่</span>
                            @elseif ($fb->status === 'reviewed')
                                <span class="rounded-full bg-blue-50 border border-blue-200 px-2 py-0.5 text-[11px] font-bold text-blue-800">รับเรื่องแล้ว</span>
                            @elseif ($fb->status === 'planned')
                                <span class="rounded-full bg-purple-50 border border-purple-200 px-2 py-0.5 text-[11px] font-bold text-purple-800">วางแผนทำ</span>
                            @elseif ($fb->status === 'done')
                                <span class="rounded-full bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[11px] font-bold text-emerald-800">เสร็จสิ้น</span>
                            @endif
                        </div>
                        <span class="text-[11px] text-stone-400 shrink-0">{{ $fb->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    {{-- Message Content --}}
                    <div class="rounded-xl bg-stone-50 p-3">
                        <p class="text-sm leading-relaxed text-stone-800 break-words whitespace-pre-line">{{ $fb->message }}</p>
                    </div>

                    {{-- Submitter & Context Meta --}}
                    <div class="space-y-0.5 text-xs text-stone-500">
                        <div><strong class="text-stone-700">ผู้ส่ง:</strong> {{ $fb->user?->name ?? 'ไม่ระบุ' }} ({{ $fb->user?->email ?? 'ไม่มีอีเมล' }})</div>
                        @if ($fb->route)
                            <div class="truncate"><strong class="text-stone-700">จากหน้า:</strong> <span class="text-stone-400 font-mono text-[11px]">{{ $fb->route }}</span></div>
                        @endif
                    </div>

                    {{-- Status Selector and Actions --}}
                    <div class="flex flex-wrap items-center justify-between gap-2 border-t border-stone-100 pt-2.5">
                        <form method="POST" action="{{ route('admin.feedback.update-status', $fb) }}" class="flex items-center gap-1.5">
                            @csrf
                            @method('PATCH')
                            <label for="status-{{ $fb->id }}" class="text-xs text-stone-600 shrink-0">สถานะ:</label>
                            <select
                                id="status-{{ $fb->id }}"
                                name="status"
                                onchange="this.form.submit()"
                                class="rounded-lg border border-stone-300 bg-white px-2 py-1 text-xs text-stone-800 focus:border-stone-900 focus:outline-none"
                            >
                                <option value="new" @selected($fb->status === 'new')>ใหม่</option>
                                <option value="reviewed" @selected($fb->status === 'reviewed')>รับเรื่องแล้ว</option>
                                <option value="planned" @selected($fb->status === 'planned')>วางแผนทำ</option>
                                <option value="done" @selected($fb->status === 'done')>เสร็จสิ้น</option>
                            </select>
                        </form>

                        <form method="POST" action="{{ route('admin.feedback.destroy', $fb) }}">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="text-xs text-red-600 hover:text-red-800 p-1"
                            >
                                ลบรายการ
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $feedbacks->links() }}
        </div>
    @endif
@endsection
