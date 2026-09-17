@extends('layouts.app')

@section('title', 'ส่งความคิดเห็น | AgencySuit')

@section('content')
    <a href="{{ route('more') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-green-800">← เพิ่มเติม</a>
    <h1 class="mt-3 text-2xl font-bold tracking-tight">ส่งความคิดเห็น</h1>
    @if(session('success'))<p role="status" class="mt-5 border-y border-green-200 bg-green-50 px-4 py-3 text-sm leading-6 text-green-900">{{ session('success') }}</p>@endif
    <p class="mt-2 text-sm leading-6 text-stone-600">บอกเราแบบสั้น ๆ เพื่อช่วยให้ AgencySuit ใช้ง่ายขึ้น</p>
    <form method="POST" action="{{ route('feedback.store') }}" class="mt-7 space-y-5">@csrf
        <fieldset><legend class="text-sm font-medium">ประเภท</legend><div class="mt-2 space-y-2">@foreach(['hard_to_use'=>'ใช้งานยาก','bug'=>'เจอปัญหา','feature'=>'อยากให้เพิ่ม'] as $value=>$label)<label class="flex min-h-12 items-center gap-3 rounded-xl border border-stone-300 px-4 has-[:checked]:border-green-800 has-[:checked]:bg-green-50"><input type="radio" name="type" value="{{ $value }}" @checked(old('type','hard_to_use')===$value) required>{{ $label }}</label>@endforeach</div>@error('type')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</fieldset>
        <div><label for="message" class="text-sm font-medium">ความคิดเห็น</label><textarea id="message" name="message" rows="5" maxlength="300" required class="mt-1.5 w-full rounded-xl border border-stone-300 px-4 py-3 text-base">{{ old('message') }}</textarea>@error('message')<p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>@enderror</div>
        <button type="submit" class="min-h-12 w-full rounded-xl bg-green-900 px-5 text-base font-semibold text-white">ส่งความคิดเห็น</button>
    </form>
@endsection
