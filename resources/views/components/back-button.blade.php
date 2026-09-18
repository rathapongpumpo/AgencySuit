@props(['fallback' => route('today'), 'label' => 'ย้อนกลับ'])

<a href="{{ $fallback }}" 
   onclick="if (window.history.length > 1 && document.referrer && document.referrer.indexOf(window.location.host) !== -1) { window.history.back(); return false; }" 
   class="as-back-link" 
   aria-label="{{ $label }}">
    <x-icon name="chevron-right" size="18" class="rotate-180" />
    <span>{{ $label }}</span>
</a>
