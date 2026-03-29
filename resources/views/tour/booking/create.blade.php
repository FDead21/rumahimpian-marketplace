<x-layout>

    <div class="max-w-3xl mx-auto px-4 py-12" x-data="tourBookingForm()">

        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">{{ __('Book Your Tour') }}</h1>
            <p class="text-gray-500">{{ __("Fill in your details and we'll confirm within 24 hours") }}</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-6 py-4 mb-8">
                <p class="font-bold mb-2">{{ __('Please fix the following:') }}</p>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tour.booking.store') }}" method="POST"
              class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 space-y-6">
            @csrf
            <input type="hidden" name="tour_id" value="{{ $tour->id }}">

            {{-- Tour Summary Card --}}
            <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-5 flex gap-4 items-center">
                @php
                    $thumb = $tour->media->first()
                        ? asset('storage/' . $tour->media->first()->file_path)
                        : ($tour->thumbnail ? asset('storage/' . $tour->thumbnail) : null);
                @endphp
                @if($thumb)
                    <img src="{{ $thumb }}" class="w-20 h-20 rounded-xl object-cover shrink-0">
                @else
                    <div class="w-20 h-20 rounded-xl bg-emerald-200 flex items-center justify-center text-3xl shrink-0">🌍</div>
                @endif
                <div>
                    <p class="text-xs text-emerald-600 font-bold uppercase tracking-wider mb-1">{{ __('Selected Tour') }}</p>
                    <h2 class="font-extrabold text-gray-900 text-lg leading-tight">{{ $tour->name }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        🕐 {{ $tour->duration_label ?? $tour->duration_days . ' ' . __('Day') }}
                        &nbsp;·&nbsp;
                        <span class="font-bold text-emerald-700">Rp {{ number_format($tour->price_per_person, 0, ',', '.') }}/{{ __('person') }}</span>
                    </p>
                </div>
            </div>

            {{-- Tour Date & Participants --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Tour Date') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="tour_date" required
                           value="{{ old('tour_date') }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-semibold text-gray-800">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        {{ __('Number of Participants') }} <span class="text-red-500">*</span>
                        <span class="text-xs text-gray-400 font-normal ml-1">
                            ({{ __('Min') }}: {{ $tour->min_participants }}@if($tour->max_participants), {{ __('Max') }}: {{ $tour->max_participants }}@endif)
                        </span>
                    </label>
                    <input type="number" name="participants" required
                           x-model="participants"
                           min="{{ $tour->min_participants }}"
                           @if($tour->max_participants) max="{{ $tour->max_participants }}" @endif
                           value="{{ old('participants', $tour->min_participants) }}"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-semibold text-gray-800">
                </div>
            </div>

            {{-- Client Info --}}
            <div class="border-t border-gray-100 pt-6">
                <h3 class="font-bold text-gray-800 mb-4 text-xl">{{ __('Your Contact Details') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Full Name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="client_name" required
                               value="{{ old('client_name') }}"
                               placeholder="{{ __('Your full name') }}"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-semibold text-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('WhatsApp Number') }} <span class="text-red-500">*</span></label>
                        <input type="tel" name="client_phone" required
                               value="{{ old('client_phone') }}"
                               placeholder="+62 812 3456 7890"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-semibold text-gray-800">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Email Address') }}</label>
                        <input type="email" name="client_email"
                               value="{{ old('client_email') }}"
                               placeholder="your@email.com"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-semibold text-gray-800">
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Special Requests / Notes') }}</label>
                <textarea name="notes" rows="3"
                          placeholder="{{ __('Any special requirements or questions...') }}"
                          class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none text-gray-800 resize-none">{{ old('notes') }}</textarea>
            </div>

            {{-- Price Summary --}}
            <div class="bg-slate-900 rounded-2xl p-6 text-white shadow-xl mt-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <p class="text-gray-400 text-sm font-medium mb-1">{{ __('Estimated Total') }}</p>
                        <div class="text-3xl md:text-4xl font-extrabold text-white">
                            Rp <span x-text="formatPrice(totalPrice)">{{ number_format($tour->price_per_person * $tour->min_participants, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-gray-400 text-xs mt-1">
                            Rp {{ number_format($tour->price_per_person, 0, ',', '.') }} × <span x-text="participants">{{ $tour->min_participants }}</span> {{ __('person') }}
                        </p>
                    </div>
                    <button type="submit"
                            class="w-full md:w-auto bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-4 px-8 rounded-xl shadow-lg shadow-emerald-500/30 transition transform hover:-translate-y-1 text-lg whitespace-nowrap">
                        📅 {{ __('Confirm Booking') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function tourBookingForm() {
            return {
                participants: {{ old('participants', $tour->min_participants) }},
                pricePerPerson: {{ $tour->price_per_person }},
                get totalPrice() {
                    const p = parseInt(this.participants) || 0;
                    return p * this.pricePerPerson;
                },
                formatPrice(price) {
                    return new Intl.NumberFormat('id-ID').format(price);
                }
            }
        }
    </script>

</x-layout>