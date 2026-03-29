<x-layout>

    <div class="max-w-3xl mx-auto px-4 py-16 print-wrapper">

        <div class="text-center mb-10 print-header">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full mb-6 text-4xl shadow-inner">
                ✅
            </div>
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">{{ __('Booking Confirmed!') }}</h1>
            <p class="text-gray-500">{{ __("Your request has been received. Please keep this receipt for your records.") }}</p>
        </div>

        {{-- Add the print-card class here to prevent splitting --}}
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden print-card">

            {{-- Header Strip --}}
            <div class="bg-slate-900 px-8 py-6 flex justify-between items-center gap-4">
                <div class="text-white">
                    <p class="text-[10px] uppercase font-bold opacity-50 mb-0.5 tracking-tighter">{{ __('Booking Code') }}</p>
                    <p class="text-xl font-mono font-bold">{{ $booking->booking_code }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] uppercase font-bold text-emerald-400 mb-1 tracking-tighter">{{ __('Status') }}</p>
                    <div class="inline-block border-2 border-emerald-400 text-emerald-400 px-3 py-1 rounded-lg text-xs font-black uppercase tracking-widest">
                        {{ __($booking->status) }}
                    </div>
                </div>
            </div>

            <div class="p-6 md:p-10">

                {{-- Client & Tour Summary --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">{{ __('Client Details') }}</h4>
                        <p class="font-bold text-gray-900 text-lg">{{ $booking->client_name }}</p>
                        <p class="text-gray-500">{{ $booking->client_phone }}</p>
                        <p class="text-gray-500">{{ $booking->client_email ?? __('No email provided') }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">{{ __('Tour Details') }}</h4>
                        <p class="text-gray-700"><span class="font-bold">{{ __('Tour') }}:</span> {{ $booking->tour->name }}</p>
                        <p class="text-gray-700"><span class="font-bold">{{ __('Date') }}:</span> {{ $booking->tour_date->translatedFormat('l, d F Y') }}</p>
                        <p class="text-gray-700"><span class="font-bold">{{ __('Participants') }}:</span> {{ $booking->participants }} {{ __('person') }}</p>
                        @if($booking->tour->meeting_point)
                            <p class="text-gray-700"><span class="font-bold">{{ __('Meeting Point') }}:</span> {{ $booking->tour->meeting_point }}</p>
                        @endif
                    </div>
                </div>

                {{-- Price Breakdown --}}
                <div class="mb-8">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">{{ __('Price Breakdown') }}</h4>
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-gray-400 text-xs border-b border-gray-100">
                                <th class="pb-3 font-bold">{{ __('Item') }}</th>
                                <th class="pb-3 font-bold text-right">{{ __('Price') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr>
                                <td class="py-4">
                                    <p class="font-bold text-gray-900">{{ $booking->tour->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        Rp {{ number_format($booking->tour->price_per_person, 0, ',', '.') }} × {{ $booking->participants }} {{ __('person') }}
                                    </p>
                                </td>
                                <td class="py-4 text-right font-bold text-gray-900 whitespace-nowrap">
                                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Grand Total --}}
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 flex justify-between items-center gap-4">
                    <span class="text-gray-600 font-bold md:text-lg">{{ __('Estimated Grand Total') }}</span>
                    <span class="text-2xl md:text-3xl font-extrabold text-emerald-600 whitespace-nowrap">
                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Footer Actions (Hidden on Print, Fixed for mobile) --}}
            @php
                $waPhone = $booking->tour->user->phone_number ?? null;
                if ($waPhone) {
                    $waPhone = preg_replace('/[^0-9]/', '', $waPhone);
                    if (str_starts_with($waPhone, '0')) $waPhone = '62' . substr($waPhone, 1);
                    
                    $intro = __("Halo! Saya telah melakukan booking tour:");
                    $codeLabel = __("Kode Booking");
                    $tourLabel = __("Tour");
                    $dateLabel = __("Tanggal");
                    $paxLabel = __("Peserta");
                    $totalLabel = __("Total");
                    $footer = __("Mohon konfirmasi pesanan saya. Terima kasih!");

                    $waMessage = "$intro\n\n" .
                        "*$codeLabel:* {$booking->booking_code}\n" .
                        "*$tourLabel:* {$booking->tour->name}\n" .
                        "*$dateLabel:* " . $booking->tour_date->format('d/m/Y') . "\n" .
                        "*$paxLabel:* {$booking->participants} " . __('person') . "\n" .
                        "*$totalLabel:* Rp " . number_format($booking->total_price, 0, ',', '.') . "\n\n" .
                        $footer;
                        
                    $waLink = "https://wa.me/{$waPhone}?text=" . urlencode($waMessage);
                }
            @endphp

            <div class="bg-gray-50/50 px-6 md:px-10 py-6 border-t border-gray-100 flex flex-col sm:flex-row gap-3 no-print">
                @if(!empty($waLink))
                    <a href="{{ $waLink }}" target="_blank"
                       class="w-full sm:flex-1 bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold py-3.5 rounded-xl shadow-lg shadow-[#25D366]/30 transition transform hover:-translate-y-1 flex items-center justify-center gap-2 text-sm md:text-base px-2">
                        💬 {{ __('Send to WhatsApp') }}
                    </a>
                @endif

                <button type="button" onclick="window.print()"
                        class="w-full sm:flex-1 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 font-bold py-3.5 rounded-xl transition flex items-center justify-center gap-2 text-sm md:text-base px-2">
                    📄 {{ __('Print Receipt') }}
                </button>
            </div>
        </div>

        <div class="text-center mt-8 no-print">
            <a href="{{ route('tour.home') }}" class="text-emerald-600 font-bold hover:underline">
                &larr; {{ __('Back to Tours') }}
            </a>
        </div>
    </div>

</x-layout>

<style>
    @media print {
        /* 1. Hide UI elements */
        nav, footer, .no-print { 
            display: none !important; 
        }

        /* 2. Setup Page */
        @page { 
            margin: 1cm; 
            size: auto;
        }
        
        body { 
            background-color: white !important; 
            padding: 0 !important;
            margin: 0 !important;
            -webkit-print-color-adjust: exact !important; 
            print-color-adjust: exact !important; 
        }

        .print-wrapper {
            max-width: 100% !important; 
            width: 100% !important; 
            margin: 0 !important;
            padding: 0 !important; 
        }

        /* 3. CRITICAL: Prevent the card from splitting */
        .print-card {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            box-shadow: none !important;
            border: 1px solid #e5e7eb !important;
            margin: 0 !important;
        }

        /* 4. Colors */
        .bg-slate-900 { background-color: #0f172a !important; color: white !important; }
        .bg-gray-50 { background-color: #f9fafb !important; }
        .text-gray-400 { color: #9ca3af !important; }
        .text-gray-500 { color: #6b7280 !important; }
        .text-emerald-600 { color: #059669 !important; }
        .border-gray-100 { border-color: #f3f4f6 !important; }
    }
</style>