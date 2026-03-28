<x-layout>
    @section('title', __('Receipt') . '_' . $booking->booking_code . '_' . Str::slug($booking->client_name))

    <title>{{ __('Receipt') }}_{{ $booking->booking_code }}_{{ Str::slug($booking->client_name) }}</title>
    <div class="max-w-3xl mx-auto px-4 py-16">
        
        {{-- Success Animation & Header --}}
        <div class="text-center mb-10 print:hidden">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 text-green-600 rounded-full mb-6 text-4xl shadow-inner">
                ✅
            </div>
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">{{ __('Booking Confirmed!') }}</h1>
            <p class="text-gray-500">{{ __("Your request has been received. Please keep this receipt for your records.") }}</p>
        </div>

        {{-- THE RECEIPT CARD --}}
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            
            {{-- Top Branding Strip --}}
            <div class="bg-slate-900 px-8 py-6 flex justify-between items-center gap-4">
                <div class="text-white">
                    <p class="text-[10px] uppercase font-bold opacity-50 mb-0.5 tracking-tighter">{{ __('Receipt Number') }}</p>
                    <p class="text-xl font-mono font-bold">{{ $booking->booking_code }}</p>
                </div>
                {{-- Status --}}
                <div class="text-right">
                    <p class="text-[10px] uppercase font-bold text-rose-500 mb-1 tracking-tighter print:text-rose-600">{{ __('Status') }}</p>
                    <div class="inline-block border-2 border-rose-500 text-rose-500 px-3 py-1 rounded-lg text-xs font-black uppercase tracking-widest print:border-rose-600 print:text-rose-600">
                        {{ __($booking->status) }}
                    </div>
                </div>
            </div>

            <div class="p-8 md:p-12">
                
                {{-- Client & Event Summary --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">{{ __('Client Details') }}</h4>
                        <p class="font-bold text-gray-900 text-lg">{{ $booking->client_name }}</p>
                        <p class="text-gray-500">{{ $booking->client_phone }}</p>
                        <p class="text-gray-500">{{ $booking->client_email ?? __('No email provided') }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">{{ __('Event Details') }}</h4>
                        <p class="text-gray-700"><span class="font-bold">{{ __('Date') }}:</span> {{ $booking->event_date->translatedFormat('l, d F Y') }}</p>
                        <p class="text-gray-700"><span class="font-bold">{{ __('Type') }}:</span> {{ __($booking->event_type) }} ({{ $booking->guest_count }} {{ __('guests') }})</p>
                        @if($booking->property)
                            <p class="text-gray-700"><span class="font-bold">{{ __('Venue') }}:</span> {{ $booking->property->title }}</p>
                        @endif
                    </div>
                </div>

                {{-- LINE ITEMS TABLE --}}
                <div class="mb-10">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">{{ __('Price Breakdown') }}</h4>
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-gray-400 text-xs border-b border-gray-100">
                                <th class="pb-3 font-bold">{{ __('Item Description') }}</th>
                                <th class="pb-3 font-bold text-right">{{ __('Price') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            {{-- Base Package --}}
                            <tr>
                                <td class="py-4">
                                    <p class="font-bold text-gray-900">{{ $booking->package->name }}</p>
                                    <p class="text-xs text-gray-500">{{ __('Main Event Package') }}</p>
                                </td>
                                <td class="py-4 text-right font-bold text-gray-900">
                                    Rp {{ number_format($booking->package->price, 0, ',', '.') }}
                                </td>
                            </tr>

                            {{-- Dynamic Vendors --}}
                            @foreach($booking->vendors as $vendor)
                                <tr>
                                    <td class="py-4">
                                        <p class="font-bold text-gray-900">{{ $vendor->name }}</p>
                                        <div class="text-xs text-gray-500 italic whitespace-pre-line">
                                            {{-- Translate pivot notes if they contain our standard keywords --}}
                                            {{ str_replace(['Included in Package Bundle', 'Selected Add-ons:'], [__('Included in Package Bundle'), __('Selected Add-ons:')], $vendor->pivot->notes) }}
                                        </div>
                                    </td>
                                    <td class="py-4 text-right font-bold text-gray-900">
                                        + Rp {{ number_format($vendor->pivot->agreed_price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- GRAND TOTAL --}}
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 flex justify-between items-center">
                    <span class="text-gray-600 font-bold text-lg">{{ __('Estimated Grand Total') }}</span>
                    <span class="text-3xl font-extrabold text-rose-600">
                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- FOOTER / ACTIONS --}}
            <div class="bg-gray-50/50 px-8 py-8 border-t border-gray-100 flex flex-col sm:flex-row gap-4 print:hidden">
                
                @php
                    $waPhone = $booking->package->user->phone_number ?? '6281296760196';
                    $waPhone = preg_replace('/[^0-9]/', '', $waPhone);

                    $message = __("Halo") . "! " . __("Saya telah melakukan booking") . ":\n\n";
                    $message .= "*" . __('Booking Code') . ":* {$booking->booking_code}\n";
                    $message .= "*" . __('Package') . ":* {$booking->package->name}\n";
                    $message .= "*" . __('Date') . ":* " . $booking->event_date->format('d/m/Y') . "\n";
                    $message .= "*" . __('Total') . ":* Rp " . number_format($booking->total_price, 0, ',', '.') . "\n\n";
                    $message .= __("Mohon konfirmasi pesanan saya. Terima kasih!");
                    
                    $waLink = "https://wa.me/{$waPhone}?text=" . urlencode($message);
                @endphp

                <a href="{{ $waLink }}" target="_blank"
                   class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-2xl shadow-lg shadow-green-500/30 transition transform hover:-translate-y-1 flex items-center justify-center gap-3">
                    💬 {{ __('Send to WhatsApp') }}
                </a>
                
                <button type="button" onclick="window.print()" 
                   class="flex-1 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 font-bold py-4 rounded-2xl transition flex items-center justify-center gap-3">
                    📄 {{ __('Print Receipt') }}
                </button>
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('eventOrganizer.home') }}" class="text-rose-600 font-bold hover:underline print:hidden">
                &larr; {{ __('Back to Home') }}
            </a>
        </div>
    </div>
</x-layout>

<style>
    @media print {
        nav, footer, .print\:hidden { 
            display: none !important; 
        }

        @page {
            margin: 1.5cm;
            size: A4;
        }

        body {
            background-color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Ensure the Receipt fills the width */
        .max-w-3xl {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
        }

        /* Make the Status Badge look like a real 'Stamp' on paper */
        .bg-rose-500\/20 {
            background-color: transparent !important;
            border: 2px solid #e11d48 !important;
            color: #e11d48 !important;
        }

        /* Force Header Color */
        .bg-slate-900 {
            background-color: #0f172a !important;
            color: white !important;
        }

        /* Tighten spacing for one-page fit */
        .p-8, .md\:p-12 { padding: 1.5rem !important; }
        .mb-12, .mb-10 { margin-bottom: 1rem !important; }
        
        /* Ensure table text is sharp */
        th, td { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
    }
</style>