<x-layout>
    {{-- Include Flatpickr CSS & JS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <div class="max-w-3xl mx-auto px-4 py-12">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-6">{{ __('Book') }} {{ $vehicle->name }}</h1>

        <form action="{{ route('rental.booking.store') }}" method="POST" class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
            @csrf
            <input type="hidden" name="rental_vehicle_id" value="{{ $vehicle->id }}">

            <div class="space-y-6">
                {{-- Date Range Picker --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Select Rental Dates') }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400"> </span>
                        
                        {{-- The Flatpickr Input --}}
                        <input type="text" id="date-range" name="dates" placeholder="Select start and end date" required readonly
                               class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition font-medium text-gray-900 cursor-pointer">
                        @error('dates')
                            <p class="text-red-500 text-sm mt-1 font-medium">⚠️ {{ $message }}</p>
                        @enderror
                        <div style="display: flex; gap: 16px; margin-top: 8px;">
                            <span style="display: flex; align-items: center; gap: 5px; font-size: 0.75rem; color: #6b7280;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                                {{ __('Closed / Holiday') }}
                            </span>
                            <span style="display: flex; align-items: center; gap: 5px; font-size: 0.75rem; color: #6b7280;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #f97316; display: inline-block;"></span>
                                {{ __('Fully Booked') }}
                            </span>
                            <span style="display: flex; align-items: center; gap: 5px; font-size: 0.75rem; color: #6b7280;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #d1d5db; display: inline-block;"></span>
                                {{ __('Available') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Client Details --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Full Name') }}</label>
                        <input type="text" name="client_name" required class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('WhatsApp Number') }}</label>
                        <input type="tel" name="client_phone" required class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                    {{ __('Request Booking') }}
                </button>
            </div>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        fetch('/api/blocked-dates')
            .then(r => r.json())
            .then(reasons => {
                flatpickr("#date-range", {
                    mode: "range",
                    minDate: "today",
                    dateFormat: "Y-m-d",
                    disable: @json($blockedDates ?? []),
                    onDayCreate: function(dObj, dStr, fp, dayElem) {
                        const d = dayElem.dateObj;
                        if (!d) return;

                        const dateStr = [
                            d.getFullYear(),
                            String(d.getMonth() + 1).padStart(2, '0'),
                            String(d.getDate()).padStart(2, '0')
                        ].join('-');

                        if (!reasons[dateStr]) return;

                        const info = reasons[dateStr];
                        const isAdmin  = info.type === 'admin';
                        const dotColor = isAdmin ? '#ef4444' : '#f97316'; // red vs orange
                        const label    = isAdmin ? '🛑 ' : '  ';

                        // Tooltip
                        dayElem.setAttribute('title', label + info.reason);
                        dayElem.style.position = 'relative';
                        dayElem.style.cursor   = 'not-allowed';

                        // Colored dot
                        const dot = document.createElement('span');
                        dot.style.cssText = `
                            position: absolute;
                            bottom: 2px;
                            left: 50%;
                            transform: translateX(-50%);
                            width: 4px;
                            height: 4px;
                            border-radius: 50%;
                            background: ${dotColor};
                            display: block;
                            pointer-events: none;
                        `;
                        dayElem.appendChild(dot);
                    }
                });
            });
    });
    </script>
</x-layout>