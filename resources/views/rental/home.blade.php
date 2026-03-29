<x-layout>
    <x-portal-hero/>
    {{-- Type Filter Tabs --}}
    <div class="max-w-7xl mx-auto px-4 pt-10">
        <div class="flex gap-3 justify-center flex-wrap">
            @foreach(['' => '🚗 ' . __('All'), 'CAR' => '🚗 ' . __('Cars'), 'MOTORBIKE' => '🛵 ' . __('Motorbikes'), 'BOAT' => '⛵ ' . __('Boats')] as $type => $label)
                <a href="{{ route('rental.vehicles.index', $type ? ['type' => strtolower($type)] : []) }}"
                   class="px-5 py-2.5 rounded-full font-bold text-sm border-2 transition
                          {{ request('type', '') === strtolower($type) ? 'bg-sky-600 border-sky-600 text-white' : 'bg-white border-gray-200 text-gray-700 hover:border-sky-400' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Featured Vehicles --}}
    <div class="max-w-7xl mx-auto px-4 py-12">
        @if($featured->count())
            <h2 class="text-2xl font-bold text-gray-900 mb-8">⭐ {{ __('Featured Vehicles') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($featured as $vehicle)
                    @include('rental.vehicle._card', ['vehicle' => $vehicle])
                @endforeach
            </div>
            <div class="text-center mt-10">
                <a href="{{ route('rental.vehicles.index') }}"
                   class="inline-block bg-sky-600 hover:bg-sky-700 text-white font-bold px-8 py-3 rounded-xl transition shadow-lg shadow-sky-600/20">
                    {{ __('Browse All Vehicles') }} →
                </a>
            </div>
        @else
            <div class="text-center py-16 text-gray-400">
                <div class="text-5xl mb-4">🚗</div>
                <p class="text-lg font-semibold">{{ __('No vehicles available yet.') }}</p>
            </div>
        @endif
    </div>

</x-layout>