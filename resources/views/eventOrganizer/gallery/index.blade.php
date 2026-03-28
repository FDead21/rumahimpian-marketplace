<x-eo-layout>

    <div class="bg-gradient-to-br from-rose-600 to-pink-700 text-white py-16 text-center">
        <h1 class="text-4xl font-extrabold mb-2">{{ __('Our Gallery') }}</h1>
        <p class="text-rose-200 text-lg">{{ __("A collection of events we've had the pleasure of organizing") }}</p>
    </div>

    {{-- Filter Tabs --}}
    <div class="max-w-7xl mx-auto px-4 py-8"
         x-data="{ filter: 'all' }">

        <div class="flex flex-wrap gap-3 mb-8 justify-center">
            @foreach(['all', 'Wedding', 'Corporate', 'Birthday', 'Gathering', 'Other'] as $type)
            <button @click="filter = '{{ strtolower($type) }}'"
                    :class="filter === '{{ strtolower($type) }}' ? 'bg-rose-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-rose-300'"
                    class="px-4 py-2 rounded-full text-sm font-bold transition">
                {{ $type === 'all' ? __('All Events') : __($type) }}
            </button>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($galleryEvents as $event)
            <div x-show="filter === 'all' || filter === '{{ strtolower($event->event_type) }}'"
                 x-transition
                 class="group bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <a href="{{ route('eventOrganizer.gallery.show', $event->slug) }}" class="block relative h-56 overflow-hidden">
                    @if($event->cover_photo)
                        <img src="{{ asset('storage/' . $event->cover_photo) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-rose-100 to-pink-200 flex items-center justify-center text-5xl">🎉</div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    @if($event->event_type)
                        <div class="absolute top-3 left-3 bg-rose-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                            {{ __($event->event_type) }}
                        </div>
                    @endif
                    <div class="absolute bottom-3 left-3 right-3 text-white text-sm font-bold opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        {{ $event->media->count() }} {{ __('photos') }} →
                    </div>
                </a>
                <div class="p-4">
                    <h3 class="font-bold text-gray-900 group-hover:text-rose-600 transition">{{ $event->title }}</h3>
                    @if($event->event_date)
                        <p class="text-xs text-gray-400 mt-1">{{ $event->event_date->format('M Y') }}</p>
                    @endif
                </div>
            </div>
            @empty
                <div class="col-span-3 text-center py-16 text-gray-400">
                    <div class="text-5xl mb-4">🖼️</div>
                    <p class="text-lg font-semibold">{{ __('No gallery events published yet.') }}</p>
                </div>
            @endforelse
        </div>

        {{ $galleryEvents->links() }}
    </div>

</x-eo-layout>