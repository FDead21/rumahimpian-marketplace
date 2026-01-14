<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ __('Map Search') }} - RumahImpian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .property-card.active { border-color: #4F46E5; background-color: #F5F3FF; }
        .leaflet-popup-content-wrapper { border-radius: 12px; padding: 0; overflow: hidden; }
        .leaflet-popup-content { margin: 0; width: 220px !important; }
        .leaflet-container a.leaflet-popup-close-button { top: 8px; right: 8px; color: white; text-shadow: 0 1px 2px rgba(0,0,0,0.5); }
    </style>
</head>
<body class="bg-gray-50 overflow-hidden">

    {{-- NAVBAR --}}
    <nav class="absolute top-0 left-0 w-full bg-white/90 backdrop-blur-md border-b border-gray-200 h-16 z-[1000] shadow-sm flex-shrink-0">
        <div class="max-w-7xl mx-auto px-4 h-full flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-xl font-extrabold text-indigo-600 tracking-tight">RumahImpian</a>
            
            <div class="flex gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2 bg-white border border-gray-300 hover:border-indigo-500 hover:text-indigo-600 px-4 py-1.5 rounded-full text-sm font-medium transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    {{ __('List View') }}
                </a>
            </div>
        </div>
    </nav>

    {{-- MAIN LAYOUT --}}
    <div class="flex h-screen pt-16 relative">
        
        {{-- 1. LEFT PANEL (Desktop List) --}}
        <div class="hidden md:flex w-[400px] lg:w-[450px] flex-col bg-white border-r border-gray-200 h-full shadow-xl z-20 relative">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h2 class="font-bold text-gray-800">{{ $properties->count() }} {{ __('Properties Found') }}</h2>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-4" id="desktop-list">
                @foreach($properties as $property)
                    <div class="property-card flex gap-3 p-3 rounded-xl border border-gray-200 hover:shadow-lg transition cursor-pointer bg-white group"
                         id="card-desktop-{{ $property->id }}"
                         onclick="focusOnMap({{ $property->latitude }}, {{ $property->longitude }}, {{ $property->id }})">
                        
                        <div class="w-28 h-24 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 relative">
                            @if($property->media->first())
                                <img src="{{ asset('storage/' . $property->media->first()->file_path) }}" class="w-full h-full object-cover">
                            @endif
                            <div class="absolute top-1 left-1 bg-black/60 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded">
                                {{ __(ucfirst(strtolower($property->listing_type))) }}
                            </div>
                        </div>

                        <div class="flex-grow flex flex-col justify-center min-w-0">
                            <h3 class="font-bold text-gray-900 text-sm leading-tight truncate group-hover:text-indigo-600 mb-1">
                                {{ $property->title }}
                            </h3>
                            <p class="text-xs text-gray-500 mb-2 truncate">{{ $property->district }}, {{ $property->city }}</p>
                            <p class="text-indigo-600 font-bold text-sm mb-2">
                                @currency($property->price)
                            </p>
                            <div class="flex items-center gap-3 text-xs text-gray-500">
                                <span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg> {{ $property->bedrooms }}</span>
                                <span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> {{ $property->bathrooms }}</span>
                                <span class="flex items-center gap-1">📐 {{ $property->building_area }}m²</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 2. MAP CONTAINER --}}
        <div class="flex-1 relative h-full w-full">
            <div id="map" class="absolute inset-0 z-0 w-full h-full bg-gray-200"></div>

            {{-- 3. MOBILE BOTTOM CARD SLIDER --}}
            <div class="md:hidden absolute bottom-6 left-0 right-0 z-[500] px-4 pointer-events-none">
                <div class="flex overflow-x-auto gap-4 no-scrollbar snap-x snap-mandatory py-4 pointer-events-auto" id="mobile-slider">
                    
                    @foreach($properties as $property)
                        <div class="snap-center flex-shrink-0 w-[85vw] max-w-[320px] bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden cursor-pointer transform transition-transform"
                             id="card-mobile-{{ $property->id }}"
                             onclick="focusOnMap({{ $property->latitude }}, {{ $property->longitude }}, {{ $property->id }})">
                            
                            <div class="flex h-28">
                                <div class="w-28 relative bg-gray-200">
                                    @if($property->media->first())
                                        <img src="{{ asset('storage/' . $property->media->first()->file_path) }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex-1 p-3 flex flex-col justify-center">
                                    <h3 class="font-bold text-gray-900 text-sm leading-tight line-clamp-1 mb-1">{{ $property->title }}</h3>
                                    <p class="text-indigo-600 font-bold text-sm mb-1">@currency($property->price)</p>
                                    <p class="text-xs text-gray-500 mb-2 truncate">{{ $property->city }}</p>
                                    
                                    <a href="{{ route('property.show', ['id' => $property->id, 'slug' => $property->slug]) }}" 
                                       class="text-xs bg-gray-900 text-white py-1.5 px-3 rounded-lg text-center font-bold">
                                        {{ __('View Details') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

    </div>

    <script>
        var map = L.map('map', { zoomControl: false }).setView([-6.9175, 107.6191], 12);
        L.control.zoom({ position: 'topright' }).addTo(map);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        var properties = @json($properties);
        var markers = {};
        var bounds = L.latLngBounds();
        
        properties.forEach(function(prop) {
            if(prop.latitude && prop.longitude) {
                
                var icon = L.divIcon({
                    className: 'custom-pin',
                    html: `<div class="w-10 h-10 bg-white rounded-full border-2 border-indigo-600 shadow-xl flex items-center justify-center text-indigo-600 text-lg font-bold hover:scale-110 transition transform">🏠</div>`,
                    iconSize: [40, 40],
                    iconAnchor: [20, 40],
                    popupAnchor: [0, -40] 
                });

                var popupContent = `
                    <div class="font-sans">
                        <div class="h-32 w-full bg-gray-200 relative">
                            <img src="/storage/${prop.media[0]?.file_path}" class="w-full h-full object-cover">
                            <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/60 to-transparent p-2">
                                <span class="text-white font-bold text-sm">Rp ${new Intl.NumberFormat('id-ID').format(prop.price)}</span>
                            </div>
                        </div>
                        <div class="p-3">
                            <h3 class="font-bold text-sm text-gray-900 leading-tight mb-1 truncate">${prop.title}</h3>
                            <p class="text-xs text-gray-500 mb-2">${prop.district}, ${prop.city}</p>
                            <a href="/property/${prop.id}/${prop.slug}" target="_blank" class="block w-full bg-indigo-600 text-white text-center text-xs py-2 rounded font-bold hover:bg-indigo-700 transition">
                                {{ __('View Property') }}
                            </a>
                        </div>
                    </div>
                `;

                var marker = L.marker([prop.latitude, prop.longitude], {icon: icon})
                    .addTo(map)
                    .bindPopup(popupContent)
                    .on('click', function() {
                        highlightCard('card-desktop-' + prop.id);
                        highlightCard('card-mobile-' + prop.id);
                        focusOnMap(prop.latitude, prop.longitude, prop.id, false); 
                    });

                markers[prop.id] = marker;
                bounds.extend([prop.latitude, prop.longitude]);
            }
        });

        if(properties.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }

        function focusOnMap(lat, lng, id, openPopup = true) {
            var offset = window.innerWidth < 768 ? -0.008 : 0; 
            map.flyTo([lat + offset, lng], 16, { animate: true, duration: 1 });

            highlightCard('card-desktop-' + id);
            highlightCard('card-mobile-' + id);

            if(openPopup && markers[id]) {
                markers[id].openPopup();
            }

            var mobileCard = document.getElementById('card-mobile-' + id);
            if(mobileCard) {
                mobileCard.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }
        }

        function highlightCard(elementId) {
            document.querySelectorAll('.property-card').forEach(el => el.classList.remove('active'));
            var el = document.getElementById(elementId);
            if(el) {
                el.classList.add('active');
                if(elementId.includes('desktop')) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        }
    </script>
</body>
</html>