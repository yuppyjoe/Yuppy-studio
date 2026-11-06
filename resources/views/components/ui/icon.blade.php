{{-- Minimal inline stroke icon set (24px grid, 1.5 stroke). --}}
@props(['name'])

@php
    $paths = [
        'home' => 'M3 10.5 12 3l9 7.5M5 9v11h5v-6h4v6h5V9',
        'studio' => 'M3 8.5h4l1.5-2.5h7L17 8.5h4V19H3V8.5Z M12 16.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z',
        'palette' => 'M12 3a9 9 0 1 0 0 18c1.1 0 1.6-.9 1.2-1.9-.5-1.1.2-2.1 1.4-2.1H17a4 4 0 0 0 4-4c0-5.5-4-10-9-10Z M7.5 11.5h.01 M10 7.5h.01 M14.5 7.5h.01',
        'users' => 'M16 19v-1a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v1 M9 10a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z M22 19v-1a4 4 0 0 0-3-3.9 M16 3.1a3.5 3.5 0 0 1 0 6.8',
        'book' => 'M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15H6.5A2.5 2.5 0 0 0 4 20.5v-15Z M4 20.5A2.5 2.5 0 0 0 6.5 23H20v-5',
        'camera' => 'M3 8.5h4l1.5-2.5h7L17 8.5h4V19H3V8.5Z M12 16.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z',
        'user' => 'M20 21v-1a5 5 0 0 0-5-5H9a5 5 0 0 0-5 5v1 M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z',
        'logout' => 'M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3 M10 17l5-5-5-5 M15 12H3',
        'menu' => 'M4 7h16M4 12h16M4 17h16',
        'x' => 'M6 6l12 12M18 6 6 18',
        'upload' => 'M12 16V4 M7 9l5-5 5 5 M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3',
        'photo' => 'M4 5h16v14H4z M4 16l4.5-4.5 4 4 2.5-2.5L20 18 M15.5 9.5h.01',
        'check' => 'M5 12.5 10 17l9-10',
        'plus' => 'M12 5v14M5 12h14',
        'search' => 'M11 18a7 7 0 1 0 0-14 7 7 0 0 0 0 14Z M20 20l-3.5-3.5',
        'arrow-right' => 'M5 12h14 M13 6l6 6-6 6',
        'arrow-left' => 'M19 12H5 M11 6l-6 6 6 6',
        'shield' => 'M12 3 4.5 6v5.5c0 4.6 3.2 8.4 7.5 9.5 4.3-1.1 7.5-4.9 7.5-9.5V6L12 3Z',
        'activity' => 'M3 12h4l3-8 4 16 3-8h4',
        'ruler' => 'M3 17 17 3l4 4L7 21l-4-4Z M7 13l2 2 M10 10l2 2 M13 7l2 2',
        'pencil' => 'M4 20h4L19 9l-4-4L4 16v4Z M13.5 6.5l4 4',
        'trash' => 'M4 7h16 M10 11v6 M14 11v6 M6 7l1 13h10l1-13 M9 7V4h6v3',
        'sparkle' => 'M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5 18 18M18 6l-2.5 2.5M8.5 15.5 6 18',
    ];
@endphp

<svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="{{ $paths[$name] ?? $paths['sparkle'] }}" />
</svg>
