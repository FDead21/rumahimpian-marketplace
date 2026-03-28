<x-eo-layout>
    @php
        // Helper to cleanly extract YouTube video ID for embedding
        $youtubeEmbedUrl = null;
        if($vendor->youtube_url) {
            preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $vendor->youtube_url, $matches);
            if(isset($matches[2])) {
                $youtubeEmbedUrl = 'https://www.youtube.com/embed/' . $matches[2];
            }
        }
    @endphp

    {{-- HEADER / HERO SECTION --}}
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 pt-20 pb-32 relative">
        <div class="absolute inset-0 bg-rose-500/10 mix-blend-overlay"></div>
        <div class="max-w-7xl mx-auto px-4 relative z-10 flex flex-col md:flex-row items-center md:items-end gap-6">
            
            {{-- Vendor Logo --}}
            <div class="w-40 h-40 bg-white rounded-3xl p-2 shadow-2xl flex-shrink-0 -mb-16 relative z-20 overflow-hidden border-4 border-slate-900">
                @if($vendor->logo)
                    <img src="{{ asset('storage/' . $vendor->logo) }}" alt="{{ $vendor->name }}" class="w-full h-full object-cover rounded-2xl">
                @else
                    <div class="w-full h-full bg-rose-100 rounded-2xl flex items-center justify-center text-6xl">🏪</div>
                @endif
            </div>

            {{-- Title & Badges --}}
            <div class="text-center md:text-left flex-1 pb-2">
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-3">
                    <span class="bg-rose-500 text-white px-3 py-1 rounded-full text-sm font-bold tracking-wide uppercase">
                        {{ $vendor->category }}
                    </span>
                    @if($vendor->city)
                        <span class="bg-white/20 text-white backdrop-blur-sm px-3 py-1 rounded-full text-sm flex items-center gap-1">
                            📍 {{ $vendor->city }}
                        </span>
                    @endif
                </div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-2">{{ $vendor->name }}</h1>
                <p class="text-gray-300 text-lg max-w-2xl">{{ $vendor->description }}</p>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="max-w-7xl mx-auto px-4 pt-24 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            {{-- LEFT COLUMN: Details --}}
            <div class="lg:col-span-2 space-y-12">
                
                {{-- Features & Tags --}}
                @if(is_array($vendor->features) && count($vendor->features) > 0)
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Highlights & Features</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($vendor->features as $feature)
                            <span class="bg-rose-50 text-rose-700 border border-rose-100 px-4 py-2 rounded-xl font-medium text-sm">
                                ✨ {{ $feature }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Detailed Description (Rich Text) --}}
                @if($vendor->detailed_description)
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">About {{ $vendor->name }}</h2>
                    {{-- Custom prose styling to handle Filament's Rich Editor output --}}
                    <div class="text-gray-600 leading-relaxed space-y-4 [&>h2]:text-xl [&>h2]:font-bold [&>h2]:text-gray-900 [&>h3]:text-lg [&>h3]:font-bold [&>ul]:list-disc [&>ul]:pl-5 [&>ol]:list-decimal [&>ol]:pl-5 [&>a]:text-rose-600 [&>a]:underline">
                        {!! $vendor->detailed_description !!}
                    </div>
                </div>
                @endif

                {{-- Promo Video --}}
                @if($youtubeEmbedUrl)
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Promo Video</h2>
                    <div class="relative w-full overflow-hidden rounded-3xl shadow-lg" style="padding-top: 56.25%;">
                        <iframe class="absolute top-0 left-0 w-full h-full" src="{{ $youtubeEmbedUrl }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
                @endif

                {{-- Service Menu / Catalog --}}
                @if(is_array($vendor->service_menu) && count($vendor->service_menu) > 0)
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Service Menu & Pricing</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($vendor->service_menu as $item)
                        <div class="bg-white rounded-2xl border border-gray-100 p-4 flex gap-4 hover:shadow-md transition">
                            @if(isset($item['image']) && $item['image'])
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['item_name'] }}" class="w-24 h-24 object-cover rounded-xl flex-shrink-0">
                            @else
                                <div class="w-24 h-24 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 border border-dashed border-gray-200 flex-shrink-0">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            <div class="flex flex-col justify-center">
                                <h4 class="font-bold text-gray-900">{{ $item['item_name'] ?? 'Service Item' }}</h4>
                                <p class="text-xs text-gray-500 mb-2 line-clamp-2">{{ $item['description'] ?? '' }}</p>
                                <span class="font-bold text-rose-600">Rp {{ number_format((float)($item['price'] ?? 0), 0, ',', '.') }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Portfolio Gallery (Upgraded to Lightbox) --}}
                @if($vendor->media->count() > 0)
                <div x-data="{
                    lightboxOpen: false,
                    activeImg: '',
                    openLightbox(img) { this.activeImg = img; this.lightboxOpen = true; document.body.style.overflow = 'hidden'; },
                    closeLightbox() { this.lightboxOpen = false; document.body.style.overflow = ''; }
                }">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Portfolio Gallery</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($vendor->media as $photo)
                        <button type="button" @click="openLightbox('{{ asset('storage/' . $photo->file_path) }}')" 
                                class="group block relative h-48 w-full rounded-2xl overflow-hidden cursor-zoom-in text-left focus:outline-none focus:ring-4 focus:ring-rose-500/50">
                            <img src="{{ asset('storage/' . $photo->file_path) }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition duration-300 flex items-center justify-center">
                                <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                            </div>
                        </button>
                        @endforeach
                    </div>

                    {{-- The Pop-up Image Viewer --}}
                    <div x-show="lightboxOpen" style="display: none;" 
                         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 p-4 backdrop-blur-sm" 
                         @click.self="closeLightbox()" @keydown.escape.window="closeLightbox()">
                        
                        <button type="button" @click="closeLightbox()" class="absolute top-6 right-6 text-white hover:text-rose-400 transition p-2">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        
                        <img :src="activeImg" class="max-w-full max-h-[90vh] object-contain rounded-xl shadow-2xl"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-90"
                             x-transition:enter-end="opacity-100 scale-100">
                    </div>
                </div>
                @endif

            </div>

            {{-- RIGHT COLUMN: Sticky Sidebar --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24 bg-white rounded-3xl p-6 border border-gray-100 shadow-xl">
                    
                    {{-- Price Range Display --}}
                    @if($vendor->price_from || $vendor->price_to)
                        <div class="mb-6 pb-6 border-b border-gray-100">
                            <p class="text-sm text-gray-500 mb-1">Estimated Price Range</p>
                            <div class="text-xl font-extrabold text-gray-900">
                                @if($vendor->price_from && $vendor->price_to)
                                    Rp {{ number_format($vendor->price_from, 0, ',', '.') }} <span class="text-gray-400 font-normal text-base mx-1">to</span> Rp {{ number_format($vendor->price_to, 0, ',', '.') }}
                                @elseif($vendor->price_from)
                                    <span class="text-gray-400 font-normal text-base mr-1">Starts from</span> Rp {{ number_format($vendor->price_from, 0, ',', '.') }}
                                @endif
                            </div>
                        </div>
                    @endif

                    <h3 class="font-bold text-gray-900 mb-4">Contact & Location</h3>
                    
                    <ul class="space-y-4 mb-8">
                        @if($vendor->address)
                        <li class="flex gap-3 text-sm text-gray-600">
                            <span class="text-rose-500 mt-0.5">📍</span>
                            <span>{{ $vendor->address }}</span>
                        </li>
                        @endif
                        @if($vendor->email)
                        <li class="flex gap-3 text-sm text-gray-600">
                            <span class="text-rose-500 mt-0.5">✉️</span>
                            <a href="mailto:{{ $vendor->email }}" class="hover:text-rose-600">{{ $vendor->email }}</a>
                        </li>
                        @endif
                        @if($vendor->phone)
                        <li class="flex gap-3 text-sm text-gray-600">
                            <span class="text-rose-500 mt-0.5">📞</span>
                            <a href="tel:{{ $vendor->phone }}" class="hover:text-rose-600">{{ $vendor->phone }}</a>
                        </li>
                        @endif
                    </ul>

                    {{-- Social Links --}}
                    @if($vendor->instagram_url || $vendor->website_url)
                    <div class="flex gap-3 mb-8">
                        @if($vendor->instagram_url)
                        <a href="{{ $vendor->instagram_url }}" target="_blank" class="flex-1 bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-500 text-white text-center py-2.5 rounded-xl font-semibold text-sm hover:opacity-90 transition">
                            Instagram
                        </a>
                        @endif
                        @if($vendor->website_url)
                        <a href="{{ $vendor->website_url }}" target="_blank" class="flex-1 bg-gray-900 text-white text-center py-2.5 rounded-xl font-semibold text-sm hover:bg-gray-800 transition">
                            Website
                        </a>
                        @endif
                    </div>
                    @endif

                    {{-- Primary Action --}}
                    @if($vendor->phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $vendor->phone) }}" target="_blank" class="block w-full bg-green-500 hover:bg-green-600 text-white text-center font-bold py-4 rounded-xl shadow-lg shadow-green-500/30 transition transform hover:-translate-y-1 mb-3">
                        💬 Chat on WhatsApp
                    </a>
                    @endif
                    
                    <a href="{{ route('eventOrganizer.booking.create') }}" class="block w-full bg-rose-50 hover:bg-rose-100 text-rose-600 text-center font-bold py-4 rounded-xl transition">
                        📅 Plan Event with Vendor
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-eo-layout>