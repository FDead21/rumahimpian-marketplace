<x-layout>

    {{-- HERO --}}
    @php
        $rawSlides = $eoSettings['eo_hero_slides'] ?? [];
        $heroSlides = is_string($rawSlides) ? json_decode($rawSlides, true) : $rawSlides;
        if (!is_array($heroSlides)) $heroSlides = [];
    @endphp
    <x-portal-hero />

    {{-- STATS BAR --}}
    <div class="bg-rose-600 text-white py-6">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div><div class="text-3xl font-extrabold">{{ $packages->count() }}+</div><div class="text-rose-200 text-sm mt-1">{{ __('Packages Available') }}</div></div>
            <div><div class="text-3xl font-extrabold">{{ $vendors->count() }}+</div><div class="text-rose-200 text-sm mt-1">{{ __('Trusted Vendors') }}</div></div>
        </div>
    </div>

    {{-- FEATURED PACKAGES --}}
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="flex justify-between items-end mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">{{ __('Our Packages') }}</h2>
                <p class="text-gray-500 mt-1">{{ __('Choose the perfect package for your event') }}</p>
            </div>
            <a href="{{ route('eventOrganizer.packages.index') }}" class="hidden md:flex items-center text-rose-600 font-bold hover:text-rose-700 transition">
                {{ __('View All Packages') }}
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($packages as $package)
            <div class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 relative">
                @if($package->is_featured)
                    <div class="absolute top-4 left-4 z-10 bg-rose-600 text-white text-xs font-bold px-3 py-1 rounded-full">{{ __('Featured') }}</div>
                @endif
                <div class="relative h-48 overflow-hidden">
                    @if($package->thumbnail)
                        <img src="{{ asset('storage/' . $package->thumbnail) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-rose-100 to-pink-200 flex items-center justify-center text-5xl"></div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-lg">
                        <span class="text-rose-700 font-extrabold text-lg">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="font-bold text-xl text-gray-900 mb-2 group-hover:text-rose-600 transition">{{ $package->name }}</h3>
                    @if($package->max_pax)
                        <p class="text-sm text-gray-500 mb-3">{{ __('Up to') }} {{ $package->max_pax }} {{ __('guests') }}</p>
                    @endif
                    @if($package->inclusions)
                        <ul class="space-y-1 mb-4">
                            @foreach(array_slice($package->inclusions, 0, 3) as $item)
                                <li class="text-sm text-gray-600 flex items-center gap-2">
                                    <span class="text-rose-500">✓</span> {{ $item['item'] ?? $item }}
                                </li>
                            @endforeach
                            @if(count($package->inclusions) > 3)
                                <li class="text-xs text-gray-400">+ {{ count($package->inclusions) - 3 }} {{ __('more inclusions') }}</li>
                            @endif
                        </ul>
                    @endif
                    <a href="{{ route('eventOrganizer.packages.show', $package->slug) }}"
                       class="block w-full text-center bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white font-bold py-2.5 rounded-xl border border-rose-200 hover:border-rose-600 transition">
                        {{ __('View Details') }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- GALLERY PREVIEW --}}
    @if($galleryEvents->count() > 0)
    <div class="bg-gray-50 border-t border-gray-200 py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">{{ __('Our Work') }}</h2>
                    <p class="text-gray-500 mt-1">{{ __('A glimpse of events we\'ve organized') }}</p>
                </div>
                <a href="{{ route('eventOrganizer.gallery.index') }}" class="hidden md:flex items-center text-rose-600 font-bold hover:text-rose-700 transition">
                    {{ __('View Full Gallery') }}
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($galleryEvents->take(4) as $event)
                <a href="{{ route('eventOrganizer.gallery.index', $event->slug) }}"
                   class="group relative h-48 rounded-2xl overflow-hidden">
                    @if($event->cover_photo)
                        <img src="{{ asset('storage/' . $event->cover_photo) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-rose-200 to-pink-300 flex items-center justify-center text-4xl"></div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute bottom-3 left-3 right-3 text-white text-sm font-bold opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        {{ $event->title }}
                    </div>
                    @if($event->event_type)
                        <div class="absolute top-3 left-3 bg-rose-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                            {{ $event->event_type }}
                        </div>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- CTA BANNER --}}
    <div class="bg-gradient-to-r from-rose-600 to-pink-600 py-16 text-white text-center">
        <div class="max-w-3xl mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-extrabold mb-4">{{ __('Ready to plan your dream event?') }}</h2>
            <p class="text-rose-100 text-lg mb-8">{{ __('Let\'s make it unforgettable. Reach out to us today.') }}</p>
            <a href="{{ route('eventOrganizer.booking.create') }}"
               class="bg-white text-rose-600 hover:bg-rose-50 font-bold px-10 py-4 rounded-xl shadow-xl transition transform hover:-translate-y-1 text-lg inline-block">
                 {{ __('Book Your Event Now') }}
            </a>
        </div>
    </div>

</x-layout>