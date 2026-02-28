<nav class="bg-white border-b border-gray-200 sticky top-0 z-50 transition-all duration-300 shadow-md font-sans" 
    x-data="{ 
    locationOpen: false, 
    agencyOpen: false,
    mobileOpen: false,
    mobileLocationOpen: false,
    mobileAgencyOpen: false,
    scrolled: false, 
    isHome: {{ request()->routeIs('home') ? 'true' : 'false' }}
    }" 
    @scroll.window="scrolled = (window.pageYOffset > 400)">

    <div class="border-b border-gray-100 bg-white relative z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-20 gap-4">

                {{-- 1. LEFT: LOGO --}}
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-2 group">
                    @if(!empty($settings['site_logo']))
                        <img src="{{ asset('storage/' . $settings['site_logo']) }}" class="h-8 md:h-10 w-auto object-contain">
                    @else
                        <span class="text-xl md:text-2xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-indigo-700 to-indigo-500 tracking-tight">
                            {{ $settings['site_name'] ?? 'RumahImpian' }}
                        </span>
                    @endif
                </a>

                {{-- 2. MIDDLE: SEARCH BAR (Desktop Only) --}}
                <div class="hidden md:block flex-1 max-w-2xl transition-all duration-300 transform origin-left"
                     :class="(isHome && !scrolled) ? 'opacity-0 scale-95 pointer-events-none' : 'opacity-100 scale-100'"
                     x-cloak>
                    <form action="{{ route('home') }}" method="GET">
                        <div class="relative group">
                            <input type="text" name="search" 
                                   class="w-full bg-gray-100 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 rounded-full py-2.5 pl-11 pr-14 transition-all outline-none text-gray-800 font-medium placeholder-gray-400"
                                   placeholder="{{ __('Search by City, District, or Property Name...') }}">
                            <span class="absolute left-4 top-3 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <button type="submit" class="absolute right-1.5 top-1.5 bg-indigo-600 text-white p-1.5 rounded-full hover:bg-indigo-700 transition shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- 3. RIGHT: ACTIONS (Desktop Only) --}}
                <div class="hidden md:flex items-center space-x-4">
                    <div class="relative" x-data="{ langOpen: false }">
                        <button @click="langOpen = !langOpen" class="flex items-center gap-1 text-gray-500 hover:text-indigo-600 font-bold text-sm">
                            @if(app()->getLocale() == 'id')
                                🇮🇩 ID
                            @else
                                🇺🇸 EN
                            @endif
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="langOpen" @click.away="langOpen = false" 
                             class="absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-xl border border-gray-100 z-50 py-1"
                             style="display: none;">
                            <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">🇺🇸 English</a>
                            <a href="{{ route('lang.switch', 'id') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">🇮🇩 Indonesia</a>
                        </div>
                    </div>

                    {{-- Wishlist --}}
                    <a href="{{ route('wishlist') }}" class="relative p-2 text-gray-400 hover:text-red-500 transition-colors group" title="{{ __('My Saved Homes') }}">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        <div x-show="$store.wishlist.ids.length > 0" x-transition.scale style="display: none;" class="absolute top-0 right-0 transform translate-x-1 -translate-y-1">
                            <span class="flex items-center justify-center h-5 w-5 rounded-full bg-red-600 text-[10px] font-bold text-white shadow-sm ring-2 ring-white" x-text="$store.wishlist.ids.length"></span>
                        </div>
                    </a>
                </div>

                {{-- 4. HAMBURGER BUTTON (Mobile Only) --}}
                <div class="md:hidden flex items-center">
                    <button @click="mobileOpen = true" class="text-gray-500 hover:text-indigo-600 p-2 focus:outline-none">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{--  DESKTOP ROW 2: MENU (Hidden on Mobile)                        --}}
    {{-- ============================================================== --}}
    <div class="hidden md:block bg-white/80 backdrop-blur-md border-b border-gray-100 relative z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center h-12 gap-8 relative">
                <a href="{{ route('home') }}" class="text-sm font-bold text-gray-600 hover:text-indigo-600 h-full flex items-center border-b-2 border-transparent hover:border-indigo-600">{{ __('Home') }}</a>
                <a href="{{ route('articles.index') }}" class="text-sm font-bold text-gray-600 hover:text-indigo-600 h-full flex items-center border-b-2 border-transparent hover:border-indigo-600">{{ __('News') }}</a>
                {{-- Locations Dropdown (Desktop) --}}
                <div class="relative h-full flex items-center" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="text-gray-500 hover:text-indigo-600 text-sm font-medium h-full flex items-center border-b-2 border-transparent">{{ __('Location') }}</button>
                    <div x-show="open" class="absolute left-0 top-full w-[600px] bg-white border border-gray-100 shadow-xl rounded-b-xl p-6 z-50 grid grid-cols-3 gap-4" style="display: none;">
                        @if(isset($cities) && $cities->count() > 0)
                            @foreach($cities as $city)
                                <a href="{{ route('home', ['search' => $city]) }}" class="text-sm text-gray-600 hover:text-indigo-600 block">📍 {{ $city }}</a>
                            @endforeach
                        @else
                            <p class="text-gray-400 italic text-sm">{{ __('No locations found.') }}</p>
                        @endif
                    </div>
                </div>

                 {{-- Agencies Dropdown (Desktop) --}}
                 <div class="relative h-full flex items-center" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="text-gray-500 hover:text-indigo-600 text-sm font-medium h-full flex items-center border-b-2 border-transparent">{{ __('Find Agencies') }}</button>
                    <div x-show="open" class="absolute left-0 top-full w-[250px] bg-white border border-gray-100 shadow-xl rounded-b-xl p-4 z-50 space-y-2" style="display: none;">
                        @if(isset($agencies) && $agencies->count() > 0)
                            @foreach($agencies as $agency)
                                <a href="{{ route('agency.show', $agency->slug) }}" class="block text-sm text-gray-600 hover:text-indigo-600">{{ $agency->name }}</a>
                            @endforeach
                        @else
                            <p class="text-gray-400 italic text-sm">{{ __('No agencies found.') }}</p>
                        @endif
                    </div>
                </div>

                <a href="{{ route('map.search') }}" class="text-sm font-bold text-gray-600 hover:text-indigo-600 h-full flex items-center">{{ __('Map Search') }}</a>
            </div>
        </div>
    </div>

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
            
            {{-- Drawer Header (User Info) --}}
            <div class="p-6 bg-indigo-50 border-b border-indigo-100">
                <div class="flex items-center justify-between mb-6">
                    <span class="font-bold text-lg text-indigo-900">{{ __('Menu') }}</span>
                    <button @click="mobileOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

            </div>

            {{-- Drawer Links --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-1">
                
                {{-- Home --}}
                <a href="{{ route('home') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg">
                    🏠 {{ __('Home') }}
                </a>

                <a href="{{ route('articles.index') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg">
                    📰 {{ __('News') }}
                </a>

                <div class="relative" x-data="{ langOpen: false }">
                    <button @click="langOpen = !langOpen" class="w-full flex items-center justify-between px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg">
                        <span class="flex items-center gap-2">
                            🌐 {{ __('Language') }}
                            @if(app()->getLocale() == 'id') (ID) @else (EN) @endif
                        </span>
                        <svg class="w-4 h-4 transition-transform" :class="langOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="langOpen" class="pl-4 space-y-1 bg-gray-50 rounded-lg mb-2 p-2">
                        <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-700 hover:text-indigo-600">🇺🇸 English</a>
                        <a href="{{ route('lang.switch', 'id') }}" class="block px-4 py-2 text-sm text-gray-700 hover:text-indigo-600">🇮🇩 Indonesia</a>
                    </div>
                </div>

                {{-- Wishlist --}}
                <a href="{{ route('wishlist') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg flex justify-between items-center">
                    <span>❤️ {{ __('My Saved Homes') }}</span>
                    <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded-full" x-text="$store.wishlist.ids.length"></span>
                </a>

                <div class="border-t border-gray-100 my-2"></div>

                {{-- Locations Toggle --}}
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex justify-between items-center px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg">
                        <span>📍 {{ __('Browse by Location') }}</span>
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" class="pl-4 space-y-1 bg-gray-50 rounded-lg mb-2 p-2">
                        @if(isset($cities) && $cities->count() > 0)
                            @foreach($cities as $city)
                                <a href="{{ route('home', ['search' => $city]) }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-indigo-600">{{ $city }}</a>
                            @endforeach
                        @else
                            <span class="block px-4 py-2 text-sm text-gray-400">{{ __('No locations found.') }}</span>
                        @endif
                    </div>
                </div>

                {{-- Agencies Toggle --}}
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex justify-between items-center px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg">
                        <span>🏢 {{ __('Find Agencies') }}</span>
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" class="pl-4 space-y-1 bg-gray-50 rounded-lg mb-2 p-2">
                         @if(isset($agencies) && $agencies->count() > 0)
                            @foreach($agencies as $agency)
                                <a href="{{ route('agency.show', $agency->slug) }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-indigo-600">{{ $agency->name }}</a>
                            @endforeach
                        @else
                            <span class="block px-4 py-2 text-sm text-gray-400">{{ __('No agencies found.') }}</span>
                        @endif
                    </div>
                </div>

                <a href="{{ route('map.search') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-gray-50 rounded-lg">
                    🗺️ {{ __('Map Search') }}
                </a>

                @auth
                    <div class="border-t border-gray-100 my-2"></div>
                    <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-3 text-red-600 font-bold hover:bg-red-50 rounded-lg">
                            🚪 {{ __('Logout') }}
                        </button>
                    </form>
                @endauth

            </div>
        </div>
    </div>
</nav>

<script src="//unpkg.com/alpinejs" defer></script>