<x-layout>
    <x-portal-hero/>
    <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white py-16 text-center">
        <h1 class="text-4xl font-extrabold mb-2">{{ __('Explore Tours') }}</h1>
        <p class="text-emerald-200 text-lg">{{ __('Guided trips, cultural experiences, and adventure packages') }}</p>
    </div>

    {{-- Category Tabs --}}
    <div class="max-w-7xl mx-auto px-4 pt-10">
        <div class="flex gap-3 justify-center flex-wrap">
            @php
                $cats = [
                    ''            => ' ' . __('All'),
                    'ADVENTURE'   => ' ' . __('Adventure'),
                    'CULTURAL'    => ' ' . __('Cultural'),
                    'NATURE'      => ' ' . __('Nature'),
                    'WATER_SPORTS' => ' ' . __('Water Sports'),
                    'CUSTOM'      => ' ' . __('Custom'),
                ];
            @endphp
            @foreach($cats as $key => $label)
                <a href="{{ route('tour.tours.index', $key ? ['category' => strtolower($key)] : []) }}"
                   class="px-5 py-2.5 rounded-full font-bold text-sm border-2 transition
                          {{ request('category', '') === strtolower($key) ? 'bg-emerald-600 border-emerald-600 text-white' : 'bg-white border-gray-200 text-gray-700 hover:border-emerald-400' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Featured Tours --}}
    <div class="max-w-7xl mx-auto px-4 py-12">
        @if($featured->count())
            <h2 class="text-2xl font-bold text-gray-900 mb-8"> {{ __('Featured Tours') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($featured as $tour)
                    @include('tour.tour._card', ['tour' => $tour])
                @endforeach
            </div>
            <div class="text-center mt-10">
                <a href="{{ route('tour.tours.index') }}"
                   class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-8 py-3 rounded-xl transition shadow-lg shadow-emerald-600/20">
                    {{ __('Browse All Tours') }} →
                </a>
            </div>
        @else
            <div class="text-center py-16 text-gray-400">
                <div class="text-5xl mb-4"></div>
                <p class="text-lg font-semibold">{{ __('No tours available yet.') }}</p>
            </div>
        @endif
    </div>

</x-layout>