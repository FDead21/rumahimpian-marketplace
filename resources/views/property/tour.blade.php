<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>360 Tour - {{ $property->title }}</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        #panorama { width: 100vw; height: 100vh; }
        .control-box {
            position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%);
            background: rgba(0,0,0,0.7); padding: 10px 20px; border-radius: 30px;
            z-index: 10; color: white; display: flex; gap: 20px; align-items: center;
        }
    </style>
</head>
<body class="bg-black overflow-hidden relative">

    <div class="absolute top-0 left-0 w-full p-4 z-10 flex justify-between items-start bg-gradient-to-b from-black/70 to-transparent">
        <div>
            <h1 class="text-white font-bold text-xl">{{ $property->title }}</h1>
            <p class="text-gray-300 text-sm">📍 {{ $property->city }}</p>
        </div>
        <a href="{{ route('property.show', ['id' => $property->id, 'slug' => $property->slug]) }}" 
           class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg backdrop-blur-sm text-sm font-bold transition">
            ✕ {{ __('Close Tour') }}
        </a>
    </div>

    <div id="panorama"></div>

    <div class="control-box">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
            <span class="text-sm">{{ __('Drag to look around') }}</span>
        </div>
        <div class="h-4 w-px bg-gray-500"></div>
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
            <span class="text-sm">{{ __('Scroll to zoom') }}</span>
        </div>
    </div>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
    
    <script>
        @if($tourImage)
            pannellum.viewer('panorama', {
                "type": "equirectangular",
                "panorama": "{{ asset('storage/' . $tourImage->file_path) }}",
                "autoLoad": true,
                "autoRotate": -2,
                "compass": true,
                "showControls": true
            });
        @else
            alert("{{ __('No 360 image found for this property.') }}");
        @endif
    </script>
</body>
</html>