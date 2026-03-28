<x-eo-layout>

    <div class="bg-gradient-to-br from-rose-600 to-pink-700 text-white py-16 text-center">
        <h1 class="text-4xl font-extrabold mb-2">Our Vendors</h1>
        <p class="text-rose-200 text-lg">Trusted partners who make your event perfect</p>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-16">

        @forelse($vendors as $category => $categoryVendors)
        <div class="mb-14">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                <span class="bg-rose-100 text-rose-600 px-4 py-1 rounded-full text-sm font-bold">
                    {{ $category }}
                </span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($categoryVendors as $vendor)
                <div class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">

                    {{-- Logo --}}
                    <div class="relative h-40 bg-gradient-to-br from-rose-50 to-pink-100 flex items-center justify-center overflow-hidden">
                        @if($vendor->logo)
                            <img src="{{ asset('storage/' . $vendor->logo) }}"
                                 class="h-24 w-24 object-cover rounded-full border-4 border-white shadow-md">
                        @else
                            <div class="h-24 w-24 rounded-full bg-rose-200 flex items-center justify-center text-4xl border-4 border-white shadow-md">
                                🏪
                            </div>
                        @endif
                    </div>

                    <div class="p-5">
                        <h3 class="font-bold text-lg text-gray-900 mb-1 group-hover:text-rose-600 transition">
                            {{ $vendor->name }}
                        </h3>

                        @if($vendor->description)
                            <p class="text-gray-500 text-sm mb-3 line-clamp-2">{{ $vendor->description }}</p>
                        @endif

                        {{-- Price range --}}
                        @if($vendor->price_from || $vendor->price_to)
                        <div class="flex items-center gap-1 text-sm text-rose-600 font-semibold mb-3">
                            <span>💰</span>
                            @if($vendor->price_from && $vendor->price_to)
                                Rp {{ number_format($vendor->price_from, 0, ',', '.') }} –
                                Rp {{ number_format($vendor->price_to, 0, ',', '.') }}
                            @elseif($vendor->price_from)
                                From Rp {{ number_format($vendor->price_from, 0, ',', '.') }}
                            @endif
                        </div>
                        @endif

                        {{-- Contact --}}
                        @if($vendor->phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $vendor->phone) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 bg-green-50 hover:bg-green-600 text-green-700 hover:text-white text-sm font-bold px-4 py-2 rounded-xl border border-green-200 hover:border-green-600 transition w-full justify-center">
                            💬 WhatsApp
                        </a>
                        @endif

                        {{-- Portfolio photos --}}
                        @if($vendor->media->count() > 0)
                        <div class="grid grid-cols-3 gap-1 mt-3">
                            @foreach($vendor->media->take(3) as $photo)
                            <div class="h-16 rounded-lg overflow-hidden">
                                <img src="{{ asset('storage/' . $photo->file_path) }}"
                                     class="w-full h-full object-cover">
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @empty
            <div class="text-center py-16 text-gray-400">
                <div class="text-5xl mb-4">🏪</div>
                <p class="text-lg font-semibold">No vendors available yet.</p>
            </div>
        @endforelse

    </div>

    {{-- CTA --}}
    <div class="bg-rose-50 border-t border-rose-100 py-12 text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Ready to book your event?</h2>
        <p class="text-gray-500 mb-6">Our vendors will be assigned to your booking automatically</p>
        <a href="{{ route('eventOrganizer.booking.create') }}"
           class="inline-block bg-rose-600 hover:bg-rose-700 text-white font-bold px-8 py-3 rounded-xl shadow transition transform hover:-translate-y-0.5">
            📅 Book Your Event
        </a>
    </div>

</x-eo-layout>