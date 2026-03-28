<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Dynamically prioritize EO settings if they exist, otherwise fallback to Property/Global settings --}}
    <title>{{ $title ?? $eoSettings['eo_site_name'] ?? $settings['site_name'] ?? 'RumahImpian Group' }}</title>

    @if(!empty($eoSettings['eo_site_favicon']))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $eoSettings['eo_site_favicon']) }}">
    @elseif(!empty($settings['site_favicon']))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $settings['site_favicon']) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}"> 
    @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen font-sans text-gray-900">

    @include('components.navbar')

    <main class="flex-grow">
        {{ $slot }}
    </main>
    @include('components.popup')
\
    @include('components.footer')

    <div x-data="compareBar()" 
         x-show="count > 0" 
         x-transition.duration.300ms
         class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 shadow-[0_-5px_15px_rgba(0,0,0,0.1)] p-4 z-50"
         style="display: none;">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div>
                <span class="font-bold text-gray-900 text-lg" x-text="count + ' {{ __('Properties Selected') }}'"></span>
                <p class="text-xs text-gray-500">{{ __('Select up to 3 properties') }}</p>
            </div>
            <div class="flex gap-3">
                <button @click="clear()" class="text-gray-500 hover:text-red-600 font-bold text-sm underline">{{ __('Clear') }}</button>
                <a :href="'/property/compare?ids=' + ids.join(',')" class="bg-sky-600 hover:bg-sky-700 text-white px-6 py-2 rounded-lg font-bold shadow-lg transition transform hover:-translate-y-1">
                    ⚖️ {{ __('Compare Now') }}
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('wishlist', {
                ids: JSON.parse(localStorage.getItem('wishlist_ids') || '[]'),
                has(id) { return this.ids.includes(id); },
                toggle(id) {
                    if (this.ids.includes(id)) { this.ids = this.ids.filter(i => i !== id); } 
                    else { this.ids.push(id); }
                    localStorage.setItem('wishlist_ids', JSON.stringify(this.ids));
                    window.dispatchEvent(new CustomEvent('wishlist-updated'));
                }
            });
        });

        function compareLogic(id) {
            return {
                id: id,
                selected: false,
                init() {
                    this.checkStatus();
                    window.addEventListener('compare-updated', () => this.checkStatus());
                },
                checkStatus() {
                    let stored = JSON.parse(localStorage.getItem('compare_ids') || '[]');
                    this.selected = stored.includes(this.id);
                },
                toggle() {
                    let stored = JSON.parse(localStorage.getItem('compare_ids') || '[]');
                    if (this.selected) { stored = stored.filter(i => i !== this.id); } 
                    else {
                        if (stored.length >= 3) { alert('{{ __('Limit 3 properties') }}'); return; }
                        stored.push(this.id);
                    }
                    localStorage.setItem('compare_ids', JSON.stringify(stored));
                    this.checkStatus();
                    window.dispatchEvent(new CustomEvent('compare-updated'));
                }
            }
        }

        function compareBar() {
            return {
                count: 0,
                ids: [],
                init() {
                    this.update();
                    window.addEventListener('compare-updated', () => this.update());
                },
                update() {
                    this.ids = JSON.parse(localStorage.getItem('compare_ids') || '[]');
                    this.count = this.ids.length;
                },
                clear() {
                    localStorage.removeItem('compare_ids');
                    window.dispatchEvent(new CustomEvent('compare-updated'));
                }
            }
        }
    </script>
</body>
</html>