<x-layout>
    {{-- ========================================================= --}}
    {{-- 1. TRAVELOKA-STYLE HERO & SEARCH WIDGET                   --}}
    {{-- ========================================================= --}}
    {{-- NEW: Added negative top margin and massive padding to stretch under navbar --}}
   <x-portal-hero />

    {{-- ========================================================= --}}
    {{-- 2. MODULE CATEGORY IMAGE CARDS                            --}}
    {{-- ========================================================= --}}
    <div class="bg-white py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">{{ __('Our Categories') }}</p>
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ __('Choose Your Ideal Experience') }}</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                {{-- Property Card --}}
                <a href="{{ route('property.home') }}" class="group relative h-64 md:h-80 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500">
                    <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80" 
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                    
                    <div class="absolute bottom-0 left-0 right-0 p-6 flex flex-col items-start text-white">
                        <h3 class="text-xl font-bold mb-1">{{ __('Property') }}</h3>
                        <p class="text-sm text-gray-300 mb-4 line-clamp-2">{{ __('Rent the best vacation place you ever wanted.') }}</p>
                        <div class="bg-white/20 backdrop-blur-md hover:bg-white/30 border border-white/30 text-white text-xs font-bold px-4 py-2 rounded-full transition flex items-center gap-2">
                            {{ __('Read More') }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>
                    </div>
                </a>

                {{-- Events Card --}}
                <a href="{{ route('eventOrganizer.home') }}" class="group relative h-64 md:h-80 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500">
                    <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80" 
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                    
                    <div class="absolute bottom-0 left-0 right-0 p-6 flex flex-col items-start text-white">
                        <h3 class="text-xl font-bold mb-1">{{ __('Event Organizer') }}</h3>
                        <p class="text-sm text-gray-300 mb-4 line-clamp-2">{{ __('Premium venues and full-service packages.') }}</p>
                        <div class="bg-white/20 backdrop-blur-md hover:bg-white/30 border border-white/30 text-white text-xs font-bold px-4 py-2 rounded-full transition flex items-center gap-2">
                            {{ __('Read More') }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>
                    </div>
                </a>

                {{-- Tours Card --}}
                <a href="{{ route('tour.home') }}" class="group relative h-64 md:h-80 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500">
                    <img src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80" 
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                    
                    <div class="absolute bottom-0 left-0 right-0 p-6 flex flex-col items-start text-white">
                        <h3 class="text-xl font-bold mb-1">{{ __('Tours & Travel') }}</h3>
                        <p class="text-sm text-gray-300 mb-4 line-clamp-2">{{ __('Explore breathtaking destinations.') }}</p>
                        <div class="bg-white/20 backdrop-blur-md hover:bg-white/30 border border-white/30 text-white text-xs font-bold px-4 py-2 rounded-full transition flex items-center gap-2">
                            {{ __('Read More') }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>
                    </div>
                </a>

                {{-- Rentals Card --}}
                <a href="{{ route('rental.home') }}" class="group relative h-64 md:h-80 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500">
                    <img src="https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=800&q=80" 
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                    
                    <div class="absolute bottom-0 left-0 right-0 p-6 flex flex-col items-start text-white">
                        <h3 class="text-xl font-bold mb-1">{{ __('Vehicle Rentals') }}</h3>
                        <p class="text-sm text-gray-300 mb-4 line-clamp-2">{{ __('Cars, scooters, and boats for your trip.') }}</p>
                        <div class="bg-white/20 backdrop-blur-md hover:bg-white/30 border border-white/30 text-white text-xs font-bold px-4 py-2 rounded-full transition flex items-center gap-2">
                            {{ __('Read More') }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>
                    </div>
                </a>

            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- 3. CONTENT RIBBON: PROPERTIES                             --}}
    {{-- ========================================================= --}}
    @if(isset($featuredProperties) && $featuredProperties->count() > 0)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ __('Trending Properties') }}</h2>
                <p class="text-gray-500 mt-1">{{ __('Explore our most sought-after homes.') }}</p>
            </div>
            <a href="{{ route('property.home') }}" class="hidden md:block text-sky-600 font-bold hover:underline">{{ __('View All') }} &rarr;</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($featuredProperties as $property)
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-xl transition group">
                    {{-- FIXED ROUTE HERE --}}
                    <a href="{{ route('property.show', [$property->id, $property->slug]) }}" class="block h-48 bg-gray-200 relative overflow-hidden">
                        @if($property->media->count())
                            <img src="{{ asset('storage/' . $property->media->first()->file_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @endif
                        <div class="absolute top-2 left-2 bg-sky-500 text-white text-[10px] font-bold px-2 py-1 rounded-md">{{ __($property->listing_type) }}</div>
                    </a>
                    <div class="p-4">
                        {{-- FIXED ROUTE HERE --}}
                        <a href="{{ route('property.show', [$property->id, $property->slug]) }}">
                            <h3 class="font-bold text-gray-900 truncate hover:text-sky-600 transition">{{ $property->title }}</h3>
                        </a>
                        <p class="text-sky-600 font-extrabold mt-1">Rp {{ number_format($property->price, 0, ',', '.') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ========================================================= --}}
    {{-- 4. CONTENT RIBBON: EVENT PACKAGES                         --}}
    {{-- ========================================================= --}}
    @if(isset($featuredPackages) && $featuredPackages->count() > 0)
    <div class="bg-slate-50 py-16 mb-16 border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ __('Featured Event Packages') }}</h2>
                    <p class="text-gray-500 mt-1">{{ __('Curated packages for unforgettable moments.') }}</p>
                </div>
                <a href="{{ route('eventOrganizer.packages.index') }}" class="hidden md:block text-rose-600 font-bold hover:underline">{{ __('View All') }} &rarr;</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($featuredPackages as $package)
                    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-xl transition group">
                        <a href="{{ route('eventOrganizer.packages.show', $package->slug) }}" class="block h-48 bg-gray-200 relative overflow-hidden">
                            @if($package->thumbnail)
                                <img src="{{ asset('storage/' . $package->thumbnail) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @endif
                        </a>
                        <div class="p-4">
                            <a href="{{ route('eventOrganizer.packages.show', $package->slug) }}">
                                <h3 class="font-bold text-gray-900 truncate hover:text-rose-600 transition">{{ $package->name }}</h3>
                            </a>
                            <p class="text-xs text-gray-500 mt-1 mb-2">👥 {{ __('Up to') }} {{ $package->max_pax }} {{ __('guests') }}</p>
                            <p class="text-rose-600 font-extrabold">Rp {{ number_format($package->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ========================================================= --}}
    {{-- 7. CONTENT RIBBON:   TOURS                                --}}
    {{-- ========================================================= --}}
    @if(isset($featuredTours) && $featuredTours->count() > 0)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ __('Popular Tours & Activities') }}</h2>
                <p class="text-gray-500 mt-1">{{ __('Discover authentic cultural experiences and adventures.') }}</p>
            </div>
            {{-- Make sure this matches your actual route name for tours index --}}
            <a href="{{ route('tour.home') }}" class="hidden md:block text-emerald-600 font-bold hover:underline">{{ __('View All Tours') }} &rarr;</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($featuredTours as $tour)
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-xl transition group">
                    {{-- Note: Update the route name here if it's different in your web.php --}}
                    <a href="{{ route('tour.tours.show', $tour->slug) }}" class="block h-48 bg-gray-200 relative overflow-hidden">
                        @if($tour->coverImage())
                            <img src="{{ asset('storage/' . $tour->coverImage()) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full bg-emerald-50 flex items-center justify-center text-4xl">🗺️</div>
                        @endif
                        <div class="absolute top-3 left-3 bg-emerald-500 text-white text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider shadow-sm">
                            {{ $tour->category_label }}
                        </div>
                    </a>
                    <div class="p-4">
                        <a href="{{ route('tour.tours.show', $tour->slug) }}">
                            <h3 class="font-bold text-gray-900 line-clamp-2 hover:text-emerald-600 transition min-h-[2.5rem]">{{ $tour->name }}</h3>
                        </a>
                        <div class="flex items-center justify-between mt-3">
                            <p class="text-xs text-gray-500 font-medium">⏱ {{ $tour->duration_days }} {{ __('Days') }}</p>
                            <p class="text-emerald-600 font-extrabold text-sm">Rp {{ number_format($tour->price_per_person, 0, ',', '.') }}<span class="text-[10px] font-normal text-gray-400">/pax</span></p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif


    {{-- ========================================================= --}}
    {{-- 6. CONTENT RIBBON: VEHICLE RENTALS (By Category)          --}}
    {{-- ========================================================= --}}
    @if((isset($rentalCars) && $rentalCars->count() > 0) || (isset($rentalBikes) && $rentalBikes->count() > 0) || (isset($rentalBoats) && $rentalBoats->count() > 0))
    <div class="bg-slate-900 py-16 mb-16 border-y border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-2xl md:text-3xl font-extrabold text-white">{{ __('Premium Vehicle Rentals') }}</h2>
                <p class="text-slate-400 mt-2">{{ __('Drive in style. Choose from our curated fleet of vehicles.') }}</p>
            </div>

            {{-- 1. ROW: CARS --}}
            @if(isset($rentalCars) && $rentalCars->count() > 0)
                <div class="mb-10">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">🚗 {{ __('Cars & SUVs') }}</h3>
                        <a href="{{ route('rental.vehicles.index', ['type' => 'car']) }}" class="text-sm text-slate-400 hover:text-white transition">{{ __('See all cars') }} &rarr;</a>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($rentalCars as $car)
                            @include('components.rental-card-small', ['vehicle' => $car])
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 2. ROW: MOTORBIKES --}}
            @if(isset($rentalBikes) && $rentalBikes->count() > 0)
                <div class="mb-10">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">🛵 {{ __('Motorbikes & Scooters') }}</h3>
                        <a href="{{ route('rental.vehicles.index', ['type' => 'motorbike']) }}" class="text-sm text-slate-400 hover:text-white transition">{{ __('See all motorbikes') }} &rarr;</a>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($rentalBikes as $bike)
                            @include('components.rental-card-small', ['vehicle' => $bike])
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 3. ROW: BOATS --}}
            @if(isset($rentalBoats) && $rentalBoats->count() > 0)
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">⛵ {{ __('Yachts & Boats') }}</h3>
                        <a href="{{ route('rental.vehicles.index', ['type' => 'boat']) }}" class="text-sm text-slate-400 hover:text-white transition">{{ __('See all boats') }} &rarr;</a>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($rentalBoats as $boat)
                            @include('components.rental-card-small', ['vehicle' => $boat])
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
    @endif

    {{-- ========================================================= --}}
    {{-- 7. CONTENT RIBBON: ARTICLES                               --}}
    {{-- ========================================================= --}}
    @if(isset($articles) && $articles->count() > 0)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ __('Latest News & Inspiration') }}</h2>
                <p class="text-gray-500 mt-1">{{ __('Tips, market trends, and guides.') }}</p>
            </div>
            <a href="{{ route('articles.index') }}" class="hidden md:block text-gray-900 font-bold hover:underline">{{ __('Read More') }} &rarr;</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($articles->take(3) as $article)
                <a href="{{ route('articles.show', $article->slug) }}" class="group block">
                    <div class="h-48 rounded-2xl bg-gray-200 mb-4 overflow-hidden">
                        @if($article->thumbnail)
                            <img src="{{ asset('storage/' . $article->thumbnail) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @endif
                    </div>
                    <p class="text-xs text-sky-600 font-bold mb-1">{{ $article->published_at->translatedFormat('d M Y') }}</p>
                    <h3 class="font-bold text-gray-900 text-lg group-hover:text-sky-600 transition">{{ $article->title }}</h3>
                </a>
            @endforeach
        </div>
    </div>
    @endif

</x-layout>