<div class="space-y-5">
    {{-- Filters --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="relative">
            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
            </div>
            <input wire:model.live.debounce.300ms="venueSearch"
                   type="text"
                   placeholder="Search title, city, district..."
                   class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition">
        </div>

        <select wire:model.live="venueCity"
                class="w-full px-3 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition">
            <option value="">🏙 All Cities</option>
            @foreach($cities as $city)
                <option value="{{ $city }}">{{ $city }}</option>
            @endforeach
        </select>

        <select wire:model.live="venueType"
                class="w-full px-3 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition">
            <option value="">🏠 All Types</option>
            @foreach($types as $type)
                <option value="{{ $type }}">{{ $type }}</option>
            @endforeach
        </select>
    </div>

    {{-- Results count --}}
    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 px-1">
        <span>{{ $properties->total() }} properties found</span>
        @if($selectedPropertyId)
            <span class="text-primary-500 font-semibold flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15 3.293 9.879a1 1 0 011.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                1 venue selected
            </span>
        @endif
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-h-[55vh] overflow-y-auto pr-1 pb-1">
        @forelse($properties as $property)
            <div wire:click="selectProperty({{ $property->id }})"
                 wire:key="property-{{ $property->id }}"
                 class="group flex flex-col cursor-pointer rounded-2xl overflow-hidden transition-all duration-200 bg-white dark:bg-gray-800
                        {{ $selectedPropertyId == $property->id
                            ? 'ring-4 ring-primary-500 shadow-xl scale-[1.02] z-10'
                            : 'border border-gray-200 dark:border-gray-700 hover:border-primary-400 hover:shadow-lg' }}">

                {{-- Image --}}
                <div class="relative h-44 shrink-0 overflow-hidden bg-gray-100 dark:bg-gray-900">
                    @if($property->media->first())
                        <img src="{{ asset('storage/' . $property->media->first()->file_path) }}"
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                             alt="{{ $property->title }}">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-300 dark:text-gray-600">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="text-xs mt-1">No photo</span>
                        </div>
                    @endif

                    {{-- Type badge --}}
                    <div class="absolute top-3 left-3">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500 text-white shadow">
                            RENT
                        </span>
                    </div>

                    {{-- Selected checkmark --}}
                    @if($selectedPropertyId == $property->id)
                        <div class="absolute top-3 right-3 bg-primary-500 text-white rounded-full p-1 shadow-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="p-4 flex flex-col gap-2.5">
                    <div class="flex justify-between items-start gap-2">
                        <div class="min-w-0">
                            <p class="text-lg font-bold text-gray-900 dark:text-white leading-none mb-1.5">
                                Rp {{ number_format($property->price, 0, ',', '.') }}
                            </p>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300 truncate group-hover:text-primary-500 transition-colors">
                                {{ $property->title }}
                            </p>
                        </div>
                        
                        {{-- Selected Badge --}}
                        @if($selectedPropertyId == $property->id)
                            <div class="shrink-0 bg-primary-500 text-white rounded-full p-1.5 shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <p class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="truncate">{{ $property->city }}{{ $property->district ? ', ' . $property->district : '' }}</span>
                    </p>

                    <div class="flex items-center gap-4 pt-2.5 text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg> 
                            {{ $property->bedrooms }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> 
                            {{ $property->bathrooms }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg> 
                            {{ $property->building_area }}m²
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-600">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <p class="font-medium">No properties found</p>
                <p class="text-sm mt-1">Try a different search or clear filters</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($properties->hasPages())
        <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
            {{ $properties->links() }}
        </div>
    @endif
</div>