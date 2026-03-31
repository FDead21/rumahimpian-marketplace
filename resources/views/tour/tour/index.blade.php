<x-layout>

    <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white py-16 text-center">
        <h1 class="text-4xl font-extrabold mb-2">{{ __('Browse Tours') }}</h1>
        <p class="text-emerald-200 text-lg">{{ __('Find your next adventure') }}</p>
    </div>

    {{-- Filters --}}
    <div class="max-w-7xl mx-auto px-4 pt-10">
        <form method="GET" action="{{ route('tour.tours.index') }}" class="flex flex-wrap gap-3 items-center justify-center">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('Search tours, destinations...') }}"
                   class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-emerald-400">

            <select name="category" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <option value="">{{ __('All Categories') }}</option>
                <option value="adventure"    {{ request('category') === 'adventure'    ? 'selected' : '' }}> {{ __('Adventure') }}</option>
                <option value="cultural"     {{ request('category') === 'cultural'     ? 'selected' : '' }}> {{ __('Cultural') }}</option>
                <option value="nature"       {{ request('category') === 'nature'       ? 'selected' : '' }}> {{ __('Nature') }}</option>
                <option value="water_sports" {{ request('category') === 'water_sports' ? 'selected' : '' }}> {{ __('Water Sports') }}</option>
                <option value="custom"       {{ request('category') === 'custom'       ? 'selected' : '' }}> {{ __('Custom') }}</option>
            </select>

            <select name="duration" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <option value="">{{ __('Any Duration') }}</option>
                @foreach([1, 2, 3, 5, 7] as $d)
                    <option value="{{ $d }}" {{ request('duration') == $d ? 'selected' : '' }}>
                        {{ $d }} {{ __('Day') }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-xl transition text-sm">
                {{ __('Search') }}
            </button>

            @if(request()->hasAny(['search', 'category', 'duration']))
                <a href="{{ route('tour.tours.index') }}" class="text-sm text-gray-500 hover:text-red-500 underline transition">{{ __('Clear') }}</a>
            @endif
        </form>
    </div>

    {{-- Grid --}}
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($tours as $tour)
                @include('tour.tour._card', ['tour' => $tour])
            @empty
                <div class="col-span-3 text-center py-16 text-gray-400">
                    <div class="text-5xl mb-4"></div>
                    <p class="text-lg font-semibold">{{ __('No tours found.') }}</p>
                    <a href="{{ route('tour.tours.index') }}" class="text-emerald-600 text-sm underline mt-2 inline-block">{{ __('Clear filters') }}</a>
                </div>
            @endforelse
        </div>
    </div>

</x-layout>