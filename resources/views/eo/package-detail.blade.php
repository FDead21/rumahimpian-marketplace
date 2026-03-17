<x-eo-layout>

    <div class="max-w-5xl mx-auto px-4 py-12">

        {{-- Back --}}
        <a href="{{ route('eo.packages') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-rose-600 mb-8 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Packages
        </a>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

            {{-- Image --}}
            <div class="rounded-2xl overflow-hidden h-80 md:h-auto">
                @if($package->thumbnail)
                    <img src="{{ asset('storage/' . $package->thumbnail) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full min-h-64 bg-gradient-to-br from-rose-100 to-pink-200 flex items-center justify-center text-8xl rounded-2xl">🎊</div>
                @endif
            </div>

            {{-- Details --}}
            <div>
                @if($package->is_featured)
                    <span class="bg-rose-100 text-rose-600 text-xs font-bold px-3 py-1 rounded-full mb-3 inline-block">⭐ Featured Package</span>
                @endif

                <h1 class="text-3xl font-extrabold text-gray-900 mb-2">{{ $package->name }}</h1>

                @if($package->max_pax)
                    <p class="text-gray-500 mb-4">👥 Up to <strong>{{ $package->max_pax }}</strong> guests</p>
                @endif

                @if($package->description)
                    <p class="text-gray-600 mb-6 leading-relaxed">{{ $package->description }}</p>
                @endif

                {{-- Price --}}
                <div class="bg-rose-50 border border-rose-100 rounded-xl px-6 py-4 mb-6">
                    <p class="text-sm text-rose-600 font-semibold mb-1">Starting from</p>
                    <p class="text-3xl font-extrabold text-rose-700">Rp {{ number_format($package->price, 0, ',', '.') }}</p>
                </div>

                {{-- Inclusions --}}
                @if($package->inclusions)
                <div class="mb-6">
                    <h3 class="font-bold text-gray-800 mb-3">What's Included:</h3>
                    <ul class="space-y-2">
                        @foreach($package->inclusions as $item)
                            <li class="flex items-center gap-3 text-gray-700">
                                <span class="w-5 h-5 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">✓</span>
                                {{ $item['item'] ?? $item }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- CTA --}}
                <a href="{{ route('eo.booking.booking-form', ['package_id' => $package->id]) }}"
                   class="block w-full text-center bg-rose-600 hover:bg-rose-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform hover:-translate-y-0.5 text-lg">
                    📅 Book This Package
                </a>
            </div>
        </div>
    </div>

</x-eo-layout>
