@php
    $thumb = $vehicle->media->first()
        ? asset('storage/' . $vehicle->media->first()->file_path)
        : ($vehicle->thumbnail ? asset('storage/' . $vehicle->thumbnail) : null);

    $typeEmoji = match($vehicle->vehicle_type) {
        'CAR'       => '',
        'MOTORBIKE' => '',
        'BOAT'      => '',
        default     => '',
    };
@endphp

<div class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative flex flex-col">

    @if($vehicle->is_featured)
        <div class="absolute top-4 left-4 z-10 bg-sky-600 text-white text-xs font-bold px-3 py-1 rounded-full">{{ __('Featured') }}</div>
    @endif

    {{-- Image --}}
    <div class="relative h-52 overflow-hidden">
        @if($thumb)
            <img src="{{ $thumb }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
        @else
            <div class="w-full h-full bg-gradient-to-br from-sky-100 to-blue-200 flex items-center justify-center text-6xl">{{ $typeEmoji }}</div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
        <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-lg">
            <span class="text-sky-700 font-extrabold text-xl">Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}</span>
            <span class="text-gray-500 text-xs font-medium">/{{ __('day') }}</span>
        </div>
        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-2 py-1 rounded-lg text-xs font-bold text-gray-700">
            {{ $typeEmoji }} {{ __($vehicle->vehicle_type) }}
        </div>
    </div>

    {{-- Content --}}
    <div class="p-6 flex-1 flex flex-col">
        <h3 class="font-bold text-xl text-gray-900 mb-1 group-hover:text-sky-600 transition">{{ $vehicle->name }}</h3>
        <p class="text-sm text-gray-500 mb-1">{{ $vehicle->brand }} {{ $vehicle->year }}</p>

        @if($vehicle->max_passengers)
            <p class="text-sm text-gray-500 mb-3">{{ __('Up to') }} {{ $vehicle->max_passengers }} {{ __('passengers') }}</p>
        @endif

        @if($vehicle->city)
            <p class="text-sm text-gray-500 mb-4">📍 {{ $vehicle->city }}</p>
        @endif

        {{-- Specs preview --}}
        @if($vehicle->specifications)
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach(array_slice($vehicle->specifications, 0, 3) as $key => $val)
                    {{-- Localize both the Key and the Value if possible --}}
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">
                        {{ __($key) }}: {{ __($val) }}
                    </span>
                @endforeach
            </div>
        @endif

        <div class="mt-auto">
            <a href="{{ route('rental.vehicles.show', $vehicle->slug) }}"
               class="block text-center bg-sky-600 hover:bg-sky-700 text-white font-bold py-2.5 rounded-xl transition text-sm">
                {{ __('View Details') }} →
            </a>
        </div>
    </div>
</div>