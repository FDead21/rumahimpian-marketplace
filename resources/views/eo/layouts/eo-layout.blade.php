<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? $eoSettings['eo_site_name'] ?? 'Event Organizer' }}</title>

    @if(!empty($eoSettings['eo_site_favicon']))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $eoSettings['eo_site_favicon']) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen font-sans text-gray-900">

    @include('eo.components.eo-navbar')

    <main class="flex-grow">
        {{ $slot }}
    </main>

    @include('eo.components.eo-footer')

</body>
</html>
