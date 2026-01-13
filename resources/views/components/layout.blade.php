<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? $settings['site_name'] ?? 'RumahImpian' }}</title>

    @if(!empty($settings['site_favicon']))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $settings['site_favicon']) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}"> 
    @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- Leaflet CSS/JS ... --}}
</head>
<body class="bg-gray-50 flex flex-col min-h-screen font-sans text-gray-900">

    @include('components.navbar')

    <main class="flex-grow">
        {{ $slot }}
    </main>

    @include('components.footer')

    <div x-data="compareBar()" 
         x-show="count > 0" 
         x-transition.duration.300ms
         class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 shadow-[0_-5px_15px_rgba(0,0,0,0.1)] p-4 z-50"
         style="display: none;">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div>
                <span class="font-bold text-gray-900 text-lg" x-text="count + ' Properties Selected'"></span>
                <p class="text-xs text-gray-500">Select up to 3 properties</p>
            </div>
            <div class="flex gap-3">
                <button @click="clear()" class="text-gray-500 hover:text-red-600 font-bold text-sm underline">Clear</button>
                <a :href="'/compare?ids=' + ids.join(',')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-bold shadow-lg transition transform hover:-translate-y-1">
                    ⚖️ Compare Now
                </a>
            </div>
        </div>
    </div>

    <script>
        // 1. Initialize Alpine Stores (Run before Alpine loads)
        document.addEventListener('alpine:init', () => {
            
            // Define the "wishlist" store used by the property card
            Alpine.store('wishlist', {
                ids: JSON.parse(localStorage.getItem('wishlist_ids') || '[]'),
                
                has(id) {
                    return this.ids.includes(id);
                },
                
                toggle(id) {
                    if (this.ids.includes(id)) {
                        this.ids = this.ids.filter(i => i !== id);
                    } else {
                        this.ids.push(id);
                    }
                    
                    localStorage.setItem('wishlist_ids', JSON.stringify(this.ids));
                    
                    // Dispatch event to update other parts of the UI if needed
                    window.dispatchEvent(new CustomEvent('wishlist-updated'));
                }
            });
        });

        // 2. Existing Comparison Logic functions
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
                        if (stored.length >= 3) { alert('Limit 3 properties'); return; }
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