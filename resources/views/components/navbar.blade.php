@php
    // 1. Detect the current module
    $isEo = request()->is('eventOrganizer*');
    $isProperty = request()->is('property*') || request()->is('map*') || request()->is('agency*') || request()->is('agent*');
    $isPortal = request()->routeIs('home'); 

    // 2. Set Theme Colors dynamically
    $themeText = $isEo ? 'text-rose-600' : 'text-sky-600';
    $themeBg = $isEo ? 'bg-rose-600 hover:bg-rose-700' : 'bg-sky-600 hover:bg-sky-700';
    $themeBorder = $isEo ? 'hover:border-rose-600' : 'hover:border-sky-600';
@endphp

<nav class="bg-white sticky top-0 z-50 transition-all duration-300 shadow-sm font-sans" x-data="{ mobileOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">

    {{-- ============================================================== --}}
    {{--  ROW 1: GLOBAL TOP BAR (Always Visible)                        --}}
    {{-- ============================================================== --}}
    <div class="border-b border-gray-100 relative z-50 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-20 gap-4">

                {{-- 1. LEFT: GLOBAL LOGO --}}
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-2 group">
                    <span class="text-xl md:text-2xl font-extrabold tracking-tight {{ $themeText }}">
                        {{ $settings['site_name'] ?? 'MadeInTravel' }}
                    </span>
                </a>

                {{-- 2. MIDDLE: CONTEXTUAL SEARCH BAR (Hidden on Portal & EO) --}}
                <div class="hidden md:block flex-1 max-w-2xl px-8" x-cloak>
                    @if($isProperty)
                        <form action="{{ route('property.home') }}" method="GET">
                            <div class="relative group">
                                <input type="text" name="search" 
                                       class="w-full bg-gray-100 border-transparent focus:bg-white focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 rounded-full py-2.5 pl-11 pr-14 outline-none text-sm transition-all"
                                       placeholder="{{ __('Search by City, District, or Property Name...') }}">
                                <span class="absolute left-4 top-2.5 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <button type="submit" class="absolute right-1.5 top-1.5 bg-sky-600 text-white p-1.5 rounded-full hover:bg-sky-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>

                {{-- 3. RIGHT: GLOBAL ACTIONS --}}
                <div class="hidden md:flex items-center space-x-6">
                    
                    {{-- Language Switcher (Always visible) --}}
                    <div class="relative" x-data="{ langOpen: false }">
                        <button @click="langOpen = !langOpen" class="flex items-center gap-1 text-gray-500 hover:text-gray-900 font-bold text-sm">
                            @if(app()->getLocale() == 'id') 🇮🇩 ID @else 🇺🇸 EN @endif
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="langOpen" @click.away="langOpen = false" class="absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-xl border border-gray-100 py-1" style="display: none;">
                            <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">🇺🇸 English</a>
                            <a href="{{ route('lang.switch', 'id') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">🇮🇩 Indonesia</a>
                        </div>
                    </div>

                    {{-- Wishlist (Only visible in Property) --}}
                    @if($isProperty)
                        <a href="{{ '/property/wishlist' }}" class="relative text-gray-400 hover:text-red-500 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            <div x-show="$store.wishlist.ids.length > 0" class="absolute -top-1 -right-2 flex items-center justify-center h-4 w-4 rounded-full bg-red-600 text-[9px] font-bold text-white" x-text="$store.wishlist.ids.length" style="display: none;"></div>
                        </a>
                    @endif
                </div>

                {{-- Hamburger --}}
                <div class="md:hidden flex items-center">
                    <button @click="mobileOpen = true" class="text-gray-500 hover:text-gray-900 p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{--  ROW 2: CONTEXTUAL SUB-NAV (Hidden on Portal)                  --}}
    {{-- ============================================================== --}}
    @if(!$isPortal)
    <div class="hidden md:block bg-white/90 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center h-12 gap-8 text-sm font-bold text-gray-500">
                
                @if($isProperty)
                    {{-- Property Links --}}
                    <a href="{{ route('property.home') }}" class="h-full flex items-center border-b-2 border-transparent {{ $themeBorder }} hover:{{ $themeText }}">{{ __('Properties') }}</a>
                    {{-- Locations Dropdown (Desktop) --}}
                    <div class="relative h-full flex items-center" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="text-gray-500 hover:{{ $themeText }} text-sm font-bold h-full flex items-center border-b-2 border-transparent transition">{{ __('Location') }}</button>
                        <div x-show="open" x-transition class="absolute left-0 top-full w-[600px] bg-white border border-gray-100 shadow-xl rounded-b-xl p-6 z-50 grid grid-cols-3 gap-4" style="display: none;">
                            @if(isset($cities) && $cities->count() > 0)
                                @foreach($cities as $city)
                                    <a href="{{ route('property.home', ['search' => $city]) }}" class="text-sm text-gray-600 hover:{{ $themeText }} block transition">📍 {{ $city }}</a>
                                @endforeach
                            @else
                                <p class="text-gray-400 italic text-sm">{{ __('No locations found.') }}</p>
                            @endif
                        </div>
                    </div>

                     {{-- Agencies Dropdown (Desktop) --}}
                     <div class="relative h-full flex items-center" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="text-gray-500 hover:{{ $themeText }} text-sm font-bold h-full flex items-center border-b-2 border-transparent transition">{{ __('Find Agencies') }}</button>
                        <div x-show="open" x-transition class="absolute left-0 top-full w-[250px] bg-white border border-gray-100 shadow-xl rounded-b-xl p-4 z-50 space-y-2" style="display: none;">
                            @if(isset($agencies) && $agencies->count() > 0)
                                @foreach($agencies as $agency)
                                    <a href="{{ route('property.agency.show', $agency->slug) }}" class="block text-sm text-gray-600 hover:{{ $themeText }} transition">{{ $agency->name }}</a>
                                @endforeach
                            @else
                                <p class="text-gray-400 italic text-sm">{{ __('No agencies found.') }}</p>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('property.map') }}" class="h-full flex items-center border-b-2 border-transparent {{ $themeBorder }} hover:{{ $themeText }}">{{ __('Map Search') }}</a>
                    <a href="{{ route('articles.index') }}" class="h-full flex items-center border-b-2 border-transparent {{ $themeBorder }} hover:{{ $themeText }}">{{ __('News') }}</a>
                @endif

                @if($isEo)
                    {{-- EO Links --}}
                    <a href="{{ route('eventOrganizer.home') }}" class="h-full flex items-center border-b-2 border-transparent {{ $themeBorder }} hover:{{ $themeText }}">{{ __('Discover Events') }}</a>
                    <a href="{{ route('eventOrganizer.packages.index') }}" class="h-full flex items-center border-b-2 border-transparent {{ $themeBorder }} hover:{{ $themeText }}">{{ __('Packages') }}</a>
                    <a href="{{ route('eventOrganizer.vendors.index') }}" class="h-full flex items-center border-b-2 border-transparent {{ $themeBorder }} hover:{{ $themeText }}">{{ __('Vendors') }}</a>
                    <a href="{{ route('eventOrganizer.gallery.index') }}" class="h-full flex items-center border-b-2 border-transparent {{ $themeBorder }} hover:{{ $themeText }}">{{ __('Gallery') }}</a>
                @endif

            </div>
        </div>
    </div>
    @endif

    {{-- ============================================================== --}}
    {{--  MOBILE SLIDE-OUT MENU                                         --}}
    {{-- ============================================================== --}}
    <div x-show="mobileOpen" 
         class="fixed inset-0 z-[100] md:hidden" 
         role="dialog" aria-modal="true"
         style="display: none;">
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" 
             @click="mobileOpen = false"
             x-show="mobileOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        {{-- Drawer Panel --}}
        <div class="fixed inset-y-0 right-0 w-full max-w-xs bg-white shadow-xl flex flex-col transition-transform"
             x-show="mobileOpen"
             x-transition:enter="transform transition ease-in-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
            
            {{-- Drawer Header --}}
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <span class="font-bold text-lg {{ $themeText }}">{{ __('Menu') }}</span>
                <button @click="mobileOpen = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Drawer Links --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-2">
                
                {{-- Global Portal Link --}}
                <a href="{{ route('home') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg">
                    🌍 {{ __('Main Page') }}
                </a>
                
                <div class="border-t border-gray-100 my-2"></div>

                {{-- Contextual Links: Property --}}
                @if($isProperty)
                    <div class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Property') }}</div>
                    <a href="{{ route('property.home') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg">🏠 {{ __('Home') }}</a>
                    <a href="{{ route('articles.index') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg">📰 {{ __('News') }}</a>
                    <a href="{{ route('property.map') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg">🗺️ {{ __('Map Search') }}</a>
                    <a href="{{ route('property.wishlist') }}" class="flex justify-between items-center px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg">
                        <span>❤️ {{ __('My Saved Homes') }}</span>
                        <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded-full" x-show="$store.wishlist.ids.length > 0" x-text="$store.wishlist.ids.length" style="display: none;"></span>
                    </a>
                @endif

                {{-- Contextual Links: EO --}}
                @if($isEo)
                    <div class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Event Organizer') }}</div>
                    <a href="{{ route('eventOrganizer.home') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg">🎊 {{ __('Discover Events') }}</a>
                    <a href="{{ route('eventOrganizer.packages.index') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg">🎁 {{ __('Packages') }}</a>
                    <a href="{{ route('eventOrganizer.vendors.index') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg">🏪 {{ __('Vendors') }}</a>
                    <a href="{{ route('eventOrganizer.gallery.index') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg">🖼️ {{ __('Gallery') }}</a>
                    <a href="{{ route('eventOrganizer.booking.create') }}" class="block mt-4 w-full text-center bg-rose-600 text-white font-bold py-3 rounded-lg shadow-sm">📅 {{ __('Book Now') }}</a>
                @endif

                <div class="border-t border-gray-100 my-2"></div>

                {{-- Language Toggle --}}
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