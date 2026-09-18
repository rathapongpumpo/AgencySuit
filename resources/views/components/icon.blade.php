@props(['name', 'size' => 20, 'class' => ''])

@php($paths = [
    'calendar' => '<rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
    'building' => '<path d="M4 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16M16 9h4a1 1 0 0 1 1 1v11M8 7h2M8 11h2M8 15h2M13 7h1M13 11h1M13 15h1M2 21h20"/>',
    'check' => '<path d="m5 12 4 4L19 6"/>',
    'chevron-right' => '<path d="m9 18 6-6-6-6"/>',
    'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
    'home' => '<path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V10Z"/>',
    'map-pin' => '<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
    'more' => '<circle cx="5" cy="12" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/>',
    'phone' => '<path d="M6.5 3h3L11 7 8.8 8.6a14 14 0 0 0 6.6 6.6L17 13l4 1.5v3a2 2 0 0 1-2.2 2A17.8 17.8 0 0 1 3.5 5.2 2 2 0 0 1 6.5 3Z"/>',
    'plus' => '<path d="M12 5v14M5 12h14"/>',
    'users' => '<circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 5a3 3 0 0 1 0 6M17 14a5 5 0 0 1 4 6"/>',
])

<svg {{ $attributes->merge(['class' => $class, 'width' => $size, 'height' => $size, 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'aria-hidden' => 'true']) }}>
    {!! $paths[$name] ?? $paths['more'] !!}
</svg>
