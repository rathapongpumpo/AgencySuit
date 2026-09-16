@props(['title', 'description', 'action', 'actionUrl' => null])

<section class="mt-6 border-t border-stone-200 pt-6">
    <h2 class="text-lg font-semibold">{{ $title }}</h2>
    <p class="mt-2 max-w-sm text-sm leading-6 text-stone-600">{{ $description }}</p>
    @if ($actionUrl)
        <a href="{{ $actionUrl }}" class="mt-5 inline-flex min-h-12 items-center rounded-xl bg-green-900 px-5 text-base font-semibold text-white">{{ $action }}</a>
    @else
        <button type="button" data-quick-add-open class="mt-5 min-h-12 rounded-xl bg-green-900 px-5 text-base font-semibold text-white">{{ $action }}</button>
    @endif
</section>
