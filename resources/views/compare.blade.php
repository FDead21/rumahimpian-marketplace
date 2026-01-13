<x-layout>
    <div class="max-w-7xl mx-auto px-4 py-10">
        <h1 class="text-3xl font-bold mb-6">Property Comparison</h1>

        @if($properties->count() < 1)
            {{-- Handle case where user removes all items --}}
            <div class="p-10 text-center bg-gray-50 rounded-xl">
                <p class="text-gray-500 mb-4">No properties selected.</p>
                <a href="{{ route('home') }}" class="text-indigo-600 font-bold underline">Browse Properties</a>
            </div>
        @elseif($properties->count() < 2)
            <div class="p-10 text-center bg-gray-50 rounded-xl">
                <p class="text-gray-500 mb-4">Select at least 2 properties to compare effectively.</p>
                <a href="{{ route('home') }}" class="text-indigo-600 font-bold underline">Browse Properties</a>
            </div>
            {{-- Still show the single property table below so they can see/remove it --}}
        @endif

        @if($properties->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed">
                    {{-- Row 1: Property Image, Title, Price, Remove Button --}}
                    <tr>
                        <td class="p-4 bg-gray-50 font-bold text-gray-500 w-48 align-top">Property</td>
                        @foreach($properties as $p)
                        <td class="p-4 border-b min-w-[250px] align-top relative group">
                            {{-- Remove Button --}}
                            <div class="absolute top-2 right-2 z-10">
                                <button onclick="removeProperty({{ $p->id }})" 
                                        class="bg-white/90 hover:bg-red-50 text-gray-400 hover:text-red-600 rounded-full p-1 shadow-sm transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>

                            <div class="h-40 w-full bg-gray-200 rounded-lg overflow-hidden mb-3 relative">
                                @if($p->media->first())
                                <img src="{{ asset('storage/' . $p->media->first()->file_path) }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <h3 class="font-bold text-lg leading-tight mb-1">{{ Str::limit($p->title, 40) }}</h3>
                            <p class="text-indigo-600 font-bold text-lg">@currency($p->price)</p>
                            <a href="{{ route('property.show', [$p->id, $p->slug]) }}" target="_blank" class="text-xs text-indigo-500 underline mt-2 block">View Details ></a>
                        </td>
                        @endforeach
                    </tr>

                    {{-- Row 2: Listing Type (Rent/Sale) --}}
                    <tr>
                        <td class="p-4 bg-gray-50 font-bold text-gray-500 border-t">Type</td>
                        @foreach($properties as $p)
                        <td class="p-4 border-t border-gray-100">
                            <span class="inline-block px-2 py-1 text-xs font-bold rounded {{ $p->listing_type == 'SALE' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                {{ ucfirst(strtolower($p->listing_type)) }}
                            </span>
                            <span class="text-gray-600 text-sm ml-2">{{ $p->property_type }}</span>
                        </td>
                        @endforeach
                    </tr>

                    {{-- Row 3: Location --}}
                    <tr>
                        <td class="p-4 bg-gray-50 font-bold text-gray-500 border-t">Location</td>
                        @foreach($properties as $p)
                        <td class="p-4 border-t border-gray-100">
                            {{ $p->district }}, {{ $p->city }}
                        </td>
                        @endforeach
                    </tr>

                    {{-- Row 4: Specs (Beds, Baths, Areas) --}}
                    <tr>
                        <td class="p-4 bg-gray-50 font-bold text-gray-500 border-t">Specs</td>
                        @foreach($properties as $p)
                        <td class="p-4 border-t border-gray-100 space-y-2">
                            <div class="flex justify-between text-sm border-b border-gray-100 pb-1">
                                <span class="text-gray-500">🛏 Beds</span> 
                                <span class="font-bold">{{ $p->bedrooms }}</span>
                            </div>
                            <div class="flex justify-between text-sm border-b border-gray-100 pb-1">
                                <span class="text-gray-500">🚿 Baths</span> 
                                <span class="font-bold">{{ $p->bathrooms }}</span>
                            </div>
                            <div class="flex justify-between text-sm border-b border-gray-100 pb-1">
                                <span class="text-gray-500">🏠 Building</span> 
                                <span class="font-bold">{{ $p->building_area }} m²</span>
                            </div>
                            @if($p->land_area)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">🌳 Land</span> 
                                <span class="font-bold">{{ $p->land_area }} m²</span>
                            </div>
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    {{-- Row 5: Price per m² --}}
                    <tr>
                        <td class="p-4 bg-gray-50 font-bold text-gray-500 border-t">Price / m²</td>
                        @foreach($properties as $p)
                        <td class="p-4 border-t border-gray-100">
                            @if($p->building_area > 0)
                                @php $pricePerM = $p->price / $p->building_area; @endphp
                                <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded text-gray-600">
                                    @currency($pricePerM) / m²
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                </table>
            </div>
        @endif
    </div>

    {{-- Script to handle Removal --}}
    <script>
        function removeProperty(id) {
            // 1. Get current IDs from LocalStorage
            let stored = JSON.parse(localStorage.getItem('compare_ids') || '[]');
            
            // 2. Filter out the ID to remove
            stored = stored.filter(i => i != id);
            
            // 3. Update LocalStorage
            localStorage.setItem('compare_ids', JSON.stringify(stored));
            
            // 4. Dispatch event (to update the floating bar count immediately)
            window.dispatchEvent(new CustomEvent('compare-updated'));

            // 5. Reload the page with the new ID list
            if (stored.length > 0) {
                window.location.href = '/compare?ids=' + stored.join(',');
            } else {
                window.location.href = '/compare'; // Or redirect to home
            }
        }
    </script>
</x-layout>