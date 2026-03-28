<x-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-slate-950 text-white relative overflow-hidden px-4 py-12">
        
        {{-- Ambient Background Orbs --}}
        <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-sky-600 rounded-full mix-blend-screen filter blur-[100px] opacity-30 animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-rose-600 rounded-full mix-blend-screen filter blur-[100px] opacity-30 animate-pulse delay-1000"></div>

        {{-- Gateway Header --}}
        <div class="relative z-10 text-center mb-12 max-w-3xl px-4 mt-8 md:mt-0">
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-6 drop-shadow-lg">
                {{ __('Welcome to') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-rose-400">{{ $settings['site_name'] ?? 'MadeInTravel' }}</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-300 font-light leading-relaxed">
                {{ __('Your gateway to perfect living and unforgettable celebrations.') }} 
                <br class="hidden md:block" />{{ __('Select your destination to begin.') }}
            </p>
        </div>

        {{-- Interactive Split Cards Container --}}
        <div class="relative z-10 w-full max-w-7xl h-[500px] md:h-[600px] flex flex-col md:flex-row gap-4 md:gap-6 px-4">
            
            {{-- 1. Property Marketplace Card --}}
            <a href="{{ route('property.home') }}" 
               class="group relative flex-1 md:hover:flex-[1.4] transition-all duration-700 ease-in-out rounded-[2rem] overflow-hidden border border-white/10 shadow-2xl flex flex-col justify-end outline-none focus:ring-4 focus:ring-sky-500/50">
                
                {{-- Background Image --}}
                <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=80" 
                     alt="Property Marketplace" 
                     class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                
                {{-- Overlays --}}
                <div class="absolute inset-0 bg-slate-900/40 group-hover:bg-slate-900/20 transition-colors duration-700"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/60 to-transparent opacity-80"></div>
                
                {{-- Content Panel --}}
                <div class="relative z-10 p-6 md:p-10 transform transition-transform duration-500">
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 transition-all duration-500 group-hover:bg-white/15">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-sky-500/20 text-sky-400 border border-sky-400/30 text-2xl">
                                🏠
                            </span>
                            {{-- Sliding Arrow indicator --}}
                            <div class="opacity-0 -translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-500 text-sky-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </div>
                        </div>
                        <h2 class="text-3xl font-bold text-white mb-2">{{ __('Property Marketplace') }}</h2>
                        <p class="text-gray-300 text-sm md:text-base line-clamp-2 md:line-clamp-none">
                            {{ __('Discover your next home. Buy, sell, or rent houses and apartments across Indonesia with our trusted agents.') }}
                        </p>
                    </div>
                </div>
            </a>

            {{-- 2. Event Organizer Card --}}
            <a href="{{ route('eventOrganizer.home') }}" 
               class="group relative flex-1 md:hover:flex-[1.4] transition-all duration-700 ease-in-out rounded-[2rem] overflow-hidden border border-white/10 shadow-2xl flex flex-col justify-end outline-none focus:ring-4 focus:ring-rose-500/50">
                
                {{-- Background Image --}}
                <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1200&q=80" 
                     alt="Event Organizer" 
                     class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                
                {{-- Overlays --}}
                <div class="absolute inset-0 bg-slate-900/40 group-hover:bg-slate-900/20 transition-colors duration-700"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/60 to-transparent opacity-80"></div>
                
                {{-- Content Panel --}}
                <div class="relative z-10 p-6 md:p-10 transform transition-transform duration-500">
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 transition-all duration-500 group-hover:bg-white/15">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-400/30 text-2xl">
                                🎊
                            </span>
                            {{-- Sliding Arrow indicator --}}
                            <div class="opacity-0 -translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-500 text-rose-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </div>
                        </div>
                        <h2 class="text-3xl font-bold text-white mb-2">{{ __('Event Organizer') }}</h2>
                        <p class="text-gray-300 text-sm md:text-base line-clamp-2 md:line-clamp-none">
                            {{ __('Bring your vision to life. Book premium venues, top-tier catering, and full-service packages for any occasion.') }}
                        </p>
                    </div>
                </div>
            </a>

        </div>
    </div>
</x-layout>