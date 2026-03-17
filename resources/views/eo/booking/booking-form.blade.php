<x-eo-layout>

    <div class="max-w-3xl mx-auto px-4 py-12" x-data="venuePicker()">

        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Book Your Event</h1>
            <p class="text-gray-500">Fill in the details below and we'll get back to you within 24 hours</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-6 py-4 mb-8 flex items-center gap-3">
                <span class="text-2xl">✅</span>
                <div>
                    <p class="font-bold">Booking submitted successfully!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-6 py-4 mb-8">
                <p class="font-bold mb-2">Please fix the following:</p>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('eo.booking.store') }}" method="POST"
              class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 space-y-6">
            @csrf

            {{-- Hidden property_id submitted with form --}}
            <input type="hidden" name="property_id" :value="selectedId">

            {{-- Package Selection --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Event Package <span class="text-red-500">*</span></label>
                <select name="package_id" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none font-semibold text-gray-800">
                    <option value="">Select a package...</option>
                    @foreach($packages as $pkg)
                        <option value="{{ $pkg->id }}" {{ (request('package_id') == $pkg->id || old('package_id') == $pkg->id) ? 'selected' : '' }}>
                            {{ $pkg->name }} — Rp {{ number_format($pkg->price, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Event Type + Date --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Event Type <span class="text-red-500">*</span></label>
                    <select name="event_type" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none font-semibold text-gray-800">
                        <option value="">Select type...</option>
                        @foreach(['Wedding', 'Corporate', 'Birthday', 'Gathering', 'Other'] as $type)
                            <option value="{{ $type }}" {{ old('event_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Event Date <span class="text-red-500">*</span></label>
                    <input type="date" name="event_date" required
                           value="{{ old('event_date') }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none font-semibold text-gray-800">
                </div>
            </div>

            {{-- Guest Count + Venue --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Estimated Guests</label>
                    <input type="number" name="guest_count" min="1"
                           value="{{ old('guest_count') }}"
                           placeholder="e.g. 200"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none font-semibold text-gray-800">
                </div>

                {{-- Venue Picker --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Venue
                        <span class="text-xs text-gray-400 font-normal ml-1">(optional — from property marketplace)</span>
                    </label>

                    {{-- Selected venue display / trigger --}}
                    <button type="button" @click="openModal()"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none text-left transition hover:border-rose-300 hover:bg-rose-50 flex items-center justify-between gap-2">
                        <span class="flex items-center gap-2 min-w-0">
                            <template x-if="selectedId">
                                <img :src="selectedThumb" class="w-8 h-8 rounded-lg object-cover shrink-0" x-show="selectedThumb">
                            </template>
                            <span class="truncate font-semibold text-gray-800" x-text="selectedId ? selectedLabel : 'I\'ll arrange my own venue'" :class="!selectedId ? 'text-gray-400 font-normal' : ''"></span>
                        </span>
                        <span class="shrink-0">
                            <template x-if="!selectedId">
                                <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M3 21h2m7-10h.01M12 7h.01"/>
                                </svg>
                            </template>
                            <template x-if="selectedId">
                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828A2 2 0 019 17H7v-2a2 2 0 01.586-1.414z"/>
                                </svg>
                            </template>
                        </span>
                    </button>

                    {{-- Clear button --}}
                    <button type="button" x-show="selectedId" @click="clearVenue()"
                            class="mt-1.5 text-xs text-gray-400 hover:text-rose-500 transition flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Clear venue selection
                    </button>
                </div>
            </div>

            {{-- Client Info --}}
            <div class="border-t border-gray-100 pt-6">
                <h3 class="font-bold text-gray-800 mb-4">Your Contact Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="client_name" required
                               value="{{ old('client_name') }}"
                               placeholder="Your full name"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none font-semibold text-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">WhatsApp Number <span class="text-red-500">*</span></label>
                        <input type="tel" name="client_phone" required
                               value="{{ old('client_phone') }}"
                               placeholder="+62 812 3456 7890"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none font-semibold text-gray-800">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="client_email"
                               value="{{ old('client_email') }}"
                               placeholder="your@email.com"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none font-semibold text-gray-800">
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Special Requests / Notes</label>
                <textarea name="notes" rows="4"
                          placeholder="Tell us anything special about your event..."
                          class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none text-gray-800 resize-none">{{ old('notes') }}</textarea>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform hover:-translate-y-0.5 text-lg">
                📅 Submit Booking Request
            </button>

            <p class="text-center text-xs text-gray-400">
                We'll contact you via WhatsApp within 24 hours to confirm your booking.
            </p>
        </form>


        {{-- ===================== VENUE MODAL ===================== --}}
        <div x-show="isOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @click.self="closeModal()"
             @keydown.escape.window="closeModal()"
             style="display:none">

            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                    <div>
                        <h2 class="text-xl font-extrabold text-gray-900">Choose a Venue</h2>
                        <p class="text-sm text-gray-400 mt-0.5">Browse available properties from the marketplace</p>
                    </div>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition p-1 rounded-full hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Filters --}}
                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="relative md:col-span-1">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                            </svg>
                            <input x-model="search" @input="filterVenues()"
                                   type="text" placeholder="Search name, city, district..."
                                   class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-rose-400 focus:border-transparent outline-none transition">
                        </div>
                        <select x-model="filterCity" @change="filterVenues()"
                                class="px-3 py-2.5 text-sm rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-rose-400 outline-none transition font-medium text-gray-700">
                            <option value="">🏙 All Cities</option>
                            @foreach($venues->pluck('city')->unique()->sort() as $city)
                                <option value="{{ $city }}">{{ $city }}</option>
                            @endforeach
                        </select>
                        <select x-model="filterType" @change="filterVenues()"
                                class="px-3 py-2.5 text-sm rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-rose-400 outline-none transition font-medium text-gray-700">
                            <option value="">🏠 All Types</option>
                            @foreach($venues->pluck('property_type')->unique()->sort() as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-between mt-3 text-xs text-gray-400">
                        <span x-text="filtered.length + ' properties available'"></span>
                        <button x-show="selectedId" @click="clearVenue()"
                                class="text-rose-400 hover:text-rose-600 font-semibold transition">
                            Clear selection
                        </button>
                    </div>
                </div>

                {{-- Grid --}}
                <div class="overflow-y-auto flex-1 p-6">
                    {{-- No venue option --}}
                    <div @click="clearVenue(); closeModal()"
                         class="mb-4 cursor-pointer rounded-2xl border-2 px-5 py-3 flex items-center gap-3 transition"
                         :class="!selectedId ? 'border-rose-400 bg-rose-50' : 'border-gray-100 hover:border-rose-200 hover:bg-rose-50/50'">
                        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-xl shrink-0">🏟</div>
                        <div>
                            <p class="font-bold text-sm text-gray-800">I'll arrange my own venue</p>
                            <p class="text-xs text-gray-400">No property from the marketplace</p>
                        </div>
                        <div class="ml-auto" x-show="!selectedId">
                            <svg class="w-5 h-5 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Property cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <template x-for="venue in filtered" :key="venue.id">
                            <div @click="selectVenue(venue)"
                                 class="group cursor-pointer rounded-2xl border-2 overflow-hidden transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5"
                                 :class="selectedId === venue.id
                                    ? 'border-rose-500 shadow-md shadow-rose-100'
                                    : 'border-gray-100 hover:border-rose-300'">

                                {{-- Image --}}
                                <div class="relative h-40 bg-gray-100 overflow-hidden">
                                    <template x-if="venue.thumb">
                                        <img :src="venue.thumb" :alt="venue.title"
                                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                    </template>
                                    <template x-if="!venue.thumb">
                                        <div class="w-full h-full flex items-center justify-center text-4xl text-gray-300">🏠</div>
                                    </template>

                                    {{-- Price badge --}}
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/65 to-transparent px-3 pt-6 pb-2">
                                        <span class="text-white font-bold text-sm" x-text="'Rp ' + venue.price"></span>
                                    </div>

                                    {{-- Selected checkmark --}}
                                    <div x-show="selectedId === venue.id"
                                         class="absolute top-2 right-2 bg-rose-500 text-white rounded-full p-1 shadow">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>

                                    {{-- Type badge --}}
                                    <div class="absolute top-2 left-2">
                                        <span class="bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide">RENT</span>
                                    </div>
                                </div>

                                {{-- Info --}}
                                <div class="p-3.5">
                                    <p class="font-bold text-sm text-gray-900 leading-snug mb-1 group-hover:text-rose-600 transition truncate" x-text="venue.title"></p>
                                    <p class="text-xs text-gray-500 flex items-center gap-1 mb-3">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span x-text="venue.city + (venue.district ? ', ' + venue.district : '')"></span>
                                    </p>
                                    <div class="flex justify-between text-xs text-gray-400 border-t border-gray-100 pt-2.5">
                                        <span x-text="venue.bedrooms + ' Bed'"></span>
                                        <span x-text="venue.bathrooms + ' Bath'"></span>
                                        <span x-text="venue.building_area + ' m²'"></span>
                                    </div>

                                    <button class="mt-3 w-full py-1.5 rounded-lg text-xs font-bold transition-all"
                                            :class="selectedId === venue.id
                                                ? 'bg-rose-500 text-white'
                                                : 'bg-gray-100 text-gray-600 group-hover:bg-rose-100 group-hover:text-rose-600'">
                                        <span x-text="selectedId === venue.id ? '✓ Selected' : 'Select Venue'"></span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- Empty state --}}
                        <div x-show="filtered.length === 0"
                             class="col-span-3 flex flex-col items-center justify-center py-16 text-gray-400">
                            <div class="text-5xl mb-3">🔍</div>
                            <p class="font-semibold">No properties match your search</p>
                            <button @click="search = ''; filterCity = ''; filterType = ''; filterVenues()"
                                    class="mt-3 text-sm text-rose-500 hover:underline">Clear filters</button>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <span class="text-sm text-gray-500" x-show="selectedId">
                        ✅ <strong x-text="selectedLabel"></strong> selected
                    </span>
                    <span class="text-sm text-gray-400" x-show="!selectedId">No venue selected</span>
                    <button @click="closeModal()"
                            class="bg-rose-600 hover:bg-rose-700 text-white font-bold px-6 py-2.5 rounded-xl transition text-sm">
                        Confirm & Close
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- Alpine Data --}}
    <script>
        function venuePicker() {
            const all = @json($venuesJson);

            return {
                isOpen:      false,
                search:      '',
                filterCity:  '',
                filterType:  '',
                filtered:    all,
                selectedId:  {{ old('property_id') ? old('property_id') : 'null' }},
                selectedLabel: '',
                selectedThumb: null,

                init() {
                    // Restore selection if old() exists (form validation failed)
                    @if(old('property_id'))
                        const pre = all.find(v => v.id === {{ old('property_id') }});
                        if (pre) { this.selectedLabel = pre.title + ' — ' + pre.city; this.selectedThumb = pre.thumb; }
                    @endif
                },

                openModal()  { this.isOpen = true; document.body.style.overflow = 'hidden'; },
                closeModal() { this.isOpen = false; document.body.style.overflow = ''; },

                filterVenues() {
                    const s = this.search.toLowerCase();
                    this.filtered = all.filter(v => {
                        const matchSearch = !s ||
                            v.title.toLowerCase().includes(s) ||
                            v.city.toLowerCase().includes(s) ||
                            v.district.toLowerCase().includes(s);
                        const matchCity = !this.filterCity || v.city === this.filterCity;
                        const matchType = !this.filterType || v.property_type === this.filterType;
                        return matchSearch && matchCity && matchType;
                    });
                },

                selectVenue(venue) {
                    this.selectedId    = venue.id;
                    this.selectedLabel = venue.title + ' — ' + venue.city;
                    this.selectedThumb = venue.thumb;
                    this.closeModal();
                },

                clearVenue() {
                    this.selectedId    = null;
                    this.selectedLabel = '';
                    this.selectedThumb = null;
                },
            }
        }
    </script>

</x-eo-layout>