<x-eo-layout>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="bg-white">

    {{-- Back --}}
    <div class="max-w-6xl mx-auto px-4 pt-8">
        <a href="{{ route('eo.packages') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-rose-600 mb-6 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Packages
        </a>
    </div>

    {{-- ========== GALLERY ========== --}}
    @php
        $allImages = collect();
        if ($package->media->count()) {
            $allImages = $package->media->map(fn($m) => asset('storage/' . $m->file_path));
        } elseif ($package->thumbnail) {
            $allImages = collect([asset('storage/' . $package->thumbnail)]);
        }
    @endphp

    @if($allImages->count())
    <div class="max-w-6xl mx-auto px-4 mb-8"
        x-data="{
            lightboxOpen: false,
            activeImage: 0,
            images: {{ Js::from($allImages->values()) }},
            next() { this.activeImage = (this.activeImage + 1) % this.images.length },
            prev() { this.activeImage = (this.activeImage - 1 + this.images.length) % this.images.length }
        }"
        @keydown.escape.window="lightboxOpen = false"
        @keydown.arrow-right.window="if(lightboxOpen) next()"
        @keydown.arrow-left.window="if(lightboxOpen) prev()">

        {{-- Grid --}}
        <div class="grid grid-cols-4 grid-rows-2 gap-2 h-[420px] rounded-2xl overflow-hidden">
            {{-- Main large image --}}
            <div class="col-span-2 row-span-2 relative group cursor-pointer"
                @click="lightboxOpen = true; activeImage = 0">
                <img src="{{ $allImages->first() }}" class="w-full h-full object-cover transition duration-300 group-hover:brightness-90">
                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                    <span class="bg-white/90 px-4 py-2 rounded-full text-sm font-bold shadow">🔍 View Photos</span>
                </div>
            </div>

            {{-- Secondary images --}}
            @foreach($allImages->skip(1)->take(4) as $index => $img)
            <div class="relative group cursor-pointer"
                @click="lightboxOpen = true; activeImage = {{ $index + 1 }}">
                <img src="{{ $img }}" class="w-full h-full object-cover transition duration-300 group-hover:brightness-90">
                @if($loop->last && $allImages->count() > 5)
                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center text-white font-bold text-xl">
                        +{{ $allImages->count() - 5 }}
                    </div>
                @endif
            </div>
            @endforeach

            {{-- Empty placeholders if less than 5 images --}}
            @for($i = $allImages->count() - 1; $i < 4; $i++)
                <div class="bg-rose-50 flex items-center justify-center text-4xl text-rose-200">🎊</div>
            @endfor
        </div>

        {{-- Lightbox --}}
        <div x-show="lightboxOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[999] bg-black/95 flex items-center justify-center"
            style="display:none;">

            <button @click="lightboxOpen = false" class="absolute top-5 right-5 text-white hover:text-gray-300 z-50">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <button @click="prev()" class="absolute left-4 z-50 p-2 bg-black/50 hover:bg-black/80 rounded-full text-white transition">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button @click="next()" class="absolute right-4 z-50 p-2 bg-black/50 hover:bg-black/80 rounded-full text-white transition">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>

            <div class="relative w-full h-full flex items-center justify-center p-8">
                <img :src="images[activeImage]" class="max-w-full max-h-[88vh] object-contain rounded-lg shadow-2xl">
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 bg-black/50 text-white px-4 py-1.5 rounded-full text-sm">
                    <span x-text="activeImage + 1"></span> / <span x-text="images.length"></span>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ========== MAIN CONTENT ========== --}}
    <div class="max-w-6xl mx-auto px-4 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            {{-- LEFT: Details --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Title & badges --}}
                <div>
                    @if($package->is_featured)
                        <span class="bg-rose-100 text-rose-600 text-xs font-bold px-3 py-1 rounded-full inline-block mb-3">⭐ Featured Package</span>
                    @endif
                    <h1 class="text-3xl font-extrabold text-gray-900 mb-2">{{ $package->name }}</h1>
                    @if($package->max_pax)
                        <p class="text-gray-500">👥 Up to <strong>{{ $package->max_pax }}</strong> guests</p>
                    @endif
                </div>

                {{-- Vendor Card --}}
                @if($package->vendor)
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                    @if($package->vendor->logo)
                        <img src="{{ asset('storage/' . $package->vendor->logo) }}" class="w-14 h-14 rounded-full object-cover border border-gray-200">
                    @else
                        <div class="w-14 h-14 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 font-bold text-xl">
                            {{ substr($package->vendor->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Vendor</p>
                        <p class="font-bold text-gray-900 text-lg">{{ $package->vendor->name }}</p>
                        <span class="text-xs bg-rose-50 text-rose-600 px-2 py-0.5 rounded-full font-medium">{{ $package->vendor->category }}</span>
                    </div>
                </div>
                @endif

                {{-- Description --}}
                @if($package->description)
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">About This Package</h2>
                    <div class="text-gray-600 leading-relaxed">{!! nl2br(e($package->description)) !!}</div>
                </div>
                @endif

                {{-- Inclusions --}}
                @if($package->inclusions)
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">What's Included</h2>
                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($package->inclusions as $item)
                            <li class="flex items-start gap-3 text-gray-700">
                                <span class="w-5 h-5 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">✓</span>
                                <span>{{ $item['item'] ?? $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- ========== MAP ========== --}}
                @if($package->latitude && $package->longitude)
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">Location</h2>
                    @if($package->address)
                        <p class="text-gray-500 text-sm mb-3">📍 {{ $package->address }}</p>
                    @endif
                    <div id="package-map" class="rounded-xl border h-72 w-full z-0 shadow"></div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            var map = L.map('package-map').setView([{{ $package->latitude }}, {{ $package->longitude }}], 15);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; OpenStreetMap contributors'
                            }).addTo(map);
                            L.marker([{{ $package->latitude }}, {{ $package->longitude }}])
                                .addTo(map)
                                .bindPopup("<b>{{ addslashes($package->name) }}</b>@if($package->address)<br>{{ addslashes($package->address) }}@endif")
                                .openPopup();
                        });
                    </script>
                </div>
                @endif

            </div>

            {{-- RIGHT: Sticky Booking Card --}}
            <div class="lg:col-span-1">
                <div class="sticky top-6 bg-white border border-gray-200 rounded-2xl shadow-lg p-6 space-y-4">

                    {{-- Price --}}
                    <div class="bg-rose-50 border border-rose-100 rounded-xl px-5 py-4">
                        <p class="text-sm text-rose-500 font-semibold mb-1">Starting from</p>
                        @if($package->original_price && $package->original_price > $package->price)
                            <div class="flex items-center gap-2 mb-1">
                                <span class="bg-rose-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                                    Hemat {{ number_format($package->discount_amount, 0, ',', '.') }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-400 line-through">Rp {{ number_format($package->original_price, 0, ',', '.') }}</p>
                        @endif
                        <p class="text-3xl font-extrabold text-rose-700">Rp {{ number_format($package->price, 0, ',', '.') }}</p>
                    </div>

                    {{-- Package info summary --}}
                    @if($package->max_pax)
                    <div class="flex items-center gap-2 text-sm text-gray-600 border-b pb-3">
                        <span>👥</span>
                        <span>Up to <strong>{{ $package->max_pax }}</strong> guests</span>
                    </div>
                    @endif

                    @if($package->vendor)
                    <div class="flex items-center gap-2 text-sm text-gray-600 border-b pb-3">
                        <span>🏢</span>
                        <span>By <strong>{{ $package->vendor->name }}</strong></span>
                    </div>
                    @endif

                    @if($package->address)
                    <div class="flex items-start gap-2 text-sm text-gray-600 border-b pb-3">
                        <span class="mt-0.5">📍</span>
                        <span>{{ $package->address }}</span>
                    </div>
                    @endif

                    {{-- CTA --}}
                    <a href="{{ route('eo.booking.booking-form', ['package_id' => $package->id]) }}"
                       class="block w-full text-center bg-rose-600 hover:bg-rose-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform hover:-translate-y-0.5 text-lg">
                        📅 Book This Package
                    </a>

                    {{-- WhatsApp --}}
                    @php
                        $waPhone = $package->vendor?->phone ?? data_get($eoSettings, 'eo_phone', '');
                        $waPhone = preg_replace('/[^0-9]/', '', $waPhone);
                        if (str_starts_with($waPhone, '0')) $waPhone = '62' . substr($waPhone, 1);
                        $waMsg = "Halo, saya tertarik dengan paket *{$package->name}* - Rp " . number_format($package->price, 0, ',', '.') . "\n\n" . url()->current();
                        $waLink = "https://wa.me/{$waPhone}?text=" . urlencode($waMsg);
                    @endphp

                    @if($waPhone)
                    <a href="{{ $waLink }}" target="_blank"
                       class="block w-full text-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        Chat via WhatsApp
                    </a>
                    @endif

                </div>
            </div>

        </div>
    </div>

    {{-- ========== RELATED PACKAGES ========== --}}
    @if($relatedPackages->count())
    <div class="bg-gray-50 border-t border-gray-100 py-14">
        <div class="max-w-6xl mx-auto px-4">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Other Packages You Might Like</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($relatedPackages as $related)
                @php
                    $relatedThumb = $related->media->first()
                        ? asset('storage/' . $related->media->first()->file_path)
                        : ($related->thumbnail ? asset('storage/' . $related->thumbnail) : null);
                @endphp
                <a href="{{ route('eo.package.show', $related->slug) }}"
                   class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="h-48 overflow-hidden">
                        @if($relatedThumb)
                            <img src="{{ $relatedThumb }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full bg-rose-50 flex items-center justify-center text-5xl">🎊</div>
                        @endif
                    </div>
                    <div class="p-5">
                        @if($related->is_featured)
                            <span class="text-[10px] bg-rose-100 text-rose-600 font-bold px-2 py-0.5 rounded-full">⭐ Featured</span>
                        @endif
                        <h3 class="font-bold text-gray-900 mt-2 mb-1 group-hover:text-rose-600 transition">{{ Str::limit($related->name, 50) }}</h3>
                        @if($related->max_pax)
                            <p class="text-xs text-gray-400 mb-3">👥 Up to {{ $related->max_pax }} guests</p>
                        @endif
                        <p class="text-rose-700 font-extrabold">Rp {{ number_format($related->price, 0, ',', '.') }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>

</x-eo-layout>