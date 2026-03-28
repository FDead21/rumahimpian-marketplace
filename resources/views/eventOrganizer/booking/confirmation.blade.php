<x-eo-layout>
    <div class="max-w-2xl mx-auto px-4 py-16 text-center">

        <div class="text-6xl mb-6">🎉</div>
        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Booking Received!</h1>
        <p class="text-gray-500 mb-8">We'll contact you via WhatsApp within 24 hours to confirm.</p>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-left mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-gray-800 text-lg">Booking Summary</h2>
                <span class="bg-rose-100 text-rose-600 font-bold px-3 py-1 rounded-full text-sm">
                    {{ $booking->booking_code }}
                </span>
            </div>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Package</span>
                    <span class="font-semibold">{{ $booking->package->name }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Event Type</span>
                    <span class="font-semibold">{{ $booking->event_type }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Event Date</span>
                    <span class="font-semibold">{{ $booking->event_date->format('d M Y') }}</span>
                </div>
                @if($booking->property)
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Venue</span>
                    <span class="font-semibold">{{ $booking->property->title }}</span>
                </div>
                @endif
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Client Name</span>
                    <span class="font-semibold">{{ $booking->client_name }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-500">Total Price</span>
                    <span class="font-extrabold text-rose-600 text-lg">
                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <a href="{{ route('eventOrganizer.home') }}"
           class="inline-block bg-rose-600 hover:bg-rose-700 text-white font-bold px-8 py-3 rounded-xl transition">
            Back to Home
        </a>
    </div>
</x-eo-layout>