@php
    $thumb = $tour->thumbnail
        ? asset('storage/' . $tour->thumbnail) 
        : ($tour->media->first() ? asset('storage/' . $tour->media->first()->file_path) : null);

    $categoryEmoji = match($tour->category) {
        'ADVENTURE'    => '',
        'CULTURAL'     => '',
        'NATURE'       => '',
        'WATER_SPORTS' => '',
        default        => '',
    };
@endphp

<div class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative flex flex-col">

    @if($tour->is_featured)
        <div class="absolute top-4 left-4 z-10 bg-emerald-600 text-white text-xs font-bold px-3 py-1 rounded-full">{{ __('Featured') }}</div>
    @endif

    {{-- Image --}}
    <div class="relative h-52 overflow-hidden">
        @if($thumb)
            <img src="{{ $thumb }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
        @else
            <div class="w-full h-full bg-gradient-to-br from-emerald-100 to-teal-200 flex items-center justify-center text-6xl">{{ $categoryEmoji }}</div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>

        {{-- Price --}}
        <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-lg">
            <span class="text-emerald-700 font-extrabold text-xl">Rp {{ number_format($tour->price_per_person, 0, ',', '.') }}</span>
            <span class="text-gray-500 text-xs font-medium">/{{ __('person') }}</span>
        </div>

        {{-- Category badge --}}
        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-2 py-1 rounded-lg text-xs font-bold text-gray-700">
            {{ $categoryEmoji }} {{ __($tour->category_label) }}
        </div>
    </div>

    {{-- Content --}}
    <div class="p-6 flex-1 flex flex-col">
        <h3 class="font-bold text-xl text-gray-900 mb-1 group-hover:text-emerald-600 transition">{{ $tour->name }}</h3>

        <div class="flex flex-wrap gap-3 text-sm text-gray-500 mb-3">
            <span>
                @if($tour->duration_label)
                    {{ __($tour->duration_label) }}
                @else
                    {{ $tour->duration_days }} {{ __('Day') }}
                @endif
            </span>
            @if($tour->min_participants)
                <span>{{ __('Min') }} {{ $tour->min_participants }} {{ __('pax') }}</span>
            @endif
            @if($tour->meeting_point)
                <span>📍 {{ Str::limit($tour->meeting_point, 30) }}</span>
            @endif
        </div>

        @if($tour->description)
            <p class="text-gray-500 text-sm mb-4 line-clamp-2">{{ $tour->description }}</p>
        @endif

        @if($tour->original_price && $tour->original_price > $tour->price_per_person)
            <p class="text-sm text-gray-400 line-through mb-1">Rp {{ number_format($tour->original_price, 0, ',', '.') }}</p>
        @endif

        <div class="mt-auto flex gap-3">
            <a href="{{ route('tour.tours.show', $tour->slug) }}"
               class="flex-1 text-center bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold py-2.5 rounded-xl border border-gray-200 transition text-sm">
                {{ __('Details') }}
            </a>
            <a href="{{ route('tour.booking.create', ['tour_id' => $tour->id]) }}"
               class="flex-1 text-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl transition text-sm">
                {{ __('Book Now') }}
            </a>
        </div>
    </div>
</div>