<x-layout>

    {{-- Page Header --}}
    <div class="bg-gradient-to-br from-rose-600 to-pink-700 text-white py-16 text-center">
        <h1 class="text-4xl font-extrabold mb-2">{{ __('Our Event Packages') }}</h1>
        <p class="text-rose-200 text-lg">{{ __('Everything you need for a perfect event, bundled together') }}</p>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($packages as $package)
            <div class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative flex flex-col">

                @if($package->is_featured)
                    <div class="absolute top-4 left-4 z-10 bg-rose-600 text-white text-xs font-bold px-3 py-1 rounded-full">{{ __('Featured') }}</div>
                @endif

                {{-- Image --}}
                <div class="relative h-52 overflow-hidden">
                    @if($package->thumbnail)
                        <img src="{{ asset('storage/' . $package->thumbnail) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-rose-100 to-pink-200 flex items-center justify-center text-6xl">🎊</div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-lg">
                        <span class="text-rose-700 font-extrabold text-xl">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-6 flex-1 flex flex-col">
                    <h3 class="font-bold text-xl text-gray-900 mb-1 group-hover:text-rose-600 transition">{{ $package->name }}</h3>
                    @if($package->max_pax)
                        <p class="text-sm text-gray-500 mb-3">{{ __('Up to') }} {{ $package->max_pax }} {{ __('guests') }}</p>
                    @endif
                    @if($package->description)
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2">{{ $package->description }}</p>
                    @endif

                    {{-- Inclusions --}}
                    @if($package->inclusions)
                        <ul class="space-y-1.5 mb-6 flex-1">
                            @foreach($package->inclusions as $item)
                                <li class="text-sm text-gray-700 flex items-center gap-2">
                                    <span class="text-rose-500 font-bold">✓</span>
                                    {{ $item['item'] ?? $item }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- Actions --}}
                    <div class="flex gap-3 mt-auto">
                        <a href="{{ route('eventOrganizer.packages.show', $package->slug) }}"
                           class="flex-1 text-center bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold py-2.5 rounded-xl border border-gray-200 transition text-sm">
                            {{ __('Details') }}
                        </a>
                        <a href="{{ route('eventOrganizer.booking.create', ['package_id' => $package->id]) }}"
                           class="flex-1 text-center bg-rose-600 hover:bg-rose-700 text-white font-bold py-2.5 rounded-xl transition text-sm">
                            {{ __('Book This') }}
                        </a>
                    </div>
                </div>
            </div>
            @empty
                <div class="col-span-3 text-center py-16 text-gray-400">
                    <div class="text-5xl mb-4"></div>
                    <p class="text-lg font-semibold">{{ __('No packages available yet.') }}</p>
                </div>
            @endforelse
        </div>
    </div>

</x-layout>