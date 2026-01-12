<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map Search - RumahImpian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        /* Custom scrollbar for the list */
        .scroller::-webkit-scrollbar { width: 6px; }
        .scroller::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; }
        .scroller::-webkit-scrollbar-track { background-color: #f1f5f9; }
        
        /* Card Hover Effect */
        .property-card.active { border: 2px solid #4F46E5; background-color: #eff6ff; }
    </style>
</head>
<body class="bg-gray-50 h-screen flex flex-col overflow-hidden">
    
    <nav class="bg-white border-b border-gray-200 h-16 flex-shrink-0 z-50">
        <div class="max-w-7xl mx-auto px-4 h-full flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-xl font-bold text-indigo-600">RumahImpian</a>
            <div class="flex gap-4 text-sm">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-indigo-600">Back to List</a>
            </div>
        </div>
    </nav>

    <div class="flex flex-grow h-[calc(100vh-64px)]">
        
        <div class="w-full md:w-[400px] lg:w-[500px] bg-white border-r border-gray-200 overflow-y-auto scroller p-4 flex-shrink-0 z-10 shadow-xl" id="card-list">
            <h2 class="text-lg font-bold text-gray-800 mb-4">{{ $properties->count() }} Properties on Map</h2>
            
            <div class="space-y-4">
                @foreach($properties as $property)
                    <div class="property-card flex gap-3 p-3 rounded-xl border border-gray-100 hover:shadow-md transition cursor-pointer bg-white group"
                         id="card-{{ $property->id }}"
                         onclick="focusOnMap({{ $property->latitude }}, {{ $property->longitude }}, {{ $property->id }})">
                        
                        <div class="w-24 h-24 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0 relative">
                            @if($property->media->first())
                                <img src="{{ asset('storage/' . $property->media->first()->file_path) }}" class="w-full h-full object-cover">
                            @endif
                            <div class="absolute top-1 left-1 bg-gray-900/50 text-white text-[10px] px-1.5 rounded">
                                {{ $property->listing_type }}
                            </div>
                        </div>

                        <div class="flex-grow flex flex-col justify-center">
                            <h3 class="font-bold text-gray-900 text-sm leading-tight group-hover:text-indigo-600 mb-1">
                                {{ Str::limit($property->title, 40) }}
                            </h3>
                            <p class="text-indigo-600 font-bold text-sm mb-2">
                                @currency($property->price)
                            </p>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <span>🛏 {{ $property->bedrooms }}</span>
                                <span>🚿 {{ $property->bathrooms }}</span>
                                <span>🏠 {{ $property->building_area }}m²</span>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($properties->isEmpty())
                    <div class="text-center py-10 text-gray-400">
                        No properties with location data found.
                    </div>
                @endif
            </div>
        </div>

        <div class="flex-grow bg-gray-100 relative">
            <div id="map" class="absolute inset-0 w-full h-full z-0"></div>
            
            <button class="md:hidden absolute bottom-6 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white px-6 py-2 rounded-full font-bold shadow-lg z-[1000]"
                    onclick="document.getElementById('card-list').classList.toggle('hidden')">
                Toggle List
            </button>
        </div>

    </div>

    <script>
        // 1. Initialize Map (Center on Bandung by default)
        var map = L.map('map').setView([-6.9175, 107.6191], 12);

        // 2. Add Tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // 3. Prepare Data from PHP
        var properties = @json($properties);
        var markers = {};
        var activeCardId = null;

        // 4. Add Markers
        properties.forEach(function(prop) {
            if(prop.latitude && prop.longitude) {
                
                // Create custom icon
                var icon = L.divIcon({
                    className: 'custom-pin',
                    html: `<div class="w-8 h-8 bg-indigo-600 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-xs font-bold">🏠</div>`,
                    iconSize: [32, 32],
                    iconAnchor: [16, 32]
                });

                // Create Marker
                var marker = L.marker([prop.latitude, prop.longitude], {icon: icon})
                    .addTo(map)
                    .bindPopup(`
                        <div class="w-[200px]">
                            <img src="/storage/${prop.media[0]?.file_path}" class="w-full h-24 object-cover rounded-md mb-2">
                            <b class="text-sm block mb-1">${prop.title}</b>
                            <span class="text-indigo-600 font-bold block mb-2">Rp ${new Intl.NumberFormat('id-ID').format(prop.price)}</span>
                            <a href="/property/${prop.id}/${prop.slug}" target="_blank" class="block w-full bg-indigo-600 text-white text-center text-xs py-1.5 rounded font-bold">
                                View Details
                            </a>
                        </div>
                    `);

                // Store marker reference
                markers[prop.id] = marker;

                // Click Event: Highlight Card
                marker.on('click', function() {
                    highlightCard(prop.id);
                });
            }
        });

        // Function: Focus on Map when Card Clicked
        function focusOnMap(lat, lng, id) {
            map.flyTo([lat, lng], 16, {
                animate: true,
                duration: 1.5
            });
            
            // Open Popup
            if(markers[id]) {
                markers[id].openPopup();
            }

            highlightCard(id);
        }

        // Function: Highlight Card in List
        function highlightCard(id) {
            // Remove active class from old card
            if (activeCardId) {
                var oldCard = document.getElementById('card-' + activeCardId);
                if (oldCard) oldCard.classList.remove('active');
            }

            // Add active class to new card
            var newCard = document.getElementById('card-' + id);
            if (newCard) {
                newCard.classList.add('active');
                newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            activeCardId = id;
        }

    </script>
</body>
</html>