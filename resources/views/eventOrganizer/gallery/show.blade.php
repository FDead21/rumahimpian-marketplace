{{-- eo/gallery/gallery-show.blade.php --}}
<x-eo-layout>

    {{-- Hero / Cover --}}
    <div class="relative h-80 md:h-[28rem] overflow-hidden bg-gray-900">
        @if($event->cover_photo)
            <img src="{{ asset('storage/' . $event->cover_photo) }}"
                 class="w-full h-full object-cover opacity-70">
        @else
            <div class="w-full h-full bg-gradient-to-br from-rose-100 to-pink-200 flex items-center justify-center text-8xl">🎉</div>
        @endif

        {{-- Gradient overlay --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>

        {{-- Back button --}}
        <div class="absolute top-6 left-6">
            <a href="{{ route('eventOrganizer.gallery.index') }}"
               class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm text-white text-sm font-semibold px-4 py-2 rounded-full hover:bg-white/30 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Gallery
            </a>
        </div>

        {{-- Event info overlay --}}
        <div class="absolute bottom-0 left-0 right-0 px-6 pb-8 md:px-12">
            <div class="max-w-4xl mx-auto">
                @if($event->event_type)
                    <span class="inline-block bg-rose-600 text-white text-xs font-bold px-3 py-1 rounded-full mb-3 uppercase tracking-wider">
                        {{ $event->event_type }}
                    </span>
                @endif
                <h1 class="text-3xl md:text-5xl font-extrabold text-white leading-tight mb-2">
                    {{ $event->title }}
                </h1>
                <div class="flex items-center gap-4 text-rose-200 text-sm">
                    @if($event->event_date)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $event->event_date->format('d F Y') }}
                        </span>
                    @endif
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $event->media->count() }} photos
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 py-12">

        {{-- Description --}}
        @if($event->description)
            <div class="max-w-3xl mx-auto text-center mb-12">
                <p class="text-gray-600 text-lg leading-relaxed">{{ $event->description }}</p>
            </div>
        @endif

        {{-- Photo Grid --}}
        @if($event->media->count())
            <div x-data="lightbox()" class="columns-1 sm:columns-2 md:columns-3 gap-4 space-y-4">
                @foreach($event->media as $index => $photo)
                    <div class="break-inside-avoid">
                        <div class="group relative overflow-hidden rounded-2xl cursor-pointer bg-gray-100"
                             @click="open({{ $index }})">
                            <img src="{{ asset('storage/' . $photo->file_path) }}"
                                 class="w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                 alt="{{ $photo->caption ?? $event->title }}">

                            {{-- Hover overlay --}}
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center">
                                <svg class="w-10 h-10 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 drop-shadow-lg"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                </svg>
                            </div>

                            @if($photo->caption)
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent px-3 py-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <p class="text-white text-xs font-medium">{{ $photo->caption }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- Lightbox Modal --}}
                <div x-show="isOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4"
                     @click.self="close()"
                     @keydown.escape.window="close()"
                     @keydown.arrow-left.window="prev()"
                     @keydown.arrow-right.window="next()"
                     style="display: none;">

                    {{-- Close --}}
                    <button @click="close()"
                            class="absolute top-4 right-4 text-white/70 hover:text-white transition z-10">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    {{-- Prev --}}
                    <button @click="prev()"
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition z-10 bg-black/30 rounded-full p-2">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    {{-- Image --}}
                    <div class="max-w-5xl max-h-[85vh] flex flex-col items-center">
                        <template x-for="(photo, i) in photos" :key="i">
                            <div x-show="current === i" class="text-center">
                                <img :src="photo.src"
                                     class="max-h-[75vh] max-w-full object-contain rounded-xl shadow-2xl"
                                     :alt="photo.caption">
                                <p x-show="photo.caption"
                                   x-text="photo.caption"
                                   class="text-white/70 text-sm mt-3"></p>
                                <p class="text-white/40 text-xs mt-1">
                                    <span x-text="current + 1"></span> / <span x-text="photos.length"></span>
                                </p>
                            </div>
                        </template>
                    </div>

                    {{-- Next --}}
                    <button @click="next()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition z-10 bg-black/30 rounded-full p-2">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            <script>
                function lightbox() {
                    return {
                        isOpen: false,
                        current: 0,
                        photos: @json($event->media->map(fn($m) => [
                            'src'     => asset('storage/' . $m->file_path),
                            'caption' => $m->caption,
                        ])),
                        open(index) { this.isOpen = true; this.current = index; },
                        close()     { this.isOpen = false; },
                        prev()      { this.current = (this.current - 1 + this.photos.length) % this.photos.length; },
                        next()      { this.current = (this.current + 1) % this.photos.length; },
                    }
                }
            </script>
        @else
            <div class="text-center py-20 text-gray-400">
                <div class="text-6xl mb-4">🖼️</div>
                <p class="text-lg font-semibold">No photos uploaded for this event yet.</p>
            </div>
        @endif

        {{-- Back CTA --}}
        <div class="mt-16 text-center">
            <a href="{{ route('eventOrganizer.gallery.index') }}"
               class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-bold px-8 py-3 rounded-full transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to All Events
            </a>
        </div>
    </div>

</x-eo-layout>