<x-layout>
<style>
    /* Sleek custom scrollbar for the vendor list */
    .vendor-scroll::-webkit-scrollbar { width: 6px; }
    .vendor-scroll::-webkit-scrollbar-track { background: transparent; }
    .vendor-scroll::-webkit-scrollbar-thumb { background-color: #e2e8f0; border-radius: 10px; }
    .vendor-scroll:hover::-webkit-scrollbar-thumb { background-color: #cbd5e1; }
</style>
    <div class="max-w-4xl mx-auto px-4 py-12" x-data="bookingForm()">

        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">{{ __('Book Your Event') }}</h1>
            <p class="text-gray-500">{{ __("Fill in the details below and we'll get back to you within 24 hours") }}</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-6 py-4 mb-8 flex items-center gap-3">
                <span class="text-2xl">✅</span>
                <div>
                    <p class="font-bold">{{ __('Booking submitted successfully!') }}</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-6 py-4 mb-8">
                <p class="font-bold mb-2">{{ __('Please fix the following:') }}</p>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('eventOrganizer.booking.store') }}" method="POST"
              class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 space-y-6">
            @csrf

            {{-- Hidden property_id submitted with form --}}
            <input type="hidden" name="property_id" :value="selectedId">

            {{-- Package Selection --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Event Package') }} <span class="text-red-500">*</span></label>
                <select name="package_id" required x-model="selectedPackageId"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none font-semibold text-gray-800">
                    <option value="">{{ __('Select a package...') }}</option>
                    @foreach($packages as $pkg)
                        <option value="{{ $pkg->id }}">
                            {{ $pkg->name }} — Rp {{ number_format($pkg->price, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Event Type + Date --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Event Type') }} <span class="text-red-500">*</span></label>
                    <select name="event_type" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none font-semibold text-gray-800">
                        <option value="">{{ __('Select type...') }}</option>
                        @foreach(['Wedding', 'Corporate', 'Birthday', 'Gathering', 'Other'] as $type)
                            <option value="{{ $type }}" {{ old('event_type') == $type ? 'selected' : '' }}>{{ __($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Event Date') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="event_date" required
                           value="{{ old('event_date') }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none font-semibold text-gray-800">
                </div>
            </div>

            {{-- Guest Count + Venue --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Estimated Guests') }}</label>
                    <input type="number" name="guest_count" min="1" required
                           value="{{ old('guest_count') }}"
                           placeholder="{{ __('e.g. 200') }}"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none font-semibold text-gray-800">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        {{ __('Venue') }}
                        <span class="text-xs text-gray-400 font-normal ml-1">({{ __('optional') }})</span>
                    </label>
                    <button type="button" @click="openModal()"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none text-left transition hover:border-rose-300 hover:bg-rose-50 flex items-center justify-between gap-2">
                        <span class="flex items-center gap-2 min-w-0">
                            <template x-if="selectedId">
                                <img :src="selectedThumb" class="w-8 h-8 rounded-lg object-cover shrink-0" x-show="selectedThumb">
                            </template>
                            <span class="truncate font-semibold text-gray-800" x-text="selectedId ? selectedLabel : `{{ __("I'll arrange my own venue") }}`" :class="!selectedId ? 'text-gray-400 font-normal' : ''"></span>
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
                    <button type="button" x-show="selectedId" @click="clearVenue()"
                            class="mt-1.5 text-xs text-gray-400 hover:text-rose-500 transition flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        {{ __('Clear venue selection') }}
                    </button>
                </div>
            </div>

            {{-- NEW: Clean Customization Section --}}
            <div class="border-t border-gray-100 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-gray-800 text-xl">{{ __('Customize Your Event') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('Optional add-ons to complete your package.') }}</p>
                    </div>
                    <button type="button" @click="openVendorListModal()"
                            class="bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold px-4 py-2 rounded-xl border border-rose-200 transition text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ __('Add Services') }}
                    </button>
                </div>

                {{-- Hidden inputs --}}
                <template x-for="(indices, vId) in selectedServices" :key="'input-'+vId">
                    <template x-for="idx in indices" :key="'input-'+vId+'-'+idx">
                        <input type="hidden" :name="`vendor_services[${vId}][]`" :value="idx">
                    </template>
                </template>
                <template x-for="vid in selectedVendors" :key="'input-base-'+vid">
                    <input type="hidden" name="vendors[]" :value="vid">
                </template>

                {{-- Dynamic Bundled Vendors (Included in Package) --}}
                <template x-if="bundledVendors.length > 0">
                    <div class="mb-6 space-y-2">
                        <p class="text-sm font-bold text-gray-700">{{ __('Included in selected package:') }}</p>
                        <template x-for="bv in bundledVendors" :key="'bundled-'+bv.id">
                            <div class="flex items-center justify-between p-3 bg-green-50/50 border border-green-100 rounded-xl shadow-sm">
                                <div class="flex items-center gap-3">
                                    <span class="text-xl">✅</span>
                                    <div>
                                        <p class="font-bold text-sm text-gray-900" x-text="bv.name"></p>
                                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider" x-text="bv.category"></p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-green-700 bg-green-100 px-2 py-1 rounded uppercase tracking-wide" x-text="bv.is_mandatory ? '{{ __('Required') }}' : '{{ __('Included') }}'"></span>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Summary of Selected Items --}}
                <div class="space-y-3" x-show="selectedVendors.length > 0 || hasSelectedServices()">
                    
                    {{-- Base Vendors Selected --}}
                    <template x-for="vid in selectedVendors" :key="'base-'+vid">
                        <div class="flex items-center justify-between p-3 border border-gray-200 bg-white rounded-xl shadow-sm">
                            <div>
                                <p class="font-bold text-sm text-gray-900" x-text="getVendorName(vid)"></p>
                                <p class="text-xs text-gray-500">{{ __('Base Package') }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="font-extrabold text-rose-600 text-sm" x-text="'+ Rp ' + formatPrice(getVendorPrice(vid))"></span>
                                <button type="button" @click="toggleBaseVendor(vid)" class="text-gray-400 hover:text-red-500 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- Specific Services Selected --}}
                    <template x-for="(indices, vId) in selectedServices" :key="'srv-'+vId">
                        <template x-for="idx in indices" :key="'srv-'+vId+'-'+idx">
                            <div class="flex items-center justify-between p-3 border border-gray-200 bg-white rounded-xl shadow-sm">
                                <div>
                                    <p class="font-bold text-sm text-gray-900" x-text="getServiceName(vId, idx)"></p>
                                    <p class="text-xs text-gray-500" x-text="'{{ __('From') }}: ' + getVendorName(vId)"></p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="font-extrabold text-rose-600 text-sm" x-text="'+ Rp ' + formatPrice(getServicePrice(vId, idx))"></span>
                                    <button type="button" @click="toggleService(vId, idx)" class="text-gray-400 hover:text-red-500 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </template>
                </div>

                {{-- Empty State --}}
                <div x-show="selectedVendors.length === 0 && !hasSelectedServices()" class="p-6 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 text-center">
                    <p class="text-gray-400 text-sm">{{ __('No extra vendors selected yet.') }}</p>
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
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none font-semibold text-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('WhatsApp Number') }} <span class="text-red-500">*</span></label>
                        <input type="tel" name="client_phone" required
                               value="{{ old('client_phone') }}"
                               placeholder="+62 812 3456 7890"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none font-semibold text-gray-800">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Email Address') }}</label>
                        <input type="email" name="client_email"
                               value="{{ old('client_email') }}"
                               placeholder="your@email.com"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none font-semibold text-gray-800">
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Special Requests / Notes') }}</label>
                <textarea name="notes" rows="3"
                          placeholder="{{ __('Tell us anything special about your event...') }}"
                          class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none text-gray-800 resize-none">{{ old('notes') }}</textarea>
            </div>

            {{-- Dynamic Price Display --}}
            <div class="bg-slate-900 rounded-2xl p-6 text-white shadow-xl mt-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <p class="text-gray-400 text-sm font-medium mb-1">{{ __('Estimated Total') }}</p>
                        <div class="text-3xl md:text-4xl font-extrabold text-white">
                            Rp <span x-text="formatPrice(totalPrice)">0</span>
                        </div>
                    </div>
                    <button type="submit"
                            class="w-full md:w-auto bg-rose-500 hover:bg-rose-600 text-white font-bold py-4 px-8 rounded-xl shadow-lg shadow-rose-500/30 transition transform hover:-translate-y-1 text-lg whitespace-nowrap">
                        📅 {{ __('Confirm Booking') }}
                    </button>
                </div>
            </div>

        </form>

        {{-- Venue Modal --}}
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

            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden">
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                    <div>
                        <h2 class="text-xl font-extrabold text-gray-900">{{ __('Choose a Venue') }}</h2>
                        <p class="text-sm text-gray-400 mt-0.5">{{ __('Browse available properties from the marketplace') }}</p>
                    </div>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition p-1 rounded-full hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="relative md:col-span-1">
                            <input x-model="search" @input="filterVenues()"
                                   type="text" placeholder="{{ __('Search name, city, district...') }}"
                                   class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-rose-400 focus:border-transparent outline-none transition">
                        </div>
                        <select x-model="filterCity" @change="filterVenues()"
                                class="px-3 py-2.5 text-sm rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-rose-400 outline-none transition font-medium text-gray-700">
                            <option value="">🏙 {{ __('All Cities') }}</option>
                            @foreach($venues->pluck('city')->unique()->sort() as $city)
                                <option value="{{ $city }}">{{ $city }}</option>
                            @endforeach
                        </select>
                        <select x-model="filterType" @change="filterVenues()"
                                class="px-3 py-2.5 text-sm rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-rose-400 outline-none transition font-medium text-gray-700">
                            <option value="">🏠 {{ __('All Types') }}</option>
                            @foreach($venues->pluck('property_type')->unique()->sort() as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center justify-between mt-3 text-xs text-gray-400">
                        <span x-text="filtered.length + ' {{ __('properties available') }}'"></span>
                        <button x-show="selectedId" @click="clearVenue()" class="text-rose-400 hover:text-rose-600 font-semibold transition">
                            {{ __('Clear selection') }}
                        </button>
                    </div>
                </div>

                <div class="overflow-y-auto flex-1 p-6">
                    <div @click="clearVenue(); closeModal()"
                         class="mb-4 cursor-pointer rounded-2xl border-2 px-5 py-3 flex items-center gap-3 transition"
                         :class="!selectedId ? 'border-rose-400 bg-rose-50' : 'border-gray-100 hover:border-rose-200 hover:bg-rose-50/50'">
                        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-xl shrink-0">🏟</div>
                        <div>
                            <p class="font-bold text-sm text-gray-800">{{ __("I'll arrange my own venue") }}</p>
                            <p class="text-xs text-gray-400">{{ __('No property from the marketplace') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <template x-for="venue in filtered" :key="venue.id">
                            <div @click="selectVenue(venue)"
                                 class="group cursor-pointer rounded-2xl border-2 overflow-hidden transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5"
                                 :class="selectedId === venue.id ? 'border-rose-500 shadow-md' : 'border-gray-100 hover:border-rose-300'">
                                
                                <div class="relative h-40 bg-gray-100 overflow-hidden">
                                    <template x-if="venue.thumb">
                                        <img :src="venue.thumb" :alt="venue.title"
                                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                    </template>
                                    <template x-if="!venue.thumb">
                                        <div class="w-full h-full flex items-center justify-center text-4xl text-gray-300">🏠</div>
                                    </template>

                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/65 to-transparent px-3 pt-6 pb-2">
                                        <span class="text-white font-bold text-sm" x-text="'Rp ' + venue.price"></span>
                                    </div>

                                    <div x-show="selectedId === venue.id"
                                         class="absolute top-2 right-2 bg-rose-500 text-white rounded-full p-1 shadow">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>

                                    <div class="absolute top-2 left-2">
                                        <span class="bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide">RENT</span>
                                    </div>
                                </div>

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
                                        <span x-text="venue.bedrooms + ' {{ __('Bed') }}'"></span>
                                        <span x-text="venue.bathrooms + ' {{ __('Bath') }}'"></span>
                                        <span x-text="venue.building_area + ' m²'"></span>
                                    </div>
                                     <button class="mt-3 w-full py-1.5 rounded-lg text-xs font-bold transition-all"
                                             :class="selectedId === venue.id ? 'bg-rose-500 text-white' : 'bg-gray-100 text-gray-600 group-hover:bg-rose-100 group-hover:text-rose-600'">
                                         <span x-text="selectedId === venue.id ? '✓ {{ __('Selected') }}' : '{{ __('Select Venue') }}'"></span>
                                     </button>
                                </div>
                            </div>
                        </template>

                        {{-- Empty state --}}
                        <div x-show="filtered.length === 0"
                             class="col-span-3 flex flex-col items-center justify-center py-16 text-gray-400">
                            <div class="text-5xl mb-3">🔍</div>
                            <p class="font-semibold">{{ __('No properties match your search') }}</p>
                            <button @click="search = ''; filterCity = ''; filterType = ''; filterVenues()"
                                    class="mt-3 text-sm text-rose-500 hover:underline">{{ __('Clear filters') }}</button>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <span class="text-sm text-gray-500" x-show="selectedId">
                        ✅ <strong x-text="selectedLabel"></strong> {{ __('selected') }}
                    </span>
                    <span class="text-sm text-gray-400" x-show="!selectedId">{{ __('No venue selected') }}</span>
                    <button @click="closeModal()" class="bg-rose-600 hover:bg-rose-700 text-white font-bold px-6 py-2.5 rounded-xl transition text-sm">
                        {{ __('Confirm & Close') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- ===================== VENDOR SERVICES MODAL ===================== --}}
        <div x-show="isVendorModalOpen"
             class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @click.self="closeVendorModal()"
             @keydown.escape.window="closeVendorModal()"
             style="display:none">

            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col overflow-hidden" x-show="isVendorModalOpen" x-transition>
                
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50">
                    <div>
                        <h2 class="text-xl font-extrabold text-gray-900" x-text="activeVendor ? activeVendor.name : '{{ __('Services') }}'"></h2>
                        <p class="text-sm text-gray-500 mt-0.5">{{ __('Select the specific items you want') }}</p>
                    </div>
                    <button type="button" @click="closeVendorModal()" class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Scrollable Service List --}}
                <div class="overflow-y-auto flex-1 p-6 space-y-3">
                    <template x-if="activeVendor && activeVendor.service_menu">
                        <template x-for="(item, index) in activeVendor.service_menu" :key="index">
                            <label class="flex items-center gap-4 p-4 border rounded-2xl cursor-pointer transition-colors"
                                   :class="(selectedServices[activeVendor.id] || []).includes(String(index)) ? 'border-rose-500 bg-rose-50' : 'border-gray-200 hover:border-rose-300'">
                                
                                <input type="checkbox" :value="index" x-model="selectedServices[activeVendor.id]" 
                                       class="w-5 h-5 text-rose-600 border-gray-300 rounded focus:ring-rose-500">
                                
                                <template x-if="item.image">
                                    <img :src="'/storage/' + item.image" class="w-16 h-16 rounded-xl object-cover shrink-0">
                                </template>
                                <template x-if="!item.image">
                                    <div class="w-16 h-16 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 shrink-0">📦</div>
                                </template>

                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-900" x-text="item.item_name"></h4>
                                    <p class="text-xs text-gray-500 line-clamp-1" x-text="item.description"></p>
                                    <p class="text-sm font-extrabold text-rose-600 mt-1" x-text="'+ Rp ' + formatPrice(item.price)"></p>
                                </div>
                            </label>
                        </template>
                    </template>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                    <button type="button" @click="closeVendorModal()" class="bg-rose-600 hover:bg-rose-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm">
                        {{ __('Done') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- ===================== VENDOR DIRECTORY MODAL ===================== --}}
        <div x-show="isVendorListModalOpen"
             class="fixed inset-0 z-[50] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @click.self="closeVendorListModal()"
             @keydown.escape.window="closeVendorListModal()"
             style="display:none">

            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[85vh] flex flex-col overflow-hidden" x-show="isVendorListModalOpen" x-transition>
                
                {{-- Header & Filters --}}
                <div class="px-6 py-5 border-b border-gray-100 bg-white flex items-center justify-between">
                    <h2 class="text-xl font-extrabold text-gray-900">{{ __('Vendor Directory') }}</h2>
                    <button type="button" @click="closeVendorListModal()" class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                {{-- Category Filter Tabs --}}
                <div class="flex gap-3 overflow-x-auto hide-scrollbar pb-2 px-6 py-2">
                     <button @click="vendorCategoryFilter = ''" class="shrink-0 px-4 py-1.5 rounded-full text-sm font-bold transition" :class="vendorCategoryFilter === '' ? 'bg-rose-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">{{ __('All') }}</button>
                     @foreach($vendorsByCategory->keys() as $cat)
                         <button @click="vendorCategoryFilter = '{{ $cat }}'" class="shrink-0 px-4 py-1.5 rounded-full text-sm font-bold transition" :class="vendorCategoryFilter === '{{ $cat }}' ? 'bg-rose-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">{{ __($cat) }}</button>
                     @endforeach
                </div>

                {{-- Vendor Grid --}}
                <div class="overflow-y-auto flex-1 p-6 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <template x-for="vendor in filteredVendorsList" :key="vendor.id">
                            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col h-full hover:shadow-md transition">
                                <div class="flex-1">
                                    <span class="text-[10px] uppercase font-bold tracking-wider text-rose-500 bg-rose-50 px-2 py-0.5 rounded-md" x-text="vendor.category"></span>
                                    <h4 class="font-bold text-gray-900 mt-2 leading-tight" x-text="vendor.name"></h4>
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2" x-text="vendor.description"></p>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-50">
                                    {{-- If it has a service menu --}}
                                    <template x-if="vendor.service_menu && vendor.service_menu.length > 0">
                                        <button @click="openVendorModal(vendor.id)" class="w-full bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold py-2 rounded-xl text-sm transition">
                                            {{ __('View Services') }}
                                        </button>
                                    </template>
                                    {{-- If it does NOT have a service menu (Base Vendor) --}}
                                    <template x-if="!vendor.service_menu || vendor.service_menu.length === 0">
                                        <button @click="toggleBaseVendor(vendor.id)" class="w-full font-bold py-2 rounded-xl text-sm transition"
                                                :class="selectedVendors.includes(String(vendor.id)) ? 'bg-rose-600 text-white' : 'bg-white border border-gray-200 text-gray-700 hover:border-rose-300'">
                                            <span x-text="selectedVendors.includes(String(vendor.id)) ? '✓ {{ __('Selected') }}' : '+ {{ __('Add') }} (Rp ' + formatPrice(vendor.price_from) + ')'"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div x-show="filteredVendorsList.length === 0" class="text-center py-10 text-gray-400">{{ __('No vendors found in this category.') }}</div>
                </div>
            </div>
        </div>
        
    </div>

    @php
        $packages->load('vendors');
        $mappedPackages = $packages->map(function($p) {
            return [
                'id' => $p->id,
                'price' => $p->price,
                'vendors' => $p->vendors->map(fn($v) => [
                    'id' => $v->id,
                    'name' => $v->name,
                    'category' => $v->category,
                    'is_mandatory' => $v->pivot->is_mandatory
                ])
            ];
        });
        $mappedVendors = $vendorsByCategory->flatten()->map(function($v) {
            return [
                'id' => $v->id,
                'name' => $v->name,
                'category' => $v->category,       
                'description' => $v->description,  
                'price_from' => $v->price_from,
                'service_menu' => is_array($v->service_menu) ? $v->service_menu : []
            ];
        })->values();
    @endphp

    {{-- Alpine Data --}}
    <script>
        function bookingForm() {
            const allVenues = @json($venuesJson ?? []); 
            const packagesData = @json($mappedPackages); 
            const vendorsData = @json($mappedVendors);

            return {
                selectedPackageId: '{{ old('package_id', request('package_id')) }}',
                selectedVendors: @json(old('vendors', [])).map(String),
                selectedServices: {}, 
                
                // Modal States
                isVendorListModalOpen: false,
                vendorCategoryFilter: '',
                isVendorModalOpen: false,
                activeVendor: null,

                init() {
                    vendorsData.forEach(v => {
                        this.selectedServices[v.id] = [];
                    });
                    
                    @if(old('property_id'))
                        const pre = allVenues.find(v => v.id === {{ old('property_id') }});
                        if (pre) { this.selectedLabel = pre.title + ' — ' + pre.city; this.selectedThumb = pre.thumb; }
                    @endif
                },

                // --- Vendor Directory Logic ---
                openVendorListModal() { this.isVendorListModalOpen = true; document.body.style.overflow = 'hidden'; },
                closeVendorListModal() { this.isVendorListModalOpen = false; document.body.style.overflow = ''; },
                
                get filteredVendorsList() {
                    if (!this.vendorCategoryFilter) return vendorsData;
                    // Because we didn't pass category in mappedVendors originally, let's look it up dynamically or just pass it! 
                    // Note: Ensure 'category' is in your mappedVendors array in the @php block at the top of the script!
                    return vendorsData.filter(v => v.category === this.vendorCategoryFilter);
                },

                // --- Service Selection Logic ---
                openVendorModal(vendorId) {
                    this.activeVendor = vendorsData.find(v => v.id == vendorId);
                    this.isVendorModalOpen = true;
                    // Keep body overflow hidden since we are likely opening this over the other modal
                },
                closeVendorModal() {
                    this.isVendorModalOpen = false;
                    setTimeout(() => this.activeVendor = null, 300);
                },

                toggleBaseVendor(vid) {
                    vid = String(vid);
                    if (this.selectedVendors.includes(vid)) {
                        this.selectedVendors = this.selectedVendors.filter(id => id !== vid);
                    } else {
                        this.selectedVendors.push(vid);
                    }
                },
                toggleService(vId, idx) {
                    idx = String(idx);
                    if (this.selectedServices[vId].includes(idx)) {
                        this.selectedServices[vId] = this.selectedServices[vId].filter(i => i !== idx);
                    } else {
                        this.selectedServices[vId].push(idx);
                    }
                },

                // --- UI Helpers for Summary ---
                hasSelectedServices() {
                    return Object.values(this.selectedServices).some(arr => arr.length > 0);
                },
                getVendorName(vid) {
                    let v = vendorsData.find(v => v.id == vid);
                    return v ? v.name : '';
                },
                getVendorPrice(vid) {
                    let v = vendorsData.find(v => v.id == vid);
                    return v ? v.price_from : 0;
                },
                getServiceName(vId, idx) {
                    let v = vendorsData.find(v => v.id == vId);
                    return v && v.service_menu[idx] ? v.service_menu[idx].item_name : '';
                },
                getServicePrice(vId, idx) {
                    let v = vendorsData.find(v => v.id == vId);
                    return v && v.service_menu[idx] ? v.service_menu[idx].price : 0;
                },
                get bundledVendors() {
                    if (!this.selectedPackageId) return [];
                    let pkg = packagesData.find(p => p.id == this.selectedPackageId);
                    return pkg && pkg.vendors ? pkg.vendors : [];
                },

                // --- Math & Format ---
                get totalPrice() {
                    let total = 0;
                    if (this.selectedPackageId) {
                        let pkg = packagesData.find(p => p.id == this.selectedPackageId);
                        if (pkg) total += parseFloat(pkg.price);
                    }
                    this.selectedVendors.forEach(vid => {
                        let vnd = vendorsData.find(v => v.id == vid);
                        if (vnd && vnd.price_from) total += parseFloat(vnd.price_from);
                    });
                    Object.entries(this.selectedServices).forEach(([vId, indices]) => {
                        let vnd = vendorsData.find(v => v.id == vId);
                        if (vnd && vnd.service_menu) {
                            indices.forEach(idx => {
                                let item = vnd.service_menu[idx];
                                if (item && item.price) total += parseFloat(item.price);
                            });
                        }
                    });
                    return total;
                },
                formatPrice(price) { return new Intl.NumberFormat('id-ID').format(price); },

                // --- Venue Logic ---
                isOpen: false, search: '', filterCity: '', filterType: '', filtered: allVenues,
                selectedId: {{ old('property_id') ? old('property_id') : 'null' }}, selectedLabel: '', selectedThumb: null,
                openModal()  { this.isOpen = true; document.body.style.overflow = 'hidden'; },
                closeModal() { this.isOpen = false; document.body.style.overflow = ''; },
                filterVenues() {
                    const s = this.search.toLowerCase();
                    this.filtered = allVenues.filter(v => (!s || v.title.toLowerCase().includes(s) || v.city.toLowerCase().includes(s) || v.district.toLowerCase().includes(s)) && (!this.filterCity || v.city === this.filterCity) && (!this.filterType || v.property_type === this.filterType));
                },
                selectVenue(venue) { this.selectedId = venue.id; this.selectedLabel = venue.title + ' — ' + venue.city; this.selectedThumb = venue.thumb; this.closeModal(); },
                clearVenue() { this.selectedId = null; this.selectedLabel = ''; this.selectedThumb = null; },
            }
        }
    </script>

</x-layout>