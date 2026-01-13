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
                @if(isset($property->media[0]))
                    <img src="{{ asset('storage/' . $property->media[0]->file_path) }}" 
                        class="w-full h-full object-cover hover:opacity-95 transition duration-300">
                    
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <span class="bg-white/90 px-4 py-2 rounded-full text-sm font-bold shadow-lg">🔍 View Photos</span>
                    </div>
                @else
                    <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">No Image</div>
                @endif
            </div>

            @foreach($property->media->skip(1)->take(4) as $index => $image)
                <div class="hidden md:block h-full relative group cursor-pointer" 
                    @click="lightboxOpen = true; activeImage = {{ $loop->index + 1 }}"> <img src="{{ asset('storage/' . $image->file_path) }}" 
                        class="w-full h-full object-cover hover:opacity-95 transition duration-300">
                    
                    @if($loop->last && $property->media->count() > 5)
                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center text-white font-bold text-xl">
                            +{{ $property->media->count() - 5 }}
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
            style="display: none;"> <button @click="lightboxOpen = false" class="absolute top-6 right-6 text-white hover:text-gray-300 z-50">
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
                        <span class="block text-gray-500 text-sm">Price</span>
                        <span class="text-2xl font-bold text-indigo-600">
                            Rp {{ number_format($property->price, 0, ',', '.') }}
                        </span>
                    </div>
                    @if($property->bedrooms)
                    <div>
                        <span class="block text-gray-500 text-sm">Bedrooms</span>
                        <span class="font-semibold text-lg">🛏 {{ $property->bedrooms }}</span>
                    </div>
                    @endif
                    @if($property->bathrooms)
                    <div>
                        <span class="block text-gray-500 text-sm">Bathrooms</span>
                        <span class="font-semibold text-lg">🚿 {{ $property->bathrooms }}</span>
                    </div>
                    @endif
                    <div>
                        <span class="block text-gray-500 text-sm">Building Size</span>
                        <span class="font-semibold text-lg">🏠 {{ $property->building_area }} m²</span>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-bold mb-3">Description</h2>
                    <div class="prose max-w-none text-gray-600 leading-relaxed">
                        {!! nl2br(e($property->description)) !!}
                    </div>
                </div>

                <div class="mt-8 border-t pt-8">
                    <h2 class="text-xl font-bold mb-4">Location</h2>
                    <div id="map" class="rounded-xl shadow-lg border"></div>
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
                    <h2 class="text-xl font-bold mb-3">Property Details</h2>
                    <div class="grid grid-cols-2 gap-4 bg-white p-6 rounded-lg border">
                        @foreach($property->specifications as $key => $value)
                            <div class="flex justify-between border-b border-gray-100 pb-2">
                                <span class="font-medium text-gray-600 capitalize">{{ str_replace('_', ' ', $key) }}</span>
                                <span class="font-semibold text-gray-900">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <div class="bg-gray-900 text-white p-8 rounded-lg text-center">
                    <h3 class="text-xl font-bold mb-2">📷 360° Virtual Tour</h3>
                    <p class="text-gray-400 mb-4">Experience this property virtually from anywhere.</p>
                    @php
                        $has360 = $property->media->where('file_type', 'VIRTUAL_TOUR_360')->count() > 0;
                    @endphp

                    @if($has360)
                        <a href="{{ route('property.tour', ['id' => $property->id, 'slug' => $property->slug]) }}" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-full font-bold transition inline-block">
                            🚀 Launch Virtual Tour
                        </a>
                    @else
                        <button disabled class="bg-gray-700 text-gray-400 px-6 py-2 rounded-full font-bold cursor-not-allowed">
                            No Virtual Tour Available
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
                            <p class="text-sm text-gray-500">Listed by</p>
                            <a href="{{ route('agent.show', $property->user->id) }}" class="hover:underline">
                                <h3 class="font-bold text-gray-900">{{ $property->user->name }}</h3>
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
                        <span>WhatsApp Agent</span>
                    </a>
                    
                    <button class="block w-full border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold py-3 rounded-lg transition">
                        Schedule Viewing
                    </button>

                    <a href="{{ route('property.pdf', ['id' => $property->id, 'slug' => $property->slug]) }}" 
                    target="_blank"
                    class="block w-full border border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-bold py-3 rounded-lg transition text-center mt-3">
                        📄 Download Brochure (PDF)
                    </a>

                    <div class="mt-6 border-t pt-4">
                        <h4 class="font-bold text-gray-900 mb-2">Send a Message</h4>
                        
                        @if(session('success'))
                            <div class="bg-green-100 text-green-700 p-2 rounded mb-2 text-sm text-center">
                                ✅ {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('inquiry.send', $property->id) }}" method="POST" class="space-y-2">
                            @csrf
                            <input type="text" name="name" placeholder="Your Name" required class="w-full border rounded px-3 py-2 text-sm">
                            <input type="text" name="phone" placeholder="Your Phone (WA)" required class="w-full border rounded px-3 py-2 text-sm">
                            <textarea name="message" rows="2" placeholder="I am interested..." class="w-full border rounded px-3 py-2 text-sm"></textarea>
                            
                            <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold py-2 rounded text-sm transition">
                                Send Inquiry
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border mt-8">
                <h3 class="font-bold text-gray-900 mb-4 text-lg">Simulasi KPR</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Property Price</label>
                        <div class="flex items-center border rounded mt-1 bg-gray-50">
                            <span class="px-3 text-gray-500 text-sm">Rp</span>
                            <input type="text" value="{{ number_format($property->price, 0, ',', '.') }}" disabled class="w-full py-2 bg-transparent text-gray-700 font-bold outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Down Payment (20%)</label>
                        <div class="flex items-center border rounded mt-1">
                            <span class="px-3 text-gray-500 text-sm">Rp</span>
                            <input type="number" id="dp_amount" class="w-full py-2 outline-none text-gray-900" 
                                value="{{ $property->price * 0.2 }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Interest (%)</label>
                            <input type="number" id="interest_rate" value="8.5" step="0.1" class="w-full border rounded mt-1 py-2 px-3 text-gray-900">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Tenor (Years)</label>
                            <select id="tenor" class="w-full border rounded mt-1 py-2 px-3 text-gray-900 bg-white">
                                <option value="10">10 Years</option>
                                <option value="15" selected>15 Years</option>
                                <option value="20">20 Years</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-indigo-50 p-4 rounded-lg text-center mt-4">
                        <p class="text-xs text-indigo-600 font-bold uppercase">Estimated Monthly Installment</p>
                        <p class="text-2xl font-bold text-indigo-700 mt-1" id="monthly_result">Rp 0</p>
                    </div>
                </div>
            </div>

            <script>
                function calculateKPR() {
                    // Get raw inputs
                    let price = {{ $property->price }};
                    let dp = document.getElementById('dp_amount').value;
                    let rate = document.getElementById('interest_rate').value;
                    let years = document.getElementById('tenor').value;

                    // KPR Formula (Standard Annuity)
                    let principal = price - dp;
                    let monthlyRate = (rate / 100) / 12;
                    let months = years * 12;

                    if (principal <= 0) {
                        document.getElementById('monthly_result').innerText = "Rp 0";
                        return;
                    }

                    // M = P [ i(1 + i)^n ] / [ (1 + i)^n – 1 ]
                    let x = Math.pow(1 + monthlyRate, months);
                    let monthly = (principal * x * monthlyRate) / (x - 1);

                    // Format to Rupiah
                    let formatter = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    });

                    document.getElementById('monthly_result').innerText = formatter.format(monthly);
                }

                // Run on load and whenever inputs change
                document.addEventListener('DOMContentLoaded', calculateKPR);
                document.querySelectorAll('#dp_amount, #interest_rate, #tenor').forEach(item => {
                    item.addEventListener('input', calculateKPR);
                });
            </script>

        </div>
    </div>
    @if($relatedProperties->count() > 0)
    <div class="bg-gray-50 border-t border-gray-200 py-16">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <h3 class="text-2xl font-bold text-gray-900 mb-8">You might also like</h3>

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