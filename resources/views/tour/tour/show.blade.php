<x-layout>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="bg-white">

    {{-- Back --}}
    <div class="max-w-6xl mx-auto px-4 pt-8">
        <a href="{{ route('tour.tours.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-emerald-600 mb-6 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Back to Tours') }}
        </a>
    </div>

    {{-- Gallery --}}
    @php
        $allImages = collect();
        if ($tour->media->count()) {
            $allImages = $tour->media->map(fn($m) => asset('storage/' . $m->file_path));
        } elseif ($tour->thumbnail) {
            $allImages = collect([asset('storage/' . $tour->thumbnail)]);
        }
        $categoryEmoji = match($tour->category) {
            'ADVENTURE'    => '',
            'CULTURAL'     => '',
            'NATURE'       => '',
            'WATER_SPORTS' => '',
            default        => '',
        };
    @endphp

    @if($allImages->count())
    <div class="max-w-6xl mx-auto px-4 mb-8"
        x-data="{
            lightboxOpen: false,
            activeImage: 0,
            images: {{ Js::from($allImages->values()) }},
            next() { this.activeImage = (this.activeImage + 1) % this.images.length },
            prev() { this.activeImage = (this.activeImage - 1 + this.images.length) % this.images.length },
            init() {
                if(this.images.length > 1) {
                    setInterval(() => { if(!this.lightboxOpen) this.next(); }, 5000);
                }
            }
        }"
        @keydown.escape.window="lightboxOpen = false"
        @keydown.arrow-right.window="if(lightboxOpen) next()"
        @keydown.arrow-left.window="if(lightboxOpen) prev()">

        <div class="relative h-[350px] md:h-[500px] w-full rounded-3xl overflow-hidden group shadow-sm border border-gray-100">
            <div class="flex w-full h-full transition-transform duration-700 ease-out"
                 :style="`transform: translateX(-${activeImage * 100}%)`">
                <template x-for="(img, index) in images" :key="index">
                    <div class="w-full h-full flex-shrink-0 relative cursor-zoom-in" @click="lightboxOpen = true">
                        <img :src="img" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/0 hover:bg-black/10 transition-colors duration-300 flex items-center justify-center">
                            <span class="opacity-0 group-hover:opacity-100 bg-white/90 text-gray-900 px-4 py-2 rounded-full text-sm font-bold shadow-lg transition-opacity duration-300">
                                {{ __('View Photos') }}
                            </span>
                        </div>
                    </div>
                </template>
            </div>
            <template x-if="images.length > 1">
                <div>
                    <button @click.prevent="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-3 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button @click.prevent="next()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-3 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-2 z-10">
                        <template x-for="(_, index) in images" :key="index">
                            <button @click="activeImage = index"
                                    class="h-2.5 rounded-full transition-all duration-300"
                                    :class="activeImage === index ? 'w-8 bg-emerald-600' : 'w-2.5 bg-white/70 hover:bg-white'">
                            </button>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        {{-- Lightbox --}}
        <div x-show="lightboxOpen"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[999] bg-black/95 flex items-center justify-center"
             style="display:none;">
            <button @click="lightboxOpen = false" class="absolute top-5 right-5 text-white hover:text-gray-300 z-50">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <template x-if="images.length > 1">
                <div>
                    <button @click="prev()" class="absolute left-4 z-50 p-2 bg-white/10 hover:bg-white/20 rounded-full text-white transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button @click="next()" class="absolute right-4 z-50 p-2 bg-white/10 hover:bg-white/20 rounded-full text-white transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </template>
            <div class="relative w-full h-full flex items-center justify-center p-8">
                <img :src="images[activeImage]" class="max-w-full max-h-[88vh] object-contain rounded-lg shadow-2xl">
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 bg-black/50 text-white px-4 py-1.5 rounded-full text-sm">
                    <span x-text="activeImage + 1"></span> / <span x-text="images.length"></span>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="max-w-6xl mx-auto px-4 mb-8">
        <div class="h-[300px] rounded-3xl bg-gradient-to-br from-emerald-100 to-teal-200 flex items-center justify-center text-8xl">
            {{ $categoryEmoji }}
        </div>
    </div>
    @endif

    {{-- Main Content --}}
    <div class="max-w-6xl mx-auto px-4 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            {{-- LEFT --}}
            <div class="lg:col-span-2 space-y-10">

                {{-- Title --}}
                <div>
                    @if($tour->is_featured)
                        <span class="bg-emerald-100 text-emerald-600 text-xs font-bold px-3 py-1 rounded-full inline-block mb-3">{{ __('Featured Tour') }}</span>
                    @endif
                    <div class="flex flex-wrap items-center gap-3 mb-2">
                        <h1 class="text-3xl font-extrabold text-gray-900">{{ $tour->name }}</h1>
                        <span class="text-sm bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1 rounded-full font-bold">
                            {{ $categoryEmoji }} {{ __($tour->category_label) }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-4 text-sm text-gray-500 mt-3">
                        <span>{{ $tour->duration_label ?? $tour->duration_days . ' ' . __('Day') }}</span>
                        <span>{{ __('Min') }} {{ $tour->min_participants }} {{ __('pax') }}@if($tour->max_participants) / {{ __('Max') }} {{ $tour->max_participants }} {{ __('pax') }}@endif</span>
                        @if($tour->meeting_point)
                            <span>📍 {{ $tour->meeting_point }}</span>
                        @endif
                    </div>
                </div>

                {{-- Description --}}
                @if($tour->description)
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">{{ __('About This Tour') }}</h2>
                    <div class="text-gray-600 leading-relaxed">{!! nl2br(e($tour->description)) !!}</div>
                </div>
                @endif

                @if($tour->youtube_url)
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Video Preview') }}</h2>
                    <div class="relative w-full overflow-hidden rounded-xl shadow-lg aspect-video">
                        @php
                            $video_id = '';
                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $tour->youtube_url, $matches)) {
                                $video_id = $matches[1];
                            }
                        @endphp
                        @if($video_id)
                            <iframe class="absolute top-0 left-0 w-full h-full"
                                src="https://www.youtube.com/embed/{{ $video_id }}?rel=0"
                                title="{{ $tour->name }}" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        @else
                            <div class="flex items-center justify-center h-full bg-gray-100 aspect-video">
                                <a href="{{ $tour->youtube_url }}" target="_blank" class="flex items-center gap-2 text-red-600 font-bold hover:underline">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                                    {{ __('Watch on YouTube') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Inclusions --}}
                @if(is_array($tour->inclusions) && count($tour->inclusions) > 0)
                <div class="mb-12" x-data="{ 
                        activeIdx: 0, 
                        showInclusionModal: false,
                        totalItems: {{ count($tour->inclusions) }},
                        autoPlayTimer: null,
                        startAutoPlay() {
                            this.autoPlayTimer = setInterval(() => {
                                // Only auto-rotate if the modal is NOT open
                                if (!this.showInclusionModal) {
                                    this.activeIdx = (this.activeIdx + 1) % this.totalItems;
                                }
                            }, 4000); // Changes image every 4 seconds
                        },
                        stopAutoPlay() {
                            clearInterval(this.autoPlayTimer);
                        }
                    }"
                    x-init="startAutoPlay()"
                    @mouseenter="stopAutoPlay()"
                    @mouseleave="startAutoPlay()">
                    
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __("What's Included") }}</h2>
                    
                    <div class="bg-white border border-gray-100 rounded-3xl p-3 shadow-sm flex flex-col md:flex-row gap-4 h-auto md:h-[450px]">
                        
                        {{-- The Left Sidebar (Buttons) --}}
                        <div class="w-full md:w-1/3 flex flex-row md:flex-col overflow-x-auto md:overflow-y-auto gap-2 p-1" style="-ms-overflow-style: none; scrollbar-width: none;">
                            @foreach($tour->inclusions as $index => $inclusion)
                                <button type="button" @click="activeIdx = {{ $index }}" 
                                        class="text-left px-5 py-4 rounded-2xl transition-all duration-300 shrink-0 md:shrink border-2 outline-none"
                                        :class="activeIdx === {{ $index }} ? 'bg-emerald-50 border-emerald-200 shadow-sm' : 'bg-transparent border-transparent hover:bg-gray-50'">
                                    <div class="flex items-center justify-between">
                                        <p class="font-bold text-sm transition-colors" :class="activeIdx === {{ $index }} ? 'text-emerald-600' : 'text-gray-700'">
                                            {{ $inclusion['item'] ?? __('Included Item') }}
                                        </p>
                                        <svg x-show="activeIdx === {{ $index }}" class="w-4 h-4 text-emerald-500 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </div>
                                </button>
                            @endforeach
                        </div>

                        {{-- The Right Side (Auto-changing Image) --}}
                        <button type="button" @click="showInclusionModal = true; document.body.style.overflow='hidden'" 
                                class="w-full md:w-2/3 relative rounded-2xl overflow-hidden bg-slate-900 h-[300px] md:h-full shrink-0 text-left cursor-zoom-in group focus:outline-none focus:ring-4 focus:ring-emerald-500/50">
                            @foreach($tour->inclusions as $index => $inclusion)
                                <div x-show="activeIdx === {{ $index }}" 
                                     x-transition:enter="transition ease-out duration-700"
                                     x-transition:enter-start="opacity-0 scale-105"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="absolute inset-0 w-full h-full flex flex-col">
                                    <div class="absolute inset-0">
                                        @if(isset($inclusion['image']) && $inclusion['image'])
                                            <img src="{{ asset('storage/' . $inclusion['image']) }}" class="w-full h-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-1000">
                                        @else
                                            <div class="w-full h-full bg-slate-800 flex items-center justify-center text-7xl opacity-50"></div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                                    </div>
                                    <div class="relative mt-auto p-6 md:p-8 text-white z-10">
                                        <span class="inline-block bg-emerald-600 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-3">{{ __('Feature') }} {{ $index + 1 }}</span>
                                        <h3 class="text-2xl md:text-3xl font-extrabold mb-2">{{ $inclusion['item'] ?? __('Included Item') }}</h3>
                                        <p class="text-gray-300 text-sm line-clamp-2">{{ __('Click to read more & view full image...') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </button>
                    </div>

                    {{-- The Fullscreen Modal --}}
                    <div x-show="showInclusionModal" style="display:none;" 
                         class="fixed inset-0 z-[100] bg-black/95 flex flex-col items-center justify-center p-4 md:p-8 backdrop-blur-sm" 
                         @click.self="showInclusionModal = false; document.body.style.overflow=''" 
                         @keydown.escape.window="showInclusionModal = false; document.body.style.overflow=''">
                        
                        <button type="button" @click="showInclusionModal = false; document.body.style.overflow=''" class="absolute top-6 right-6 text-white hover:text-emerald-400 z-50 p-2 transition">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        @foreach($tour->inclusions as $index => $inclusion)
                            <div x-show="activeIdx === {{ $index }}" class="w-full max-w-6xl flex flex-col md:flex-row gap-8 items-center"
                                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                                <div class="w-full md:w-3/5 flex items-center justify-center">
                                    @if(isset($inclusion['image']) && $inclusion['image'])
                                        <img src="{{ asset('storage/' . $inclusion['image']) }}" class="max-h-[50vh] md:max-h-[80vh] rounded-2xl object-contain shadow-2xl border border-gray-800">
                                    @else
                                        <div class="w-full aspect-video max-w-2xl bg-gray-900 rounded-2xl flex items-center justify-center text-7xl border border-gray-800"></div>
                                    @endif
                                </div>
                                <div class="w-full md:w-2/5 text-white bg-gray-900/50 p-8 rounded-3xl border border-gray-800">
                                    <span class="text-emerald-500 font-bold text-sm tracking-widest uppercase mb-2 block">{{ __('Feature') }} {{ $index + 1 }}</span>
                                    <h3 class="text-3xl md:text-4xl font-extrabold mb-6">{{ $inclusion['item'] }}</h3>
                                    <div class="text-gray-300 text-lg leading-relaxed whitespace-pre-wrap">{{ $inclusion['description'] ?? __('No additional description provided for this item.') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Itinerary --}}
                @if(is_array($tour->itinerary) && count($tour->itinerary) > 0)
                <div x-data="{ activeDay: 0 }">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">{{ __('Itinerary') }}</h2>

                    {{-- Day tabs --}}
                    <div class="flex gap-2 flex-wrap mb-6">
                        @foreach($tour->itinerary as $i => $day)
                        <button @click="activeDay = {{ $i }}"
                                class="px-4 py-2 rounded-xl text-sm font-bold transition border-2"
                                :class="activeDay === {{ $i }} ? 'bg-emerald-600 border-emerald-600 text-white' : 'bg-white border-gray-200 text-gray-600 hover:border-emerald-400'">
                            {{ __('Day') }} {{ $day['day'] ?? $i + 1 }}: {{ $day['title'] ?? '' }}
                        </button>
                        @endforeach
                    </div>

                    {{-- Day content --}}
                    @foreach($tour->itinerary as $i => $day)
                    <div x-show="activeDay === {{ $i }}"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                        <h3 class="font-extrabold text-gray-900 text-lg mb-6">
                            {{ __('Day') }} {{ $day['day'] ?? $i + 1 }}: {{ $day['title'] ?? '' }}
                        </h3>
                        @if(!empty($day['items']))
                        <div class="space-y-4">
                            @foreach($day['items'] as $item)
                            <div class="flex gap-4">
                                <div class="shrink-0 text-right w-20">
                                    @if(!empty($item['time']))
                                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">{{ $item['time'] }}</span>
                                    @endif
                                </div>
                                <div class="flex-1 pb-4 border-b border-gray-50 last:border-0 last:pb-0">
                                    <p class="font-bold text-gray-900 text-sm">{{ $item['activity'] ?? '' }}</p>
                                    @if(!empty($item['description']))
                                        <p class="text-xs text-gray-500 mt-1">{{ $item['description'] }}</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Meeting Point Map --}}
                @if($tour->meeting_point_lat && $tour->meeting_point_lng)
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Meeting Point') }}</h2>
                    @if($tour->meeting_point)
                        <p class="text-gray-600 mb-3">{{ $tour->meeting_point }}</p>
                    @endif
                    <div class="relative rounded-xl overflow-hidden border shadow-sm" x-data="{ expanded: false }">
                        <div id="tour-map" class="w-full transition-all duration-500 z-0"
                             :class="expanded ? 'h-80' : 'h-48'"></div>

                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none"
                             x-show="!expanded" x-transition.opacity>
                            <span class="bg-black/50 text-white text-xs font-bold px-3 py-1.5 rounded-full backdrop-blur-sm">
                                {{ __('Click map to interact') }}
                            </span>
                        </div>

                        <button @click="expanded = !expanded"
                                class="absolute bottom-2 right-2 z-[999] bg-white shadow-md border border-gray-200 text-gray-700 hover:bg-gray-50 text-xs font-bold px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                            <span x-text="expanded ? '{{ __('Collapse') }}' : '{{ __('Expand') }}'"></span>
                            <svg class="w-3 h-3 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <a href="https://www.google.com/maps?q={{ $tour->meeting_point_lat }},{{ $tour->meeting_point_lng }}"
                           target="_blank"
                           class="absolute bottom-2 left-2 z-[999] bg-white shadow-md border border-gray-200 text-gray-700 hover:bg-gray-50 text-xs font-bold px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            {{ __('Open in Maps') }}
                        </a>
                    </div>
                </div>

                @push('head')
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                @endpush

                @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const map = L.map('tour-map', {
                            scrollWheelZoom: false,
                            zoomControl: true,
                        }).setView([{{ $tour->meeting_point_lat }}, {{ $tour->meeting_point_lng }}], 15);

                        map.getContainer().addEventListener('click', () => map.scrollWheelZoom.enable());
                        map.getContainer().addEventListener('mouseleave', () => map.scrollWheelZoom.disable());

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap'
                        }).addTo(map);

                        L.marker([{{ $tour->meeting_point_lat }}, {{ $tour->meeting_point_lng }}])
                            .addTo(map)
                            .bindPopup(`
                                <div style="min-width:160px">
                                    <strong style="font-size:13px">{{ __('Meeting Point') }}</strong><br>
                                    <span style="color:#6b7280;font-size:12px">{{ addslashes($tour->meeting_point ?? $tour->name) }}</span>
                                </div>
                            `)
                            .openPopup();

                        window.addEventListener('resize', () => map.invalidateSize());
                        document.querySelector('#tour-map')
                            .closest('[x-data]')
                            ?.__x?.$watch('expanded', () => {
                                setTimeout(() => map.invalidateSize(), 520);
                            });
                    });
                </script>
                @endpush
                @endif

            </div>

            {{-- RIGHT: Sticky Booking Card --}}
            <div class="lg:col-span-1">
                <div class="sticky top-6 bg-white border border-gray-200 rounded-2xl shadow-lg p-6 space-y-4">

                    <div class="bg-emerald-50 border border-emerald-100 rounded-xl px-5 py-4">
                        <p class="text-sm text-emerald-500 font-semibold mb-1">{{ __('Price per Person') }}</p>
                        @if($tour->original_price && $tour->original_price > $tour->price_per_person)
                            <p class="text-sm text-gray-400 line-through">Rp {{ number_format($tour->original_price, 0, ',', '.') }}</p>
                        @endif
                        <p class="text-3xl font-extrabold text-emerald-700">Rp {{ number_format($tour->price_per_person, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-400 font-medium">/ {{ __('person') }}</p>
                    </div>

                    <div class="space-y-3 pt-2 pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <span class="text-lg"></span>
                            <span>{{ $tour->duration_label ?? $tour->duration_days . ' ' . __('Day') }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <span class="text-lg"></span>
                            <span>{{ __('Min') }} {{ $tour->min_participants }} @if($tour->max_participants) / {{ __('Max') }} {{ $tour->max_participants }}@endif {{ __('pax') }}</span>
                        </div>
                        @if($tour->meeting_point)
                        <div class="flex items-start gap-3 text-sm text-gray-600">
                            <span class="text-lg mt-0.5"></span>
                            <span>{{ $tour->meeting_point }}</span>
                        </div>
                        @endif
                    </div>

                    @if($tour->user)
                    <div class="flex items-center gap-3 px-2 py-2">
                        @php
                            $avatarSrc = $tour->user->avatar_url
                                ? (str_starts_with($tour->user->avatar_url, 'http') ? $tour->user->avatar_url : asset('storage/' . $tour->user->avatar_url))
                                : null;
                        @endphp
                        @if($avatarSrc)
                            <img src="{{ $avatarSrc }}" class="w-10 h-10 rounded-full object-cover shadow-sm border border-gray-100">
                        @else
                            <div class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold shadow-sm">
                                {{ substr($tour->user->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">{{ __('Tour Guide') }}</p>
                            <p class="text-sm font-bold text-gray-900">{{ $tour->user->name }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="space-y-3 pt-2">
                        <a href="{{ route('tour.booking.create', ['tour_id' => $tour->id]) }}"
                           class="flex items-center justify-center gap-2 w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-emerald-600/20 transition transform hover:-translate-y-0.5 text-lg">
                            {{ __('Book This Tour') }}
                        </a>

                        @php
                            $phone = $tour->user->phone_number ?? null;
                            if ($phone) {
                                $phone = preg_replace('/[^0-9]/', '', $phone);
                                if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);
                                
                                // Localized Message Components
                                $intro = __('Halo, saya tertarik dengan tour:');
                                $durationLabel = __('Durasi');
                                $priceLabel = __('Harga');
                                $personLabel = __('orang');
                                $footer = __('Bisa info lebih lanjut?');
                                $linkLabel = __('Link');
                                $currentDuration = $tour->duration_label ?? $tour->duration_days . ' ' . __('Day');

                                $waMsg = urlencode(
                                    "$intro\n\n" .
                                    "*{$tour->name}*\n" .
                                    "$durationLabel: $currentDuration\n" .
                                    "$priceLabel: Rp " . number_format($tour->price_per_person, 0, ',', '.') . "/$personLabel\n\n" .
                                    "$footer\n" .
                                    "$linkLabel: " . request()->url()
                                );
                                $waUrl = "https://wa.me/{$phone}?text={$waMsg}";
                            }
                        @endphp

                        @if(!empty($waUrl))
                        <a href="{{ $waUrl }}" target="_blank"
                           class="flex items-center justify-center gap-2 w-full bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold py-3 rounded-xl transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            {{ __('Chat via WhatsApp') }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Related --}}
    @if($related->count())
    <div class="bg-gray-50 border-t border-gray-100 py-14">
        <div class="max-w-6xl mx-auto px-4">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">{{ __('Other Tours You Might Like') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($related as $tour)
                    @include('tour.tour._card', ['tour' => $tour])
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>

</x-layout>