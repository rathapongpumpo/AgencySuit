@extends('layouts.admin')

@section('title', 'จัดการข้อเสนอแนะ | AgencySuit Admin')

@section('content')
    <div class="as-page-head">
        <div>
            <h1 class="as-page-title">ข้อเสนอแนะจากผู้ใช้</h1>
            <p class="as-page-subtitle">ทั้งหมด {{ number_format($counts['total']) }} รายการ (ใหม่: {{ $counts['new'] }}, วางแผน: {{ $counts['planned'] }}, เสร็จแล้ว: {{ $counts['done'] }})</p>
        </div>
    </div>

    {{-- Search and Filter Form --}}
    <form method="GET" action="{{ route('admin.feedback.index') }}" class="mt-4 space-y-2.5">
        <div class="relative">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="ค้นหาข้อความ, ผู้ส่ง หรือหน้าที่ส่ง..." class="as-input pl-10 pr-20 text-sm">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-stone-400 pointer-events-none">
                <x-icon name="search" size="16" />
            </span>
            @if (request('q') || request('status') || request('type'))
                <a href="{{ route('admin.feedback.index') }}" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-xs font-semibold text-stone-400 hover:text-stone-700">ล้างค้นหา</a>
            @endif
        </div>

        {{-- Filter Chips --}}
        <div class="flex gap-2 overflow-x-auto pb-1">
            <a href="{{ route('admin.feedback.index', array_filter(['q' => request('q'), 'type' => request('type')])) }}"
                @class(['as-chip is-active' => ! request('status'), 'as-chip' => request('status')])>
                ทั้งหมด ({{ $counts['total'] }})
            </a>
            <a href="{{ route('admin.feedback.index', array_filter(['status' => 'new', 'q' => request('q'), 'type' => request('type')])) }}"
                @class(['as-chip is-active' => request('status') === 'new', 'as-chip' => request('status') !== 'new'])>
                ใหม่ ({{ $counts['new'] }})
            </a>
            <a href="{{ route('admin.feedback.index', array_filter(['status' => 'reviewed', 'q' => request('q'), 'type' => request('type')])) }}"
                @class(['as-chip is-active' => request('status') === 'reviewed', 'as-chip' => request('status') !== 'reviewed'])>
                รับเรื่อง ({{ $counts['reviewed'] }})
            </a>
            <a href="{{ route('admin.feedback.index', array_filter(['status' => 'planned', 'q' => request('q'), 'type' => request('type')])) }}"
                @class(['as-chip is-active' => request('status') === 'planned', 'as-chip' => request('status') !== 'planned'])>
                วางแผน ({{ $counts['planned'] }})
            </a>
            <a href="{{ route('admin.feedback.index', array_filter(['status' => 'done', 'q' => request('q'), 'type' => request('type')])) }}"
                @class(['as-chip is-active' => request('status') === 'done', 'as-chip' => request('status') !== 'done'])>
                เสร็จแล้ว ({{ $counts['done'] }})
            </a>
        </div>
    </form>

    {{-- Feedback List --}}
    @if ($feedbacks->isEmpty())
        <div class="mt-5 as-surface overflow-hidden">
            <x-empty-state title="ไม่พบข้อเสนอแนะ" description="ลองเปลี่ยนคำค้นหา หรือกดล้างตัวกรองเพื่อดูทั้งหมด" action="ดูทั้งหมด" :action-url="route('admin.feedback.index')" />
        </div>
    @else
        <div class="mt-4 space-y-3">
            @foreach ($feedbacks as $fb)
                <div class="as-card space-y-2.5 p-3.5">
                    {{-- Header: Type Badge, Status, Date --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            @if ($fb->type === 'เจอปัญหา')
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-bold bg-red-500/15 text-red-700 dark:text-red-400 border border-red-500/25">เจอปัญหา</span>
                            @elseif ($fb->type === 'ใช้งานยาก')
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-bold bg-amber-500/15 text-amber-700 dark:text-amber-400 border border-amber-500/25">ใช้งานยาก</span>
                            @else
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-bold bg-blue-500/15 text-blue-700 dark:text-blue-400 border border-blue-500/25">อยากให้เพิ่ม</span>
                            @endif

                            @if ($fb->status === 'new')
                                <span class="rounded-full px-2 py-0.5 text-[11px] font-bold bg-amber-500/15 text-amber-700 dark:text-amber-400 border border-amber-500/25">ใหม่</span>
                            @elseif ($fb->status === 'reviewed')
                                <span class="rounded-full px-2 py-0.5 text-[11px] font-bold bg-blue-500/15 text-blue-700 dark:text-blue-400 border border-blue-500/25">รับเรื่องแล้ว</span>
                            @elseif ($fb->status === 'planned')
                                <span class="rounded-full px-2 py-0.5 text-[11px] font-bold bg-purple-500/15 text-purple-700 dark:text-purple-400 border border-purple-500/25">วางแผนทำ</span>
                            @elseif ($fb->status === 'done')
                                <span class="rounded-full px-2 py-0.5 text-[11px] font-bold bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 border border-emerald-500/25">เสร็จสิ้น</span>
                            @endif
                        </div>
                        <span class="text-xs text-[var(--as-muted)] shrink-0">{{ $fb->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    {{-- Feedback Message --}}
                    <div class="rounded-xl border border-[var(--as-line)] bg-[var(--as-surface-raised)] p-3">
                        <p class="text-sm leading-relaxed text-[var(--as-ink)] break-words whitespace-pre-line">{{ $fb->message }}</p>
                    </div>

                    {{-- Meta: Submitter & Route --}}
                    <div class="text-xs text-[var(--as-muted)] space-y-0.5">
                        <div>ผู้ส่ง: <strong class="text-[var(--as-ink)]">{{ $fb->user?->name ?? 'ไม่ระบุ' }}</strong> ({{ $fb->user?->email ?? 'ไม่มีอีเมล' }})</div>
                        @if ($fb->route)
                            <div class="truncate">หน้าที่ส่ง: <span class="font-mono text-[11px] text-[var(--as-faint)]">{{ $fb->route }}</span></div>
                        @endif
                    </div>

                    {{-- Footer: Status Changer & Delete Action --}}
                    <div class="flex items-center justify-between gap-2 border-t border-[var(--as-line)] pt-2.5">
                        <form method="POST" action="{{ route('admin.feedback.update-status', $fb) }}" class="flex items-center gap-1.5">
                            @csrf
                            @method('PATCH')
                            <label for="status-{{ $fb->id }}" class="text-xs text-[var(--as-muted)] shrink-0">สถานะ:</label>
                            <select
                                id="status-{{ $fb->id }}"
                                name="status"
                                onchange="this.form.submit()"
                                class="rounded-lg border border-[var(--as-line)] bg-[var(--as-surface)] px-2 py-1 text-xs text-[var(--as-ink)] font-semibold"
                            >
                                <option value="new" @selected($fb->status === 'new')>ใหม่</option>
                                <option value="reviewed" @selected($fb->status === 'reviewed')>รับเรื่องแล้ว</option>
                                <option value="planned" @selected($fb->status === 'planned')>วางแผนทำ</option>
                                <option value="done" @selected($fb->status === 'done')>เสร็จสิ้น</option>
                            </select>
                        </form>

                        <form method="POST" action="{{ route('admin.feedback.destroy', $fb) }}" data-feedback-destroy>
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="text-xs text-[var(--as-coral)] hover:text-red-400 p-1 font-semibold"
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
