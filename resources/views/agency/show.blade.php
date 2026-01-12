<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $agency->name }} - Real Estate Office</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    @include('components.navbar')

    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-12 flex flex-col md:flex-row items-center gap-8">
            
            <div class="w-32 h-32 bg-gray-100 rounded-2xl border flex items-center justify-center overflow-hidden flex-shrink-0">
                @if($agency->logo)
                    <img src="{{ asset('storage/' . $agency->logo) }}" class="w-full h-full object-contain p-2">
                @else
                    <span class="text-4xl font-bold text-gray-300">{{ substr($agency->name, 0, 1) }}</span>
                @endif
            </div>

            <div class="text-center md:text-left flex-grow">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $agency->name }}</h1>
                <div class="space-y-1 text-gray-600">
                    <p class="flex items-center justify-center md:justify-start gap-2">
                        📍 {{ $agency->address ?? 'Indonesia' }}
                    </p>
                    <p class="flex items-center justify-center md:justify-start gap-2">
                        📞 {{ $agency->phone ?? 'Contact Agent' }}
                    </p>
                    <p class="flex items-center justify-center md:justify-start gap-2 text-indigo-600 font-bold">
                        👥 {{ $agency->agents->count() }} Agents Registered
                    </p>
                </div>
            </div>

        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12 flex-grow w-full">
        <h2 class="text-xl font-bold text-gray-900 mb-6 border-l-4 border-indigo-600 pl-4">
            Properties by {{ $agency->name }}
        </h2>

        @if($properties->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($properties as $property)
                    <x-property-card :property="$property" />

                    <div class="absolute top-4 right-4 z-10">
                        <button x-data 
                                @click="$store.wishlist.toggle({{ $property->id }})"
                                class="bg-white/90 backdrop-blur-sm p-2 rounded-full shadow-sm hover:scale-110 transition active:scale-95 group/heart">
                            
                            <svg x-show="!$store.wishlist.has({{ $property->id }})" 
                                    class="w-5 h-5 text-gray-600 group-hover/heart:text-red-500 transition" 
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>

                            <svg x-show="$store.wishlist.has({{ $property->id }})" 
                                    style="display: none;"
                                    class="w-5 h-5 text-red-500 fill-current" 
                                    viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8">
                {{ $properties->links() }}
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                <p class="text-gray-500">No active listings found for this agency.</p>
            </div>
        @endif
    </div>

    @include('components.footer')
</body>
</html>