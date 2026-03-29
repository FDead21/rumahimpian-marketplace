<x-layout>

    {{-- Header --}}
    <div class="bg-gradient-to-br from-sky-600 to-blue-700 text-white py-16 text-center">
        <h1 class="text-4xl font-extrabold mb-2">{{ __('Browse Vehicles') }}</h1>
        <p class="text-sky-200 text-lg">{{ __('Find your perfect ride') }}</p>
    </div>

    {{-- Filters --}}
    <div class="max-w-7xl mx-auto px-4 pt-10">
        <form method="GET" action="{{ route('rental.vehicles.index') }}" class="flex flex-wrap gap-3 items-center justify-center">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('Search vehicle, brand, city...') }}"
                   class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-sky-400">

            <select name="type" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400">
                <option value="">{{ __('All Types') }}</option>
                <option value="car"       {{ request('type') === 'car'       ? 'selected' : '' }}>🚗 {{ __('Car') }}</option>
                <option value="motorbike" {{ request('type') === 'motorbike' ? 'selected' : '' }}>🛵 {{ __('Motorbike') }}</option>
                <option value="boat"      {{ request('type') === 'boat'      ? 'selected' : '' }}>⛵ {{ __('Boat') }}</option>
            </select>

            <input type="text" name="city" value="{{ request('city') }}"
                   placeholder="{{ __('City') }}"
                   class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm w-40 focus:outline-none focus:ring-2 focus:ring-sky-400">

            <button type="submit"
                    class="bg-sky-600 hover:bg-sky-700 text-white font-bold px-6 py-2.5 rounded-xl transition text-sm">
                🔍 {{ __('Search') }}
            </button>

            @if(request()->hasAny(['search', 'type', 'city']))
                <a href="{{ route('rental.vehicles.index') }}"
                   class="text-sm text-gray-500 hover:text-red-500 underline transition">{{ __('Clear') }}</a>
            @endif
        </form>
    </div>

    {{-- Grid --}}
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($vehicles as $vehicle)
                @include('rental.vehicle._card', ['vehicle' => $vehicle])
            @empty
                <div class="col-span-3 text-center py-16 text-gray-400">
                    <div class="text-5xl mb-4">🚗</div>
                    <p class="text-lg font-semibold">{{ __('No vehicles found.') }}</p>
                    <a href="{{ route('rental.vehicles.index') }}" class="text-sky-600 text-sm underline mt-2 inline-block">{{ __('Clear filters') }}</a>
                </div>
            @endforelse
        </div>
    </div>

</x-layout>