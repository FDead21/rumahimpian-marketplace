@props(['property'])

<div class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 ease-in-out relative">
    
    <div class="relative h-64 overflow-hidden">
        <a href="{{ route('property.show', ['id' => $property->id, 'slug' => $property->slug]) }}">
            @if($property->media->first())
                <img src="{{ asset('storage/' . $property->media->first()->file_path) }}" 
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
            @else
                <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            @endif
        </a>

        <div class="absolute top-4 left-4">
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm
                {{ $property->listing_type == 'SALE' ? 'bg-indigo-600 text-white' : 'bg-emerald-500 text-white' }}">
                {{ __(ucfirst(strtolower($property->listing_type))) }}
            </span>
        </div>

        <div class="absolute top-4 right-4 z-10">
            <button x-data 
                    @click.prevent="$store.wishlist.toggle({{ $property->id }})"
                    class="bg-white/90 backdrop-blur-sm p-2 rounded-full shadow-sm hover:scale-110 transition active:scale-95 group/heart">
                
                <svg x-show="!$store.wishlist.has({{ $property->id }})" 
                        class="w-5 h-5 text-gray-600 group-hover/heart:text-red-500 transition" 
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>

                <svg x-show="$store.wishlist.has({{ $property->id }})" 
                        style="display: none;"
                        class="w-5 h-5 text-red-500 fill-current" 
                        viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>

        <div x-data="compareLogic({{ $property->id }})" class="absolute top-16 right-4 z-10 mt-1">
            <button 
                @click.prevent="toggle()" 
                class="bg-white/90 backdrop-blur-sm p-2 rounded-full shadow-sm hover:scale-110 transition active:scale-95 group/compare border border-transparent"
                :class="selected ? 'text-indigo-600 ring-2 ring-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600'"
                title="{{ __('Compare Property') }}"
            >
                <svg x-show="!selected" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>

                <svg x-show="selected" style="display: none;" class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </button>
        </div>

        <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-lg shadow-sm border border-white/50">
            <span class="text-indigo-700 font-extrabold text-lg">
                @currency($property->price)
            </span>
        </div>
    </div>

    <div class="p-5">
        <div class="flex items-center text-xs text-gray-500 mb-2">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            {{ $property->city }}, {{ $property->district }}
        </div>

        <h3 class="font-bold text-gray-900 text-lg leading-tight mb-4 group-hover:text-indigo-600 transition-colors">
            <a href="{{ route('property.show', ['id' => $property->id, 'slug' => $property->slug]) }}">
                {{ Str::limit($property->title, 45) }}
            </a>
        </h3>

        <div class="flex items-center justify-between border-t border-gray-100 pt-4 text-sm text-gray-600">
            <div class="flex items-center gap-1" title="{{ __('Bedrooms') }}">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="font-semibold">{{ $property->bedrooms }}</span> <span class="text-xs">{{ __('Bed') }}</span>
            </div>
            <div class="flex items-center gap-1" title="{{ __('Bathrooms') }}">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m8-2a2 2 0 00-2-2H9a2 2 0 00-2 2m5-4v.01M17 16v.01"></path></svg>
                <span class="font-semibold">{{ $property->bathrooms }}</span> <span class="text-xs">{{ __('Bath') }}</span>
            </div>
            <div class="flex items-center gap-1" title="{{ __('Building Area') }}">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m8-2a2 2 0 00-2-2H9a2 2 0 00-2 2m5-4v.01M17 16v.01"></path></svg>
                <span class="font-semibold">{{ $property->building_area }}</span> <span class="text-xs">m²</span>
            </div>
        </div>
    </div>
</div>