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
                {{ __($settings['hero_title'] ?? 'Find Your Dream Home') }}
            </h1>
            <p class="text-xl text-gray-200 drop-shadow-md max-w-2xl">
                {{ __($settings['hero_subtitle'] ?? 'Search properties for sale and rent from top agents.') }}
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
                            {{ __('For Sale') }}
                            <div x-show="tab === 'SALE'" class="absolute bottom-0 left-0 w-full h-1 bg-indigo-600"></div>
                        </button>
                        <button @click="tab = 'RENT'" 
                                class="flex-1 py-4 text-center font-bold text-lg transition-colors relative"
                                :class="tab === 'RENT' ? 'text-indigo-600 bg-white' : 'text-gray-500 bg-gray-50 hover:bg-gray-100'">
                            {{ __('For Rent') }}
                            <div x-show="tab === 'RENT'" class="absolute bottom-0 left-0 w-full h-1 bg-indigo-600"></div>
                        </button>
                    </div>

                    {{-- SEARCH FORM --}}
                    <form action="{{ route('home') }}" method="GET" class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        <input type="hidden" name="listing_type" :value="tab">

                        {{-- Location --}}
                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">{{ __('Location') }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400">📍</span>
                                <input type="text" name="search" value="{{ request('search') }}"
                                       class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-semibold text-gray-800"
                                       placeholder="{{ __('City, District, or Keyword...') }}">
                            </div>
                        </div>

                        {{-- Property Type --}}
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">{{ __('Property Type') }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400">🏠</span>
                                <select name="type" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-semibold text-gray-800 appearance-none">
                                    <option value="ALL">{{ __('All Types') }}</option>
                                    <option value="House">{{ __('House') }}</option>
                                    <option value="Apartment">{{ __('Apartment') }}</option>
                                    <option value="Villa">{{ __('Villa') }}</option>
                                    <option value="Land">{{ __('Land') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- Max Price --}}
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">{{ __('Max Price') }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400">💰</span>
                                <input type="number" name="max_price" placeholder="{{ __('Any Price') }}"
                                       class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-semibold text-gray-800">
                            </div>
                        </div>

                        {{-- Button --}}
                        <div class="md:col-span-2">
                            <button type="submit" class="w-full h-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                                {{ __('Search') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="h-32 bg-gray-50"></div>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('Latest Listings') }}</h2>
        
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
                {{ __('No properties found. Please go to Admin Panel and change status to') }} <strong>PUBLISHED</strong>.
            </div>
        @endif
    </div>

    @if(isset($latestArticles) && $latestArticles->count() > 0)
    <div class="bg-gray-50 py-16 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">{{ __('Latest Property News') }}</h2>
                    <p class="text-gray-500 mt-2">{{ __('Insights, trends, and tips for your property journey.') }}</p>
                </div>
                <a href="{{ route('articles.index') }}" class="hidden md:flex items-center text-indigo-600 font-bold hover:text-indigo-700 transition">
                    {{ __('View All News') }} 
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($latestArticles as $article)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition duration-300 overflow-hidden flex flex-col h-full border border-gray-100 group">
                    <a href="{{ route('articles.show', $article->slug) }}" class="relative h-48 overflow-hidden block">
                        @if($article->thumbnail)
                            <img src="{{ asset('storage/' . $article->thumbnail) }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                <span class="text-4xl">📰</span>
                            </div>
                        @endif
                        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-gray-800 shadow-sm">
                            {{ $article->published_at->format('M d, Y') }}
                        </div>
                    </a>
                    
                    <div class="p-6 flex-1 flex flex-col">
                        <a href="{{ route('articles.show', $article->slug) }}" class="block mb-3">
                            <h3 class="text-xl font-bold text-gray-900 group-hover:text-indigo-600 transition line-clamp-2">
                                {{ $article->title }}
                            </h3>
                        </a>
                        <p class="text-gray-500 text-sm mb-4 line-clamp-3 flex-1">
                            {{ Str::limit(strip_tags($article->content), 120) }}
                        </p>
                        <a href="{{ route('articles.show', $article->slug) }}" class="inline-flex items-center text-indigo-600 font-bold text-sm hover:underline mt-auto">
                            {{ __('Read Article') }} 
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8 md:hidden text-center">
                <a href="{{ route('articles.index') }}" class="inline-block px-6 py-3 bg-white border border-gray-300 rounded-lg text-gray-700 font-bold hover:bg-gray-50">
                    {{ __('View All News') }}
                </a>
            </div>
        </div>
    </div>
    @endif

</x-layout>