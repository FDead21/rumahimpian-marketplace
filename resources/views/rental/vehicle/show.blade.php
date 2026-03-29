<x-layout>
@if($vehicle->latitude && $vehicle->longitude)
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush
@endif
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="bg-white">

    {{-- Back --}}
    <div class="max-w-6xl mx-auto px-4 pt-8">
        <a href="{{ route('rental.vehicles.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-sky-600 mb-6 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Back to Vehicles') }}
        </a>
    </div>

    {{-- Gallery Carousel --}}
    @php
        $allImages = collect();
        if ($vehicle->media->count()) {
            $allImages = $vehicle->media->map(fn($m) => asset('storage/' . $m->file_path));
        } elseif ($vehicle->thumbnail) {
            $allImages = collect([asset('storage/' . $vehicle->thumbnail)]);
        }

        $typeEmoji = match($vehicle->vehicle_type) {
            'CAR'       => '🚗',
            'MOTORBIKE' => '🛵',
            'BOAT'      => '⛵',
            default     => '🚗',
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
                                🔍 {{ __('View Photos') }}
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
                                    :class="activeImage === index ? 'w-8 bg-sky-600' : 'w-2.5 bg-white/70 hover:bg-white'">
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
        <div class="h-[300px] rounded-3xl bg-gradient-to-br from-sky-100 to-blue-200 flex items-center justify-center text-8xl">
            {{ $typeEmoji }}
        </div>
    </div>
    @endif

    {{-- Main Content --}}
    <div class="max-w-6xl mx-auto px-4 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            {{-- LEFT: Details --}}
            <div class="lg:col-span-2 space-y-8">

                <div>
                    @if($vehicle->is_featured)
                        <span class="bg-sky-100 text-sky-600 text-xs font-bold px-3 py-1 rounded-full inline-block mb-3">⭐ {{ __('Featured') }}</span>
                    @endif
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-3xl font-extrabold text-gray-900">{{ $vehicle->name }}</h1>
                        <span class="text-sm bg-sky-50 text-sky-700 border border-sky-100 px-3 py-1 rounded-full font-bold">
                            {{ $typeEmoji }} {{ __($vehicle->vehicle_type) }}
                        </span>
                    </div>
                    <p class="text-gray-500">{{ $vehicle->brand }} {{ $vehicle->year }}</p>
                </div>

                @if($vehicle->description)
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">{{ __('About This Vehicle') }}</h2>
                    <div class="text-gray-600 leading-relaxed">{!! nl2br(e($vehicle->description)) !!}</div>
                </div>
                @endif

                {{-- Specifications --}}
                @if($vehicle->specifications)
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Specifications') }}</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($vehicle->specifications as $key => $val)
                            <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                <p class="text-xs text-gray-400 uppercase tracking-wide font-bold mb-1">{{ $key }}</p>
                                <p class="text-gray-900 font-semibold">{{ $val }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                
                 @if($vehicle->latitude && $vehicle->longitude)
                    <div class="mt-2 rounded-xl overflow-hidden border border-gray-100 relative" 
                        x-data="{ expanded: false }">

                        {{-- Map container --}}
                        <div id="rental-map" 
                            class="w-full transition-all duration-500"
                            :class="expanded ? 'h-72' : 'h-40'">
                        </div>

                        {{-- Overlay hint (shown initially so user knows to click) --}}
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none"
                            x-show="!expanded" x-transition.opacity>
                            <span class="bg-black/50 text-white text-xs font-bold px-3 py-1.5 rounded-full backdrop-blur-sm">
                                🗺️ {{ __('Click map to interact') }}
                            </span>
                        </div>

                        {{-- Expand/collapse toggle --}}
                        <button @click="expanded = !expanded"
                                class="absolute bottom-2 right-2 z-[999] bg-white shadow-md border border-gray-200 text-gray-700 hover:bg-gray-50 text-xs font-bold px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                            <span x-text="expanded ? '{{ __('Collapse') }}' : '{{ __('Expand') }}'"></span>
                            <svg class="w-3 h-3 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- Open in Google Maps --}}
                        <a href="https://www.google.com/maps?q={{ $vehicle->latitude }},{{ $vehicle->longitude }}"
                        target="_blank"
                        class="absolute bottom-2 left-2 z-[999] bg-white shadow-md border border-gray-200 text-gray-700 hover:bg-gray-50 text-xs font-bold px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            {{ __('Open in Maps') }}
                        </a>
                    </div>

                    @push('head')
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                    @endpush

                    @push('scripts')
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const map = L.map('rental-map', {
                                scrollWheelZoom: false,   // disabled by default — enable on click
                                zoomControl: true,
                                dragging: true,
                            }).setView([{{ $vehicle->latitude }}, {{ $vehicle->longitude }}], 15);

                            // Enable scroll zoom only when map is clicked/focused
                            map.once('focus', function () { map.scrollWheelZoom.enable(); });
                            map.getContainer().addEventListener('click', function () {
                                map.scrollWheelZoom.enable();
                            });
                            // Disable scroll zoom when mouse leaves
                            map.getContainer().addEventListener('mouseleave', function () {
                                map.scrollWheelZoom.disable();
                            });

                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '© OpenStreetMap'
                            }).addTo(map);

                            const marker = L.marker([{{ $vehicle->latitude }}, {{ $vehicle->longitude }}])
                                .addTo(map)
                                .bindPopup(`
                                    <div style="min-width:160px">
                                        <strong style="font-size:13px">{{ addslashes($vehicle->name) }}</strong><br>
                                        <span style="color:#6b7280;font-size:12px">📍 {{ addslashes($vehicle->city) }}</span><br>
                                        <span style="color:#6b7280;font-size:11px">Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}/hari</span>
                                    </div>
                                `)
                                .openPopup();

                            // Invalidate size when expanded (Alpine changes height)
                            window.addEventListener('resize', function () { map.invalidateSize(); });
                            document.querySelector('[id="rental-map"]')
                                .closest('[x-data]')
                                ?.__x?.$watch('expanded', () => {
                                    setTimeout(() => map.invalidateSize(), 520);
                                });
                        });
                    </script>
                    @endpush
                    @endif
            </div>

            {{-- RIGHT: Sticky Contact Card --}}
            <div class="lg:col-span-1">
                <div class="sticky top-6 bg-white border border-gray-200 rounded-2xl shadow-lg p-6 space-y-4">

                    <div class="bg-sky-50 border border-sky-100 rounded-xl px-5 py-4">
                        <p class="text-sm text-sky-500 font-semibold mb-1">{{ __('Rental Price') }}</p>
                        <p class="text-3xl font-extrabold text-sky-700">Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-400 font-medium">/ {{ __('day') }}</p>
                    </div>

                    <div class="space-y-3 pt-2 pb-4 border-b border-gray-100">
                        @if($vehicle->max_passengers)
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <span class="text-lg">👤</span>
                            <span>{{ __('Up to') }} <strong>{{ $vehicle->max_passengers }}</strong> {{ __('passengers') }}</span>
                        </div>
                        @endif
                        @if($vehicle->city)
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <span class="text-lg">📍</span>
                            <span>{{ $vehicle->city }}</span>
                        </div>
                        @endif
                         @if($vehicle->address)
                        <div class="flex items-start gap-3 text-sm text-gray-600">
                            <span class="text-lg mt-0.5">🗺️</span>
                            <span>{{ $vehicle->address }}</span>
                        </div>
                        @endif
                    </div>

                    @if($vehicle->user)
                    <div class="flex items-center gap-3 px-2 py-2">
                        @php
                            $avatarSrc = $vehicle->user->avatar_url
                                ? (str_starts_with($vehicle->user->avatar_url, 'http') ? $vehicle->user->avatar_url : asset('storage/' . $vehicle->user->avatar_url))
                                : null;
                        @endphp
                        @if($avatarSrc)
                            <img src="{{ $avatarSrc }}" class="w-10 h-10 rounded-full object-cover shadow-sm border border-gray-100">
                        @else
                            <div class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold shadow-sm">
                                {{ substr($vehicle->user->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">{{ __('Managed By') }}</p>
                            <p class="text-sm font-bold text-gray-900">{{ $vehicle->user->name }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- WhatsApp CTA --}}
                    @php
                        $phone = $vehicle->user->phone_number ?? null;
                        if ($phone) {
                            $phone = preg_replace('/[^0-9]/', '', $phone);
                            if (str_starts_with($phone, '0')) {
                                $phone = '62' . substr($phone, 1);
                            }
                            $waMessage = urlencode(
                                "Halo, saya tertarik menyewa kendaraan:\n\n" .
                                "*{$vehicle->name}*\n" .
                                "Tipe: {$vehicle->vehicle_type}\n" .
                                "Harga: Rp " . number_format($vehicle->price_per_day, 0, ',', '.') . "/hari\n\n" .
                                "Bisa minta info lebih lanjut? Terima kasih.\n" .
                                "Link: " . request()->url()
                            );
                            $waUrl = "https://wa.me/{$phone}?text={$waMessage}";
                        }
                    @endphp

                    <div class="space-y-3 pt-2">
                        @if(!empty($waUrl))
                        <a href="{{ $waUrl }}" target="_blank"
                        class="flex items-center justify-center gap-2 w-full bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold py-3.5 rounded-xl transition shadow-lg shadow-[#25D366]/20 text-lg">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            {{ __('Chat via WhatsApp') }}
                        </a>
                        @else
                        <p class="text-center text-sm text-gray-400">{{ __('No WhatsApp number set for this owner.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Related Vehicles --}}
    @if($related->count())
    <div class="bg-gray-50 border-t border-gray-100 py-14">
        <div class="max-w-6xl mx-auto px-4">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">{{ __('Other Vehicles You Might Like') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($related as $vehicle)
                    @include('rental.vehicle._card', ['vehicle' => $vehicle])
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>

</x-layout>