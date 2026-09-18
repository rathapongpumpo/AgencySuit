@props(['title', 'description', 'action', 'actionUrl' => null])

<section class="as-empty">
    <h2 class="as-empty-title">{{ $title }}</h2>
    <p class="as-empty-copy">{{ $description }}</p>
    @if ($actionUrl)
        <a href="{{ $actionUrl }}" class="as-action-primary mt-5 max-w-sm">{{ $action }}</a>
    @else
        <button type="button" data-quick-add-open class="as-action-primary mt-5 max-w-sm">{{ $action }}</button>
    @endif
</section>
