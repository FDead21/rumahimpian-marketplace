<x-filament-panels::page>

    {{-- HEADER --}}
    @if($role === 'ADMIN')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Platform Overview</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ now()->format('l, d F Y') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ \App\Filament\Resources\TourBookingResource::getUrl('create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition"
            style="background: #0f766e22; color: #0f766e; border: 1px solid #0f766e44;">
                🗺️ New Tour Booking
            </a>
            <a href="{{ \App\Filament\Resources\RentalBookingResource::getUrl('create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition"
            style="background: #1d4ed822; color: #1d4ed8; border: 1px solid #1d4ed844;">
                🚗 New Rental Booking
            </a>
            <a href="{{ \App\Filament\Resources\BookingResource::getUrl('create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition"
            style="background: #b4530922; color: #b45309; border: 1px solid #b4530944;">
                🎉 New Event Booking
            </a>
        </div>
    </div>
    @endif

    {{-- PENDING APPROVALS BANNER --}}
    @if($role === 'ADMIN' && $pendingBookings > 0)
    <div class="mb-6 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl px-5 py-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-2xl">⚠️</span>
            <div>
                <p class="font-bold text-amber-800 dark:text-amber-300 text-sm">
                    {{ $pendingBookings }} booking{{ $pendingBookings > 1 ? 's' : '' }} waiting for confirmation
                </p>
                <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">
                    @php
                        $parts = [];
                        if($tourPending > 0)   $parts[] = $tourPending . ' tour';
                        if($rentalPending > 0) $parts[] = $rentalPending . ' rental';
                        if($eventPending > 0)  $parts[] = $eventPending . ' event';
                    @endphp
                    {{ implode(' · ', $parts) }}
                </p>
            </div>
        </div>
        <div class="flex gap-2 flex-shrink-0 flex-wrap">
            @if($tourPending > 0)
            <a href="{{ \App\Filament\Resources\TourBookingResource::getUrl('index') }}"
            class="text-xs font-semibold text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-800/50 hover:bg-amber-200 dark:hover:bg-amber-700/50 px-3 py-1.5 rounded-lg transition">
                🗺️ {{ $tourPending }} Tour →
            </a>
            @endif
            @if($rentalPending > 0)
            <a href="{{ \App\Filament\Resources\RentalBookingResource::getUrl('index') }}"
            class="text-xs font-semibold text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-800/50 hover:bg-amber-200 dark:hover:bg-amber-700/50 px-3 py-1.5 rounded-lg transition">
                🚗 {{ $rentalPending }} Rental →
            </a>
            @endif
            @if($eventPending > 0)
            <a href="{{ \App\Filament\Resources\BookingResource::getUrl('index') }}"
            class="text-xs font-semibold text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-800/50 hover:bg-amber-200 dark:hover:bg-amber-700/50 px-3 py-1.5 rounded-lg transition">
                🎉 {{ $eventPending }} Event →
            </a>
            @endif
        </div>
    </div>
    @endif

    {{-- STATS GRID --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">

        {{-- Total Properties --}}
        <div class="bg-white dark:bg-gray-900 p-5 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
            <div class="w-11 h-11 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-xl flex-shrink-0">🏠</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Properties</p>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($totalProperties) }}</h3>
            </div>
        </div>

        {{-- Total Bookings --}}
        <div class="bg-white dark:bg-gray-900 p-5 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
            <div class="w-11 h-11 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center text-xl flex-shrink-0">📅</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Total Bookings</p>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($totalBookings) }}</h3>
            </div>
        </div>

        {{-- Total Revenue --}}
        <div class="bg-white dark:bg-gray-900 p-5 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
            <div class="w-11 h-11 bg-amber-50 dark:bg-amber-900/30 rounded-lg flex items-center justify-center text-xl flex-shrink-0">💰</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Total Revenue</p>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            </div>
        </div>

        {{-- Pending / Users --}}
        @if($role === 'ADMIN')
        <div class="bg-white dark:bg-gray-900 p-5 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
            <div class="w-11 h-11 bg-rose-50 dark:bg-rose-900/30 rounded-lg flex items-center justify-center text-xl flex-shrink-0">⏳</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Pending Bookings</p>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($pendingBookings) }}</h3>
            </div>
        </div>
        @else
        <div class="bg-white dark:bg-gray-900 p-5 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
            <div class="w-11 h-11 bg-purple-50 dark:bg-purple-900/30 rounded-lg flex items-center justify-center text-xl flex-shrink-0">✉️</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">New Leads</p>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $recentLeads->count() }}</h3>
            </div>
        </div>
        @endif

    </div>

    {{-- TODAY'S SCHEDULE STRIP --}}
    @if($role === 'ADMIN')
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="px-6 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-base">📅</span>
                    <h3 class="font-bold text-gray-900 dark:text-white text-sm">
                        Today — {{ now()->format('l, d F Y') }}
                    </h3>
                </div>
                <span class="text-xs text-gray-400">
                    {{ ($todaySchedule ?? collect())->count() }} active booking{{ ($todaySchedule ?? collect())->count() !== 1 ? 's' : '' }} today
                </span>
            </div>

            @if(($todaySchedule ?? collect())->isEmpty())
            <div class="px-6 py-4 text-sm text-gray-400 text-center">
                Nothing scheduled for today. 🎉
            </div>
            @else
            <div class="flex overflow-x-auto gap-3 px-6 py-4 scrollbar-hide">
                @foreach($todaySchedule as $item)
                <a href="{{ $item['url'] }}"
                class="flex-shrink-0 flex items-center gap-3 px-4 py-3 rounded-xl border transition hover:shadow-sm"
                style="border-color: {{ $item['color'] }}44; background: {{ $item['color'] }}11; min-width: 220px;">
                    <span class="text-xl flex-shrink-0">{{ $item['icon'] }}</span>
                    <div class="min-w-0">
                        <p class="text-sm font-bold truncate"
                        style="color: {{ $item['color'] }};">
                            {{ $item['name'] }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">{{ $item['client'] }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs font-medium px-1.5 py-0.5 rounded"
                                style="background: {{ $item['color'] }}22; color: {{ $item['color'] }};">
                                {{ $item['status'] }}
                            </span>
                            <span class="text-xs text-gray-400">{{ $item['time'] }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- CHARTS GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Booking Trend (spans 2 cols) --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            @livewire(\App\Filament\Widgets\BookingTrendChart::class)
        </div>

        {{-- Revenue by Type (spans 1 col) --}}
        <div class="lg:col-span-1 bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            @livewire(\App\Filament\Widgets\RevenueByTypeChart::class)
        </div>

    </div>

    {{-- BOTTOM: Upcoming Bookings + Recent Properties --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- Upcoming Bookings Feed (spans 2 cols) --}}
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <h3 class="font-bold text-gray-900 dark:text-white">Upcoming Bookings</h3>
                <span class="text-xs text-gray-400">Next 10 confirmed &amp; pending</span>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($upcomingBookings ?? [] as $booking)
                <a href="{{ $booking['url'] }}"
                   class="flex items-center gap-4 px-6 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition group">

                    {{-- Icon --}}
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center text-base flex-shrink-0"
                         style="background: {{ $booking['color'] }}22;">
                        {{ $booking['icon'] }}
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                            {{ $booking['name'] }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ $booking['client'] }} · {{ $booking['date'] }}
                        </p>
                    </div>

                    {{-- Status badge --}}
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <span class="text-xs font-semibold px-2 py-1 rounded-full"
                              style="
                                background: {{ $booking['status'] === 'CONFIRMED' ? '#d1fae5' : '#fef3c7' }};
                                color: {{ $booking['status'] === 'CONFIRMED' ? '#065f46' : '#92400e' }};
                              ">
                            {{ $booking['status'] }}
                        </span>
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">
                            Rp {{ number_format($booking['price'], 0, ',', '.') }}
                        </span>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
                @empty
                <div class="px-6 py-10 text-center text-gray-400 text-sm">
                    No upcoming bookings.
                </div>
                @endforelse
            </div>
        </div>
    </div>


</x-filament-panels::page>