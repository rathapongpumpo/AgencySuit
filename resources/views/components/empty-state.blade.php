@props(['title', 'description', 'action' => null, 'actionLabel' => null, 'actionUrl' => null])

@php($btnText = $action ?? $actionLabel ?? 'เริ่มต้น')

<section class="as-empty">
    <h2 class="as-empty-title">{{ $title }}</h2>
    <p class="as-empty-copy">{{ $description }}</p>
    @if ($actionUrl)
        <a href="{{ $actionUrl }}" class="as-action-primary mt-5 max-w-sm">{{ $btnText }}</a>
    @else
        <button type="button" data-quick-add-open class="as-action-primary mt-5 max-w-sm">{{ $btnText }}</button>
    @endif
</section>
