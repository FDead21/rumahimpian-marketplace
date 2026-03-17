<nav class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50"
     x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            {{-- Logo --}}
            <a href="{{ route('eo.home') }}" class="flex items-center space-x-2">
                @if(!empty($eoSettings['eo_site_logo']))
                    <img src="{{ asset('storage/' . $eoSettings['eo_site_logo']) }}" class="h-8 w-auto">
                @else
                    <span class="text-xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-pink-500">
                        {{ $eoSettings['eo_site_name'] ?? 'RumahImpian EO' }}
                    </span>
                @endif
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('eo.home') }}"
                   class="text-sm font-semibold {{ request()->routeIs('eo.home') ? 'text-rose-600' : 'text-gray-600 hover:text-rose-600' }} transition">
                    Home
                </a>
                <a href="{{ route('eo.packages') }}"
                   class="text-sm font-semibold {{ request()->routeIs('eo.packages*') ? 'text-rose-600' : 'text-gray-600 hover:text-rose-600' }} transition">
                    Packages
                </a>
                <a href="{{ route('eo.gallery') }}"
                   class="text-sm font-semibold {{ request()->routeIs('eo.gallery*') ? 'text-rose-600' : 'text-gray-600 hover:text-rose-600' }} transition">
                    Gallery
                </a>
                <a href="{{ route('eo.vendors') }}"
                   class="text-sm font-semibold {{ request()->routeIs('eo.vendors') ? 'text-rose-600' : 'text-gray-600 hover:text-rose-600' }} transition">
                    Vendors
                </a>

                {{-- CTA Button --}}
                <a href="{{ route('eo.booking.booking-form') }}"
                   class="bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold px-5 py-2 rounded-lg shadow transition transform hover:-translate-y-0.5">
                    📅 Book Now
                </a>
            </div>

            {{-- Mobile menu button --}}
            <button @click="open = !open" class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="open" style="display:none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="open" x-transition class="md:hidden bg-white border-t border-gray-100 px-4 py-4 space-y-3">
        <a href="{{ route('eo.home') }}" class="block text-sm font-semibold text-gray-700 hover:text-rose-600">Home</a>
        <a href="{{ route('eo.packages') }}" class="block text-sm font-semibold text-gray-700 hover:text-rose-600">Packages</a>
        <a href="{{ route('eo.gallery') }}" class="block text-sm font-semibold text-gray-700 hover:text-rose-600">Gallery</a>
        <a href="{{ route('eo.vendors') }}" class="block text-sm font-semibold text-gray-700 hover:text-rose-600">Vendors</a>
        <a href="{{ route('eo.booking.booking-form') }}" class="block w-full text-center bg-rose-600 text-white font-bold py-2 rounded-lg">📅 Book Now</a>
        <a href="{{ route('home') }}" class="block text-xs text-center text-gray-400 border border-gray-200 py-2 rounded-lg">🏠 Property Site</a>
    </div>
</nav>
