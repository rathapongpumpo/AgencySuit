@extends('layouts.app')

@section('title', 'ส่งความคิดเห็น | AgencySuit')

@section('content')
    <x-back-button :fallback="route('more')" label="ย้อนกลับ" />
    <div class="as-detail-hero">
        <h1 class="as-detail-title">ส่งความคิดเห็น</h1>
        <p class="as-page-subtitle">บอกเราแบบสั้น ๆ เพื่อช่วยให้ AgencySuit ใช้ง่ายขึ้น</p>
    </div>
    @if(session('success'))<p role="status" class="as-alert as-alert--success mb-5">{{ session('success') }}</p>@endif
    <form method="POST" action="{{ route('feedback.store') }}" class="as-form" novalidate>@csrf
        <fieldset class="as-field">
            <legend class="as-field-label">อยากบอกเราเรื่องอะไร</legend>
            <div class="mt-2 grid gap-2">
                @foreach(['hard_to_use'=>'ใช้งานยาก','bug'=>'เจอปัญหา','feature'=>'อยากให้เพิ่ม'] as $value=>$label)
                    <label class="as-choice justify-start px-4"><input type="radio" name="type" value="{{ $value }}" @checked(old('type','hard_to_use')===$value) required>{{ $label }}</label>
                @endforeach
            </div>
            @error('type')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror
        </fieldset>
        <div class="as-field"><label for="message" class="as-field-label">ความคิดเห็น</label><textarea id="message" name="message" rows="5" maxlength="300" required class="as-textarea">{{ old('message') }}</textarea>@error('message')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</div>
        <button type="submit" class="as-action-primary">ส่งความคิดเห็น</button>
    </form>
@endsection
