<x-layout>
    <div class="max-w-7xl mx-auto px-4 py-10">
        <h1 class="text-3xl font-bold mb-6">Property Comparison</h1>

        @if($properties->count() < 2)
            <div class="p-10 text-center bg-gray-50 rounded-xl">
                <p class="text-gray-500 mb-4">Select at least 2 properties to compare.</p>
                <a href="{{ route('home') }}" class="text-indigo-600 font-bold underline">Browse Properties</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <tr>
                        <td class="p-4 bg-gray-50 font-bold text-gray-500 w-48">Property</td>
                        @foreach($properties as $p)
                        <td class="p-4 border-b min-w-[250px]">
                            <div class="h-40 w-full bg-gray-200 rounded-lg overflow-hidden mb-3">
                                @if($p->media->first())
                                <img src="{{ asset('storage/' . $p->media->first()->file_path) }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <h3 class="font-bold text-lg">{{ Str::limit($p->title, 30) }}</h3>
                            <p class="text-indigo-600 font-bold text-lg">@currency($p->price)</p>
                            <a href="{{ route('property.show', [$p->id, $p->slug]) }}" target="_blank" class="text-xs text-indigo-500 underline mt-1 block">View Details ></a>
                        </td>
                        @endforeach
                    </tr>

                    <tr>
                        <td class="p-4 bg-gray-50 font-bold text-gray-500 border-t">Location</td>
                        @foreach($properties as $p)
                        <td class="p-4 border-t border-gray-100">
                            {{ $p->district }}, {{ $p->city }}
                        </td>
                        @endforeach
                    </tr>

                    <tr>
                        <td class="p-4 bg-gray-50 font-bold text-gray-500 border-t">Specs</td>
                        @foreach($properties as $p)
                        <td class="p-4 border-t border-gray-100 space-y-1">
                            <div class="flex justify-between text-sm"><span>🛏 Beds:</span> <b>{{ $p->bedrooms }}</b></div>
                            <div class="flex justify-between text-sm"><span>🚿 Baths:</span> <b>{{ $p->bathrooms }}</b></div>
                            <div class="flex justify-between text-sm"><span>🏠 Size:</span> <b>{{ $p->building_area }} m²</b></div>
                        </td>
                        @endforeach
                    </tr>

                    <tr>
                        <td class="p-4 bg-gray-50 font-bold text-gray-500 border-t">Price / m²</td>
                        @foreach($properties as $p)
                        <td class="p-4 border-t border-gray-100">
                            @php $pricePerM = $p->price / $p->building_area; @endphp
                            <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">
                                @currency($pricePerM) / m²
                            </span>
                        </td>
                        @endforeach
                    </tr>
                </table>
            </div>
        @endif
    </div>
</x-layout>