<nav class="bg-white/90 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50 transition-all duration-300" x-data="{ locationOpen: false, agencyOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">R</div>
                    <span class="text-2xl font-bold text-indigo-600 tracking-tight">RumahImpian</span>
                </a>

                <div class="hidden sm:ml-10 sm:flex sm:space-x-8 h-full items-center">
                    
                    <a href="{{ route('home') }}" class="border-transparent text-gray-500 hover:text-indigo-600 hover:border-indigo-600 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium h-full transition">
                        Home
                    </a>

                    <div class="relative h-full flex items-center" @mouseenter="locationOpen = true" @mouseleave="locationOpen = false">
                        <button class="text-gray-500 hover:text-indigo-600 inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium focus:outline-none h-full transition group">
                            <span>Browse by Location</span>
                            <svg class="ml-2 h-4 w-4 text-gray-400 group-hover:text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="locationOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute left-0 top-16 w-[600px] bg-white border border-gray-100 shadow-xl rounded-b-xl p-6 z-50 mt-px">
                            
                            <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider mb-3 border-b pb-2">Available Locations</h3>
                            <ul class="grid grid-cols-3 gap-4">
                                @foreach($cities as $city)
                                    <li>
                                        <a href="{{ route('home', ['city' => $city]) }}" class="text-sm text-gray-600 hover:text-indigo-600 hover:underline block truncate">
                                            {{ $city }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                             @if($cities->isEmpty())
                                <p class="text-sm text-gray-400 py-4">No locations available yet.</p>
                            @endif
                            <div class="mt-6 bg-gray-50 -m-6 p-4 rounded-b-xl flex justify-between items-center">
                                <span class="text-xs text-gray-500">Auto-generated based on active listings</span>
                                <a href="{{ route('home') }}" class="text-xs font-bold text-indigo-600 hover:underline">Clear Filter →</a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('map.search') }}" class="text-gray-500 hover:text-indigo-600 font-medium">Map Search 🗺️</a>

                    <div class="relative h-full flex items-center" @mouseenter="agencyOpen = true" @mouseleave="agencyOpen = false">
                        <button class="text-gray-500 hover:text-indigo-600 inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium focus:outline-none h-full transition group">
                            <span>Find Agencies</span>
                            <svg class="ml-2 h-4 w-4 text-gray-400 group-hover:text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="agencyOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute left-0 top-16 w-[250px] bg-white border border-gray-100 shadow-xl rounded-b-xl p-4 z-50 mt-px">
                            
                            <h3 class="font-bold text-gray-900 text-xs uppercase mb-3 border-b pb-2">Top Offices</h3>
                            <ul class="space-y-3">
                                @foreach($agencies as $agency)
                                    <li class="flex items-center gap-3">
                                        @if($agency->logo)
                                            <img src="{{ asset('storage/' . $agency->logo) }}" class="w-8 h-8 object-contain">
                                        @else
                                            <div class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center text-xs font-bold">
                                                {{ substr($agency->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <a href="#" class="text-sm text-gray-600 hover:text-indigo-600 hover:underline">
                                            {{ $agency->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                </div>
            </div>

            <div class="flex items-center gap-4">
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 text-gray-700 hover:text-indigo-600 font-medium focus:outline-none">
                            @if(Auth::user()->avatar_url)
                                <img src="{{ asset('storage/' . Auth::user()->avatar_url) }}" 
                                    class="w-8 h-8 rounded-full object-cover border border-indigo-200"
                                    alt="Avatar">
                            @else
                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold border border-indigo-200">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                            <span class="hidden md:block">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="open" @click.outside="open = false" class="absolute right-0 top-12 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                            <a href="/portal" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">⚙️ Admin Panel</a>
                            <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Log Out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="/portal/login" class="bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-4 py-2 rounded-lg text-sm font-bold transition">
                        Agent Login
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<script src="//unpkg.com/alpinejs" defer></script>