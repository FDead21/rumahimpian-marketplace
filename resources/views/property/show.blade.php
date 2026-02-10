<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $property->title }} | RumahImpian</title>
    
    <meta name="description" content="For {{ $property->listing_type }}: {{ $property->title }} in {{ $property->city }}. Price: Rp {{ number_format($property->price, 0, ',', '.') }}">

    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $property->title }} - Rp {{ number_format($property->price, 0, ',', '.') }}" />
    <meta property="og:description" content="📍 {{ $property->district }}, {{ $property->city }} | 🛏 {{ $property->bedrooms }} Bed | 🚿 {{ $property->bathrooms }} Bath. Click to see photos and virtual tour!" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:site_name" content="RumahImpian Indonesia" />
    
    @if($property->media->first())
        <meta property="og:image" content="{{ asset('storage/' . $property->media->first()->file_path) }}" />
        
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
    @endif

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $property->title }}">
    <meta name="twitter:description" content="Check out this property in {{ $property->city }}!">
    @if($property->media->first())
        <meta name="twitter:image" content="{{ asset('storage/' . $property->media->first()->file_path) }}">
    @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    @include('components.navbar') 

    <div class="max-w-7xl mx-auto px-4 py-8">
        
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $property->title }}</h1>
            <p class="text-gray-500 mt-1">📍 {{ $property->address }}, {{ $property->district }}, {{ $property->city }}</p>
        </div>

        @php
            $galleryImages = $property->media->where('file_type', '!=', 'VIRTUAL_TOUR_360')->values();
        @endphp

        <div x-data="{ 
            lightboxOpen: false, 
            activeImage: 0,
            images: {{ Js::from($property->media->map(fn($m) => asset('storage/' . $m->file_path))) }},
            next() { this.activeImage = (this.activeImage + 1) % this.images.length },
            prev() { this.activeImage = (this.activeImage - 1 + this.images.length) % this.images.length }
        }" 
        @keydown.escape.window="lightboxOpen = false"
        @keydown.arrow-right.window="next()"
        @keydown.arrow-left.window="prev()">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-2 h-[400px] mb-8 rounded-xl overflow-hidden">
            
            <div class="md:col-span-2 md:row-span-2 h-full relative group cursor-pointer" 
                @click="lightboxOpen = true; activeImage = 0">
                @if(isset($galleryImages[0]))
                    <img src="{{ asset('storage/' . $galleryImages[0]->file_path) }}" 
                        class="w-full h-full object-cover hover:opacity-95 transition duration-300">
                    
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <span class="bg-white/90 px-4 py-2 rounded-full text-sm font-bold shadow-lg">🔍 {{ __('View Photos') }}</span>
                    </div>
                @else
                    <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">{{ __('No Image') }}</div>
                @endif
            </div>

            @foreach($galleryImages->skip(1)->take(4) as $index => $image)
                <div class="hidden md:block h-full relative group cursor-pointer" 
                    @click="lightboxOpen = true; activeImage = {{ $loop->iteration }}"> 
                    <img src="{{ asset('storage/' . $image->file_path) }}" 
                        class="w-full h-full object-cover hover:opacity-95 transition duration-300">
                    
                    {{-- Counter Logic --}}
                    @if($loop->last && $galleryImages->count() > 5)
                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center text-white font-bold text-xl">
                            +{{ $galleryImages->count() - 5 }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div x-show="lightboxOpen" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[999] bg-black/95 flex items-center justify-center backdrop-blur-sm"
            style="display: none;"> 
            
            <button @click="lightboxOpen = false" class="absolute top-6 right-6 text-white hover:text-gray-300 z-50">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <button @click="prev()" class="absolute left-4 text-white hover:text-gray-300 z-50 p-2 bg-black/50 rounded-full hover:bg-black/80 transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>

            <button @click="next()" class="absolute right-4 text-white hover:text-gray-300 z-50 p-2 bg-black/50 rounded-full hover:bg-black/80 transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>

            <div class="relative w-full h-full flex items-center justify-center p-4" @click.outside="lightboxOpen = false">
                <img :src="images[activeImage]" 
                    class="max-w-full max-h-[90vh] object-contain shadow-2xl rounded-lg"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100">
                
                <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white bg-black/50 px-4 py-2 rounded-full text-sm font-medium">
                    <span x-text="activeImage + 1"></span> / <span x-text="images.length"></span>
                </div>
            </div>
        </div>

    </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            
            <div class="md:col-span-2 space-y-8">
                
                <div class="flex space-x-8 border-b pb-6">
                    <div>
                        <span class="block text-gray-500 text-sm">{{ __('Price') }}</span>
                        <span class="text-2xl font-bold text-indigo-600">
                            Rp {{ number_format($property->price, 0, ',', '.') }}
                        </span>
                    </div>
                    @if($property->bedrooms)
                    <div>
                        <span class="block text-gray-500 text-sm">{{ __('Bedrooms') }}</span>
                        <span class="font-semibold text-lg">🛏 {{ $property->bedrooms }}</span>
                    </div>
                    @endif
                    @if($property->bathrooms)
                    <div>
                        <span class="block text-gray-500 text-sm">{{ __('Bathrooms') }}</span>
                        <span class="font-semibold text-lg">🚿 {{ $property->bathrooms }}</span>
                    </div>
                    @endif
                    <div>
                        <span class="block text-gray-500 text-sm">{{ __('Building Size') }}</span>
                        <span class="font-semibold text-lg">🏠 {{ $property->building_area }} m²</span>
                    </div>
                </div>

                @if($property->youtube_url)
                    <div class="mt-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Video Tour</h2>
                        
                        <div class="relative w-full overflow-hidden rounded-xl shadow-lg aspect-video">
                            @php
                                $video_id = '';
                                $url = $property->youtube_url;
                                
                                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
                                    $video_id = $matches[1];
                                }
                            @endphp

                            @if($video_id)
                                <iframe 
                                    class="absolute top-0 left-0 w-full h-full"
                                    src="https://www.youtube.com/embed/{{ $video_id }}?rel=0" 
                                    title="YouTube video player" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                                </iframe>
                            @else
                                <div class="flex items-center justify-center h-full bg-gray-100">
                                    <a href="{{ $property->youtube_url }}" target="_blank" class="flex items-center gap-2 text-red-600 font-bold hover:underline">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                                        Watch on YouTube
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div>
                    <h2 class="text-xl font-bold mb-3">{{ __('Description') }}</h2>
                    <div class="prose max-w-none text-gray-600 leading-relaxed">
                        {!! nl2br(e($property->description)) !!}
                    </div>
                </div>

                <div class="mt-8 border-t pt-8">
                    <h2 class="text-xl font-bold mb-4">{{ __('Location') }}</h2>
                    <div id="map" class="rounded-xl shadow-lg border h-96 w-full z-0"></div>
                </div>

                <script>
                    // Initialize Map
                    var map = L.map('map').setView([{{ $property->latitude ?? -6.2088 }}, {{ $property->longitude ?? 106.8456 }}], 15);

                    // Add OpenStreetMap Tiles
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    // Add a Pin
                    @if($property->latitude && $property->longitude)
                        L.marker([{{ $property->latitude }}, {{ $property->longitude }}])
                            .addTo(map)
                            .bindPopup("<b>{{ $property->title }}</b><br>{{ $property->address }}")
                            .openPopup();
                    @endif
                </script>

                @if($property->specifications)
                <div>
                    <h2 class="text-xl font-bold mb-3">{{ __('Property Details') }}</h2>
                    <div class="grid grid-cols-2 gap-4 bg-white p-6 rounded-lg border">
                        @foreach($property->specifications as $key => $value)
                            <div class="flex justify-between border-b border-gray-100 pb-2">
                                <span class="font-medium text-gray-600 capitalize">{{ __(ucwords(str_replace('_', ' ', $key))) }}</span>
                                <span class="font-semibold text-gray-900">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <div class="bg-gray-900 text-white p-8 rounded-lg text-center">
                    <h3 class="text-xl font-bold mb-2">📷 {{ __('360° Virtual Tour') }}</h3>
                    <p class="text-gray-400 mb-4">{{ __('Experience this property virtually from anywhere.') }}</p>
                    @php
                        $has360 = $property->media->where('file_type', 'VIRTUAL_TOUR_360')->count() > 0;
                    @endphp

                    @if($has360)
                        <a href="{{ route('property.tour', ['id' => $property->id, 'slug' => $property->slug]) }}" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-full font-bold transition inline-block">
                            🚀 {{ __('Launch Virtual Tour') }}
                        </a>
                    @else
                        <button disabled class="bg-gray-700 text-gray-400 px-6 py-2 rounded-full font-bold cursor-not-allowed">
                            {{ __('No Virtual Tour Available') }}
                        </button>
                    @endif
                </div>

            </div>

            <div class="md:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-lg border sticky top-4">
                    <div class="flex items-center space-x-4 mb-6">
                        @if($property->user->avatar_url)
                            <img src="{{ asset('storage/' . $property->user->avatar_url) }}" 
                                class="w-24 h-24 rounded-full object-cover border border-indigo-200"
                                alt="Avatar">
                        @else
                            <div class="w-24 h-24 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold border border-indigo-200">
                                {{ substr($property->user->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500">{{ __('Listed by') }}</p>
                            <a href="{{ route('agent.show', $property->user->id) }}" class="hover:underline">
                                <div class="flex items-center gap-1">
                                    <div class="font-bold text-gray-900 text-lg">{{ $property->user->name }}</div>
                                    @if($property->user->is_verified)
                                        {{-- Blue Tick Icon --}}
                                        <svg class="w-5 h-5 text-blue-500 fill-current" viewBox="0 0 24 24">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15l-4-4 1.41-1.41L11 14.17l6.59-6.59L19 9l-8 8z" fill="currentColor" stroke="none"/>
                                        </svg>
                                        {{-- Optional Tooltip --}}
                                        <span class="text-[10px] bg-blue-100 text-blue-700 px-1 rounded border border-blue-200">{{ __('Verified') }}</span>
                                    @endif
                                </div>
                            </a>
                        </div>
                    </div>

                    @php
                        $phone = $property->user->phone_number;
                        $phone = preg_replace('/[^0-9]/', '', $phone);
                        if(str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        }

                        $message = "Halo, saya tertarik dengan properti Anda di RumahImpian:\n\n";
                        $message .= "*" . $property->title . "*\n"; // Asterisks make text BOLD in WhatsApp
                        $message .= "Lokasi: " . $property->city . "\n";
                        $message .= "Harga: Rp " . number_format($property->price, 0, ',', '.') . "\n\n";
                        $message .= "Bisa minta info lebih lanjut? Terima kasih.\n";
                        $message .= "Link: " . url()->current(); // <--- CRITICAL: Sends the exact URL

                        $wa_link = "https://wa.me/{$phone}?text=" . urlencode($message);
                    @endphp

                    <a href="{{ $wa_link }}" target="_blank" class="block w-full bg-green-500 hover:bg-green-600 text-white text-center font-bold py-3 rounded-lg mb-3 transition flex items-center justify-center gap-2">
                        <span>{{ __('WhatsApp Agent') }}</span>
                    </a>
                    
                    <div x-data="{ 
                            viewingOpen: false,
                            date: '',
                            time: '',
                            phone: '{{ $phone }}',
                            title: '{{ $property->title }}',
                            url: '{{ url()->current() }}',
                            
                            get whatsappLink() {
                                let msg = 'Halo, saya ingin menjadwalkan viewing properti:\n\n';
                                msg += '*' + this.title + '*\n';
                                msg += '📅 Tanggal: ' + (this.date || 'Belum ditentukan') + '\n';
                                msg += '⏰ Jam: ' + (this.time || 'Belum ditentukan') + '\n';
                                msg += 'Link: ' + this.url;
                                
                                return 'https://wa.me/' + this.phone + '?text=' + encodeURIComponent(msg);
                            }
                        }">

                        <button @click="viewingOpen = true" 
                                class="w-full bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-3 rounded-lg transition flex items-center justify-center gap-2">
                            📅 {{ __('Schedule Viewing') }}
                        </button>

                        <div x-show="viewingOpen" 
                            style="display: none;"
                            class="fixed inset-0 z-[9999] flex items-center justify-center px-4"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0">
                            
                            <div class="absolute inset-0 bg-gray-900/70 backdrop-blur-sm" @click="viewingOpen = false"></div>

                            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md p-6 overflow-hidden transform transition-all"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-4">
                                
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-xl font-bold text-gray-900">{{ __('Schedule a Visit') }}</h3>
                                    <button @click="viewingOpen = false" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">{{ __('Select Date') }}</label>
                                        <input type="date" x-model="date" 
                                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none"
                                            min="{{ date('Y-m-d') }}">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">{{ __('Select Time') }}</label>
                                        <select x-model="time" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                                            <option value="">-- {{ __('Choose Time') }} --</option>
                                            <option value="09:00 AM">09:00 AM</option>
                                            <option value="10:00 AM">10:00 AM</option>
                                            <option value="11:00 AM">11:00 AM</option>
                                            <option value="01:00 PM">01:00 PM</option>
                                            <option value="02:00 PM">02:00 PM</option>
                                            <option value="03:00 PM">03:00 PM</option>
                                            <option value="04:00 PM">04:00 PM</option>
                                        </select>
                                    </div>
                                    
                                    <div class="bg-blue-50 text-blue-800 text-xs p-3 rounded-lg flex gap-2 items-start">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p>{{ __('The agent will confirm availability via WhatsApp after you send this request.') }}</p>
                                    </div>
                                </div>

                                <div class="mt-8 pt-4 border-t border-gray-100 flex gap-3">
                                    <button @click="viewingOpen = false" class="w-1/3 px-4 py-3 text-gray-700 font-bold hover:bg-gray-100 rounded-lg transition">
                                        {{ __('Cancel') }}
                                    </button>
                                    <a :href="whatsappLink" target="_blank" @click="viewingOpen = false"
                                    class="w-2/3 bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-lg transition flex items-center justify-center gap-2 shadow-lg hover:shadow-green-500/30">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        {{ __('Confirm & Chat') }}
                                    </a>
                                </div>

                            </div>
                        </div>

                    </div>
                    <a href="{{ route('property.pdf', ['id' => $property->id, 'slug' => $property->slug]) }}" 
                    target="_blank"
                    class="block w-full border border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-bold py-3 rounded-lg transition text-center mt-3">
                        📄 {{ __('Download Brochure (PDF)') }}
                    </a>

                    <div class="mt-6 border-t pt-4">
                        <h4 class="font-bold text-gray-900 mb-2">{{ __('Send a Message') }}</h4>
                        
                        @if(session('success'))
                            <div class="bg-green-100 text-green-700 p-2 rounded mb-2 text-sm text-center">
                                ✅ {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('inquiry.send', $property->id) }}" method="POST" class="space-y-2">
                            @csrf
                            <input type="text" name="name" placeholder="{{ __('Your Name') }}" required class="w-full border rounded px-3 py-2 text-sm">
                            <input type="text" name="phone" placeholder="{{ __('Your Phone (WA)') }}" required class="w-full border rounded px-3 py-2 text-sm">
                            <textarea name="message" rows="2" placeholder="{{ __('I am interested...') }}" class="w-full border rounded px-3 py-2 text-sm"></textarea>
                            
                            <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold py-2 rounded text-sm transition">
                                {{ __('Send Inquiry') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border mt-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900 text-lg">{{ __('KPR Simulation') }}</h3>
                    <span class="text-xs bg-indigo-50 text-indigo-600 px-2 py-1 rounded font-bold">{{ __('Smart Calc') }}</span>
                </div>
                
                <div class="space-y-4" x-data="kprCalculator()">
                    
                    {{-- Bank Selector --}}
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">{{ __('Select Bank Program') }}</label>
                        <select x-model="selectedBankId" @change="updateBank()" class="w-full border border-gray-300 rounded-lg py-2 px-3 mt-1 outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach($banks as $bank)
                                <option value="{{ $bank->id }}">
                                    {{ $bank->name }} (Floating: {{ $bank->floating_rate }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Rate Info Display --}}
                    <div class="grid grid-cols-3 gap-2 text-center text-[10px] text-gray-500 bg-gray-50 p-2 rounded-lg" x-show="currentBank">
                        <div>
                            <span class="block font-bold text-gray-900" x-text="(currentBank.fixed_rate_1y || '-') + '%'"></span>
                            <span>Fix 1Yr</span>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-900" x-text="(currentBank.fixed_rate_3y || '-') + '%'"></span>
                            <span>Fix 3Yr</span>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-900" x-text="(currentBank.fixed_rate_5y || '-') + '%'"></span>
                            <span>Fix 5Yr</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">{{ __('Down Payment (%)') }}</label>
                            <input type="number" x-model="dp_percent" class="w-full border border-gray-300 rounded-lg mt-1 py-2 px-3 focus:ring-indigo-500 outline-none">
                            <p class="text-[10px] text-red-500 mt-1" x-show="dp_percent < (currentBank.min_dp_percent || 0)">
                                Min DP is <span x-text="currentBank.min_dp_percent"></span>%
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">{{ __('Tenor (Years)') }}</label>
                            <select x-model="tenor" class="w-full border border-gray-300 rounded-lg mt-1 py-2 px-3 outline-none">
                                <template x-for="y in (currentBank.max_tenor || 20)">
                                    <option :value="y" x-text="y + ' Years'"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="bg-indigo-50 p-4 rounded-lg text-center mt-4 border border-indigo-100">
                        <p class="text-xs text-indigo-600 font-bold uppercase">{{ __('Estimated Monthly') }}</p>
                        <p class="text-2xl font-extrabold text-indigo-700 mt-1" x-text="calculate()">Rp 0</p>
                        <p class="text-[10px] text-gray-500 mt-2">
                            *Using <span x-text="effectiveRate"></span>% Effective Rate (Avg of Fixed & Floating)
                        </p>
                    </div>
                </div>
            </div>

            <script>
            function kprCalculator() {
                return {
                    price: {{ $property->price }},
                    dp_percent: 20,
                    tenor: 15,
                    banks: {{ Js::from($banks) }},
                    selectedBankId: {{ $banks->first()->id ?? 'null' }},
                    currentBank: {},
                    effectiveRate: 0,

                    init() {
                        this.updateBank();
                    },

                    updateBank() {
                        this.currentBank = this.banks.find(b => b.id == this.selectedBankId) || {};
                        if(this.currentBank.min_dp_percent) this.dp_percent = Math.max(this.dp_percent, this.currentBank.min_dp_percent);
                    },

                    calculate() {
                        if (!this.currentBank.id) return 'Rp 0';

                        let dpAmount = this.price * (this.dp_percent / 100);
                        let loanAmount = this.price - dpAmount;
                        if (loanAmount <= 0) return 'Rp 0';

                        // SMART LOGIC: Determine applicable rate
                        // If tenor <= 1, use Fix 1. If <= 3, use Fix 3. Else use Floating or Weighted.
                        let rate = this.currentBank.floating_rate; 
                        
                        // Simple logic: If we have a fixed rate that matches the tenor, use it. 
                        // Otherwise, assume a blend (simplified for UI).
                        if (this.tenor <= 1 && this.currentBank.fixed_rate_1y) rate = this.currentBank.fixed_rate_1y;
                        else if (this.tenor <= 3 && this.currentBank.fixed_rate_3y) rate = this.currentBank.fixed_rate_3y;
                        else if (this.tenor <= 5 && this.currentBank.fixed_rate_5y) rate = this.currentBank.fixed_rate_5y;
                        
                        this.effectiveRate = rate;

                        let monthlyRate = (rate / 100) / 12;
                        let months = this.tenor * 12;
                        let x = Math.pow(1 + monthlyRate, months);
                        let monthly = (loanAmount * x * monthlyRate) / (x - 1);

                        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(monthly);
                    }
                }
            }
            </script>

        </div>
    </div>
    @if($relatedProperties->count() > 0)
    <div class="bg-gray-50 border-t border-gray-200 py-16">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <h3 class="text-2xl font-bold text-gray-900 mb-8">{{ __('You might also like') }}</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($relatedProperties as $related)
                    
                    <div class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 ease-in-out relative">
                        
                        <div class="relative h-64 overflow-hidden">
                            <a href="{{ route('property.show', ['id' => $related->id, 'slug' => $related->slug]) }}">
                                @if($related->media->first())
                                    <img src="{{ asset('storage/' . $related->media->first()->file_path) }}" 
                                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                @else
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                        No Image
                                    </div>
                                @endif
                            </a>

                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm
                                    {{ $related->listing_type == 'SALE' ? 'bg-indigo-600 text-white' : 'bg-emerald-500 text-white' }}">
                                    {{ $related->listing_type }}
                                </span>
                            </div>

                            <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-lg shadow-sm border border-white/50">
                                <span class="text-indigo-700 font-extrabold text-lg">
                                    @currency($related->price)
                                </span>
                            </div>
                        </div>

                        <div class="p-5">
                            <div class="flex items-center text-xs text-gray-500 mb-2">
                                📍 {{ $related->city }}, {{ $related->district }}
                            </div>

                            <h3 class="font-bold text-gray-900 text-lg leading-tight mb-4 group-hover:text-indigo-600 transition-colors">
                                <a href="{{ route('property.show', ['id' => $related->id, 'slug' => $related->slug]) }}">
                                    {{ Str::limit($related->title, 45) }}
                                </a>
                            </h3>

                            <div class="flex items-center justify-between border-t border-gray-100 pt-4 text-sm text-gray-600">
                                <div class="flex items-center gap-1">
                                    <span class="font-semibold">{{ $related->bedrooms }}</span> <span class="text-xs">Bed</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="font-semibold">{{ $related->bathrooms }}</span> <span class="text-xs">Bath</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="font-semibold">{{ $related->building_area }}</span> <span class="text-xs">m²</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
            </div>

        </div> </div>
    @endif

@include('components.footer')
</body>
</html>