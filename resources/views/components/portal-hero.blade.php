@php
    $isEo = request()->is('eventOrganizer*');
    $isRental = request()->is('rental*'); 
    $isTour = request()->is('tour*');
    
    $initialTab = $isTour ? "'tours'" : ($isEo ? "'events'" : ($isRental ? "'rentals'" : "'property'"));

    // Fetch Global Settings
    $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

    // Helper to get the first image from JSON slides, or fallback to Unsplash
    $getImage = function($key, $fallback) use ($settings) {
        if (!empty($settings[$key])) {
            $slides = json_decode($settings[$key], true);
            if (is_array($slides) && count($slides) > 0) {
                return asset('storage/' . $slides[0]);
            }
        }
        return $fallback;
    };

    // Images
    $propImg = $getImage('hero_slides', 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=2000&q=80');
    $eoImg = $getImage('eo_hero_slides', 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=2000&q=80');
    $tourImg = $getImage('tour_hero_slides', 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=2000&q=80');
    $rentalImg = $getImage('rental_hero_slides', 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=2000&q=80');

    // Texts (With translation fallbacks)
    $propTitle = $settings['hero_title'] ?? __('Find Your Perfect Home in Indonesia');
    $propSub = $settings['hero_subtitle'] ?? __('Search thousands of verified listings.');
    
    $eoTitle = $settings['eo_hero_title'] ?? __('Unforgettable Events, Perfectly Organized');
    $eoSub = $settings['eo_hero_subtitle'] ?? __('Premium venues, top catering, and all-in-one packages.');
    
    $tourTitle = $settings['tour_hero_title'] ?? __('Explore Breathtaking Destinations');
    $tourSub = $settings['tour_hero_subtitle'] ?? __('Discover cultural tours, hikes, and adventure packages.');
    
    $rentalTitle = $settings['rental_hero_title'] ?? __('Rent the Perfect Vehicle for Your Trip');
    $rentalSub = $settings['rental_hero_subtitle'] ?? __('Cars, motorbikes, and boats at the best prices.');
@endphp

{{-- ========================================================= --}}
{{-- TRUE UNIFIED GLOBAL HERO SECTION (Alpine Powered)         --}}
{{-- ========================================================= --}}
<div class="relative bg-slate-900 min-h-[70vh] md:min-h-[85vh] flex flex-col pt-36 md:pt-48" 
    x-data="{ 
        activeTab: {{ $initialTab }},
        images: { property: '{{ $propImg }}', events: '{{ $eoImg }}', tours: '{{ $tourImg }}', rentals: '{{ $rentalImg }}' },
        titles: { property: '{{ addslashes($propTitle) }}', events: '{{ addslashes($eoTitle) }}', tours: '{{ addslashes($tourTitle) }}', rentals: '{{ addslashes($rentalTitle) }}' },
        subtitles: { property: '{{ addslashes($propSub) }}', events: '{{ addslashes($eoSub) }}', tours: '{{ addslashes($tourSub) }}', rentals: '{{ addslashes($rentalSub) }}' },
        routes: { property: '{{ route('property.home') }}', events: '{{ route('eventOrganizer.packages.index') }}', tours: '{{ route('tour.home') ?? '#' }}', rentals: '{{ route('rental.home') ?? '#' }}' },
        placeholders: { 
            property: '{{ __('City, district, or property name') }}', 
            events: '{{ __('Search packages or vendors') }}', 
            tours: '{{ __('Search destinations (e.g., Bali, Temple)') }}', 
            rentals: '{{ __('Search vehicles (e.g., Vespa, Alphard)') }}' 
        },
        icons: { property: '', events: '', tours: '', rentals: '' }
    }">  

    {{-- Dynamic Background Image --}}
    <div class="absolute inset-0 overflow-hidden bg-slate-900">
        <img :src="images[activeTab]" 
            alt="Background" 
            class="w-full h-full object-cover opacity-40 transition-opacity duration-700 ease-in-out"
            x-transition>
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/80 via-slate-900/40 to-slate-900"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        
        {{-- Module Tabs --}}
        <div class="flex items-center gap-2 mb-6 overflow-x-auto hide-scrollbar">
            <button type="button" @click="activeTab = 'property'" 
                    class="flex items-center gap-2 px-5 py-2.5 rounded-full font-bold text-sm transition-all whitespace-nowrap"
                    :class="activeTab === 'property' ? 'bg-white text-slate-900 shadow-md' : 'bg-white/10 text-white hover:bg-white/20'">
                {{ __('Property') }}
            </button>
            
            <button type="button" @click="activeTab = 'events'" 
                    class="flex items-center gap-2 px-5 py-2.5 rounded-full font-bold text-sm transition-all whitespace-nowrap"
                    :class="activeTab === 'events' ? 'bg-white text-slate-900 shadow-md' : 'bg-white/10 text-white hover:bg-white/20'">
                {{ __('Event Organizer') }}
            </button>
            
            <button type="button" @click="activeTab = 'tours'" 
                    class="flex items-center gap-2 px-5 py-2.5 rounded-full font-bold text-sm transition-all whitespace-nowrap"
                    :class="activeTab === 'tours' ? 'bg-white text-slate-900 shadow-md' : 'bg-white/10 text-white hover:bg-white/20'">
                {{ __('Tours') }}
            </button>
            
            <button type="button" @click="activeTab = 'rentals'" 
                    class="flex items-center gap-2 px-5 py-2.5 rounded-full font-bold text-sm transition-all whitespace-nowrap"
                    :class="activeTab === 'rentals' ? 'bg-white text-slate-900 shadow-md' : 'bg-white/10 text-white hover:bg-white/20'">
                {{ __('Rentals') }}
            </button>
        </div>

        {{-- Dynamic Hero Title --}}
        <div class="text-center md:text-left mb-8 md:min-h-[110px]">
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-4 drop-shadow-lg tracking-tight" x-text="titles[activeTab]"></h1>
            <p class="text-gray-200 text-lg md:text-xl drop-shadow-md" x-text="subtitles[activeTab]"></p>
        </div>

        {{-- ONE UNIFIED SMART FORM --}}
        <div class="w-full max-w-5xl">
            <form method="GET" class="flex flex-col md:flex-row w-full bg-white rounded-2xl shadow-2xl p-1 gap-1" :action="routes[activeTab]">
                
                {{-- Dynamic Input Area --}}
                <div class="flex-1 relative flex items-center bg-transparent border-b md:border-b-0" :class="activeTab === 'property' ? 'md:border-r border-gray-200' : ''">
                    <span class="pl-4 text-gray-400 text-xl" x-text="icons[activeTab]"></span>
                    
                    <input type="text" name="search" value="{{ request('search') }}"
                           :placeholder="placeholders[activeTab]"
                           class="w-full pl-3 pr-4 py-3.5 bg-transparent border-none focus:ring-0 outline-none font-medium text-gray-800 placeholder-gray-400">
                </div>
                
                {{-- Property Type Dropdown --}}
                <div x-show="activeTab === 'property'" style="display:none;" class="md:w-64 relative flex items-center bg-transparent">
                    <span class="pl-4 text-gray-400 text-xl"></span>
                    <select name="type" :disabled="activeTab !== 'property'" class="w-full pl-3 pr-8 py-3.5 bg-transparent border-none focus:ring-0 outline-none font-medium text-gray-800 appearance-none cursor-pointer">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="House" {{ request('type') == 'House' ? 'selected' : '' }}>{{ __('House') }}</option>
                        <option value="Apartment" {{ request('type') == 'Apartment' ? 'selected' : '' }}>{{ __('Apartment') }}</option>
                        <option value="Villa" {{ request('type') == 'Villa' ? 'selected' : '' }}>{{ __('Villa') }}</option>
                    </select>
                    <span class="absolute right-4 text-gray-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </span>
                </div>
                
                {{-- Submit Button --}}
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3.5 px-10 rounded-xl transition flex items-center justify-center gap-2 md:w-auto w-full shrink-0 shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span class="md:hidden">{{ __('Search') }}</span>
                </button>
            </form>
        </div>

    </div>
</div>