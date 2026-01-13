<x-layout>
    
    <div class="relative h-[600px] w-full" 
         x-data="{ 
            activeSlide: 0, 
            {{-- FIX: Decode the JSON string from DB into a real Array --}}
            slides: {{ Js::from(json_decode($settings['hero_slides'] ?? '[]', true)) }},
            timer: null,
            init() {
                if(this.slides.length > 1) {
                    this.timer = setInterval(() => {
                        this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                    }, 5000);
                }
            }
         }">

        {{-- 1. BACKGROUND CAROUSEL (Inner container with overflow-hidden) --}}
        <div class="absolute inset-0 overflow-hidden">
            <template x-for="(slide, index) in slides" :key="index">
                <div class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
                     x-show="activeSlide === index"
                     x-transition:enter="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <img :src="'/storage/' + slide" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-black/30"></div>
                </div>
            </template>
            
            {{-- Fallback --}}
            @if(empty($settings['hero_slides']) || $settings['hero_slides'] == '[]')
                <div class="absolute inset-0 bg-gray-900">
                    <img src="https://images.unsplash.com/photo-1600596542815-2a4d9fdb580e?auto=format&fit=crop&w=2070&q=80" 
                         class="w-full h-full object-cover opacity-50">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                </div>
            @endif
        </div>

        {{-- 2. HERO TEXT --}}
        <div class="absolute inset-0 flex flex-col items-center justify-center pb-32 text-center z-10 px-4 pointer-events-none">
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-4 drop-shadow-lg tracking-tight">
                {{ $settings['hero_title'] ?? 'Find Your Dream Home' }}
            </h1>
            <p class="text-xl text-gray-200 drop-shadow-md max-w-2xl">
                {{ $settings['hero_subtitle'] ?? 'Search properties for sale and rent from top agents.' }}
            </p>
        </div>

        {{-- 3. SEARCH BAR (Z-Index 30 ensures it is on top) --}}
        <div class="absolute bottom-0 left-0 w-full z-30 translate-y-1/2 px-4 pointer-events-auto">
            <div class="max-w-5xl mx-auto">
                {{-- (Keep your existing Search Box Code here...) --}}
                <div class="bg-white rounded-xl shadow-2xl overflow-hidden ring-1 ring-black/5" 
                     x-data="{ tab: '{{ request('listing_type', 'SALE') }}' }">
                    
                    {{-- TABS --}}
                    <div class="flex border-b border-gray-100">
                        <button @click="tab = 'SALE'" 
                                class="flex-1 py-4 text-center font-bold text-lg transition-colors relative"
                                :class="tab === 'SALE' ? 'text-indigo-600 bg-white' : 'text-gray-500 bg-gray-50 hover:bg-gray-100'">
                            For Sale
                            <div x-show="tab === 'SALE'" class="absolute bottom-0 left-0 w-full h-1 bg-indigo-600"></div>
                        </button>
                        <button @click="tab = 'RENT'" 
                                class="flex-1 py-4 text-center font-bold text-lg transition-colors relative"
                                :class="tab === 'RENT' ? 'text-indigo-600 bg-white' : 'text-gray-500 bg-gray-50 hover:bg-gray-100'">
                            For Rent
                            <div x-show="tab === 'RENT'" class="absolute bottom-0 left-0 w-full h-1 bg-indigo-600"></div>
                        </button>
                    </div>

                    {{-- SEARCH FORM --}}
                    <form action="{{ route('home') }}" method="GET" class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        <input type="hidden" name="listing_type" :value="tab">

                        {{-- Location --}}
                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">Location</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400">📍</span>
                                <input type="text" name="search" value="{{ request('search') }}"
                                       class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-semibold text-gray-800"
                                       placeholder="City, District, or Keyword...">
                            </div>
                        </div>

                        {{-- Property Type --}}
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">Property Type</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400">🏠</span>
                                <select name="type" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-semibold text-gray-800 appearance-none">
                                    <option value="ALL">All Types</option>
                                    <option value="House">House</option>
                                    <option value="Apartment">Apartment</option>
                                    <option value="Villa">Villa</option>
                                    <option value="Land">Land</option>
                                </select>
                            </div>
                        </div>

                        {{-- Max Price --}}
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">Max Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400">💰</span>
                                <input type="number" name="max_price" placeholder="Any Price"
                                       class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-semibold text-gray-800">
                            </div>
                        </div>

                        {{-- Button --}}
                        <div class="md:col-span-2">
                            <button type="submit" class="w-full h-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                                Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="h-32 bg-gray-50"></div>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Latest Listings</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($properties as $property)
                <x-property-card :property="$property" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $properties->links() }}
        </div>

        @if($properties->count() == 0)
            <div class="text-center py-12 text-gray-500">
                No properties found. Please go to Admin Panel and change status to <strong>PUBLISHED</strong>.
            </div>
        @endif
    </div>

</x-layout>