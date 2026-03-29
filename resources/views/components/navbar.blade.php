@php
    // 1. Detect the current module
    $isEo = request()->is('eventOrganizer*');
    $isRental = request()->is('rental*');
    $isTour = request()->is('tour*');
    $isProperty = request()->is('property*') || request()->is('map*') || request()->is('agency*') || request()->is('agent*');
    $isPortal = request()->routeIs('home'); 
    $isHomePage = request()->routeIs('home') || 
                  request()->routeIs('property.home') || 
                  request()->routeIs('eventOrganizer.home') || 
                  request()->routeIs('rental.home') ||
                  request()->routeIs('tour.home'); 

    // 2. Set Theme Colors dynamically
    $themeText = 'text-sky-600';
    $themeBg = 'bg-sky-600 hover:bg-sky-700';
    $themeBorder = 'hover:border-sky-600';
@endphp

<nav class="fixed w-full top-0 z-[100] transition-all duration-500 font-sans" 
     :class="scrolled || !{{ $isHomePage ? 'true' : 'false' }} ? 'bg-white shadow-md py-2' : 'bg-transparent py-4'"
     x-data="{ mobileOpen: false, scrolled: false }" 
     @scroll.window="scrolled = (window.pageYOffset > 20)">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-14 md:h-16">

            {{-- 1. LEFT: GLOBAL LOGO --}}
            <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-2 group">
                <span class="text-xl md:text-2xl font-extrabold tracking-tight transition-colors duration-300"
                      :class="scrolled || !{{ $isHomePage ? 'true' : 'false' }} ? '{{ $themeText }}' : 'text-white drop-shadow-md'">
                    {{ $settings['site_name'] ?? 'MadeInTravel' }}
                </span>
            </a>

            {{-- 2. MIDDLE: FROSTED PILL NAVIGATION WITH DROPDOWNS --}}
            <div class="hidden md:flex items-center rounded-full p-1 transition-all duration-500"
                 :class="scrolled || !{{ $isHomePage ? 'true' : 'false' }} ? 'bg-gray-100/80 border border-gray-200' : 'bg-white/10 backdrop-blur-md border border-white/20'">
                
                {{-- Home --}}
                <a href="{{ route('home') }}" 
                   class="px-5 py-2 rounded-full text-sm font-bold transition-all"
                   :class="scrolled || !{{ $isHomePage ? 'true' : 'false' }} ? 'text-gray-600 hover:bg-white hover:shadow-sm' : 'text-white hover:bg-white/20'">
                    {{ __('Home') }}
                </a>
                
                {{-- Properties Dropdown --}}
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <a href="{{ route('property.home') }}" 
                       class="px-5 py-2 rounded-full text-sm font-bold transition-all block"
                       :class="scrolled || !{{ $isHomePage ? 'true' : 'false' }} ? 'text-gray-600 hover:bg-white hover:shadow-sm' : 'text-white hover:bg-white/20'">
                        {{ __('Properties') }}
                    </a>
                    <div x-show="open" x-transition.opacity class="absolute left-0 top-full mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-2 overflow-hidden" style="display: none;">
                        <a href="{{ route('property.home') }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-sky-50 hover:text-sky-600">{{ __('Search Homes') }}</a>
                        <a href="{{ route('property.map') }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-sky-50 hover:text-sky-600">{{ __('Map Search') }}</a>
                    </div>
                </div>

                {{-- Events Dropdown --}}
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <a href="{{ route('eventOrganizer.home') }}" 
                       class="px-5 py-2 rounded-full text-sm font-bold transition-all block"
                       :class="scrolled || !{{ $isHomePage ? 'true' : 'false' }} ? 'text-gray-600 hover:bg-white hover:shadow-sm' : 'text-white hover:bg-white/20'">
                        {{ __('Events') }}
                    </a>
                    <div x-show="open" x-transition.opacity class="absolute left-0 top-full mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-2 overflow-hidden" style="display: none;">
                        <a href="{{ route('eventOrganizer.home') }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-sky-50 hover:text-sky-600">{{ __('Discover Events') }}</a>
                        <a href="{{ route('eventOrganizer.packages.index') }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-sky-50 hover:text-sky-600">{{ __('Packages') }}</a>
                        <a href="{{ route('eventOrganizer.vendors.index') }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-sky-50 hover:text-sky-600">{{ __('Vendors') }}</a>
                        <a href="{{ route('eventOrganizer.gallery.index') }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-sky-50 hover:text-sky-600">{{ __('Gallery') }}</a>
                    </div>
                </div>

                {{-- Tour Dropdown --}}
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <a href="{{ route('tour.home') }}"
                    class="px-5 py-2 rounded-full text-sm font-bold transition-all block"
                    :class="scrolled || !{{ $isHomePage ? 'true' : 'false' }} ? 'text-gray-600 hover:bg-white hover:shadow-sm' : 'text-white hover:bg-white/20'">
                        {{ __('Tours') }}
                    </a>
                    <div x-show="open" x-transition.opacity class="absolute left-0 top-full mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-2 overflow-hidden" style="display: none;">
                        <a href="{{ route('tour.home') }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-sky-50 hover:text-sky-600">{{ __('Browse Tours') }}</a>
                        <a href="{{ route('tour.tours.index', ['category' => 'adventure']) }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-sky-50 hover:text-sky-600"> {{ __('Adventure') }}</a>
                        <a href="{{ route('tour.tours.index', ['category' => 'cultural']) }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-sky-50 hover:text-sky-600"> {{ __('Cultural') }}</a>
                        <a href="{{ route('tour.tours.index', ['category' => 'nature']) }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-sky-50 hover:text-sky-600"> {{ __('Nature') }}</a>
                        <a href="{{ route('tour.tours.index', ['category' => 'water_sports']) }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-sky-50 hover:text-sky-600"> {{ __('Water Sports') }}</a>
                    </div>
                </div>

                {{-- Rental Dropdown --}}
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <a href="{{ route('rental.home') }}" 
                       class="px-5 py-2 rounded-full text-sm font-bold transition-all block"
                       :class="scrolled || !{{ $isHomePage ? 'true' : 'false' }} ? 'text-gray-600 hover:bg-white hover:shadow-sm' : 'text-white hover:bg-white/20'">
                        {{ __('Rentals') }}
                    </a>
                    <div x-show="open" x-transition.opacity class="absolute left-0 top-full mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-2 overflow-hidden" style="display: none;">
                        <a href="{{ route('rental.home') }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-sky-50 hover:text-sky-600">{{ __('Browse Vehicles') }}</a>
                        <a href="{{ route('rental.vehicles.index', ['type' => 'car']) }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-sky-50 hover:text-sky-600"> {{ __('Cars') }}</a>
                        <a href="{{ route('rental.vehicles.index', ['type' => 'motorbike']) }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-sky-50 hover:text-sky-600"> {{ __('Motorbikes') }}</a>
                        <a href="{{ route('rental.vehicles.index', ['type' => 'boat']) }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-sky-50 hover:text-sky-600"> {{ __('Boats') }}</a>
                    </div>
                </div>

                {{-- News --}}
                <a href="{{ route('articles.index') }}" 
                   class="px-5 py-2 rounded-full text-sm font-bold transition-all"
                   :class="scrolled || !{{ $isHomePage ? 'true' : 'false' }} ? 'text-gray-600 hover:bg-white hover:shadow-sm' : 'text-white hover:bg-white/20'">
                    {{ __('News') }}
                </a>
            </div>

            {{-- 3. RIGHT: ACTIONS --}}
            <div class="hidden md:flex items-center space-x-4">
                
                {{-- Language Switcher --}}
                <div class="relative" x-data="{ langOpen: false }">
                    <button @click="langOpen = !langOpen" class="flex items-center gap-1 font-bold text-sm transition-colors duration-300"
                            :class="scrolled || !{{ $isHomePage ? 'true' : 'false' }} ? 'text-gray-600 hover:text-gray-900' : 'text-white/90 hover:text-white drop-shadow'">
                        @if(app()->getLocale() == 'id') 🇮🇩 ID @else 🇺🇸 EN @endif
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="langOpen" @click.away="langOpen = false" class="absolute right-0 mt-4 w-32 bg-white rounded-xl shadow-xl border border-gray-100 py-2 overflow-hidden" style="display: none;">
                        <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-sky-50 hover:text-sky-600">🇺🇸 English</a>
                        <a href="{{ route('lang.switch', 'id') }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-sky-50 hover:text-sky-600">🇮🇩 Indonesia</a>
                    </div>
                </div>

                {{-- Wishlist (Always in DOM, hides if empty) --}}
                <a href="{{ '/property/wishlist' }}" class="relative transition-colors"
                   :class="scrolled || !{{ $isHomePage ? 'true' : 'false' }} ? 'text-gray-400 hover:text-red-500' : 'text-white/80 hover:text-white'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <div x-show="$store.wishlist.ids.length > 0" class="absolute -top-1 -right-2 flex items-center justify-center h-4 w-4 rounded-full bg-red-600 text-[9px] font-bold text-white shadow" x-text="$store.wishlist.ids.length" style="display: none;"></div>
                </a>
            </div>

            {{-- Hamburger --}}
            <div class="md:hidden flex items-center">
                <button @click="mobileOpen = true" class="p-2 transition-colors"
                        :class="scrolled || !{{ $isHomePage ? 'true' : 'false' }} ? 'text-gray-600' : 'text-white'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>

        </div>
    </div>

    {{-- MOBILE SLIDE-OUT MENU --}}
    <div x-show="mobileOpen" class="fixed inset-0 z-[100] md:hidden" role="dialog" aria-modal="true" style="display: none;">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="mobileOpen = false" x-show="mobileOpen" x-transition.opacity></div>

        <div class="fixed inset-y-0 right-0 w-full max-w-xs bg-white shadow-xl flex flex-col transition-transform"
             x-show="mobileOpen" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
            
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <span class="font-bold text-lg text-sky-600">{{ __('Menu') }}</span>
                <button @click="mobileOpen = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-2">
                <a href="{{ route('home') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg">🌍 {{ __('Main Page') }}</a>
                <div class="border-t border-gray-100 my-2"></div>
                
                {{-- Property Mobile --}}
                <div class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Property') }}</div>
                <a href="{{ route('property.home') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg"> {{ __('Search Homes') }}</a>
                <a href="{{ route('property.map') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg"> {{ __('Map Search') }}</a>
                
                <div class="border-t border-gray-100 my-2"></div>

                {{-- Events Mobile --}}
                <div class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Event Organizer') }}</div>
                <a href="{{ route('eventOrganizer.home') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg"> {{ __('Discover Events') }}</a>
                <a href="{{ route('eventOrganizer.packages.index') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg"> {{ __('Packages') }}</a>
                <a href="{{ route('eventOrganizer.vendors.index') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg"> {{ __('Vendors') }}</a>
                <a href="{{ route('eventOrganizer.gallery.index') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg"> {{ __('Gallery') }}</a>

                <div class="border-t border-gray-100 my-2"></div>

                {{-- Tour Mobile --}}
                <div class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Tours') }}</div>
                <a href="{{ route('tour.home') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg"> {{ __('Browse Tours') }}</a>
                <a href="{{ route('tour.tours.index', ['category' => 'adventure']) }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg"> {{ __('Adventure') }}</a>
                <a href="{{ route('tour.tours.index', ['category' => 'cultural']) }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg"> {{ __('Cultural') }}</a>
                <a href="{{ route('tour.tours.index', ['category' => 'nature']) }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg"> {{ __('Nature') }}</a>
                <a href="{{ route('tour.tours.index', ['category' => 'water_sports']) }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg"> {{ __('Water Sports') }}</a>

                <div class="border-t border-gray-100 my-2"></div>

                {{-- Rental Mobile --}}
                <div class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Rentals') }}</div>
                <a href="{{ route('rental.home') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg"> {{ __('Browse Vehicles') }}</a>
                <a href="{{ route('rental.vehicles.index', ['type' => 'car']) }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg"> {{ __('Cars') }}</a>
                <a href="{{ route('rental.vehicles.index', ['type' => 'motorbike']) }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg"> {{ __('Motorbikes') }}</a>
                <a href="{{ route('rental.vehicles.index', ['type' => 'boat']) }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg"> {{ __('Boats') }}</a>

                <div class="border-t border-gray-100 my-2"></div>
                

                <div class="relative" x-data="{ langOpen: false }">
                    <button @click="langOpen = !langOpen" class="w-full flex items-center justify-between px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg">
                        <span class="flex items-center gap-2">🌐 {{ __('Language') }} @if(app()->getLocale() == 'id') (ID) @else (EN) @endif</span>
                        <svg class="w-4 h-4 transition-transform" :class="langOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="langOpen" class="pl-4 space-y-1 bg-gray-50 rounded-lg mb-2 p-2" style="display: none;">
                        <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-700 hover:text-sky-600">🇺🇸 English</a>
                        <a href="{{ route('lang.switch', 'id') }}" class="block px-4 py-2 text-sm text-gray-700 hover:text-sky-600">🇮🇩 Indonesia</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>