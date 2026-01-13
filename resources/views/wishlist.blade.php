<x-layout title="My Favorites - RumahImpian">
    <div class="max-w-7xl mx-auto px-4 py-12 w-full" 
         x-data="wishlistPage()">
        
        <h1 class="text-3xl font-bold text-gray-900 mb-8 flex items-center gap-3">
            <span class="text-red-500">❤️</span> My Saved Homes
        </h1>

        {{-- Loading State --}}
        <div x-show="loading" class="py-20 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
            <p class="text-gray-500">Loading your favorites...</p>
        </div>

        {{-- Empty State --}}
        <div x-show="!loading && isEmpty" class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300" style="display: none;">
            <div class="text-6xl mb-4">💔</div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No favorites yet</h3>
            <p class="text-gray-500 mb-6">Start browsing and click the heart icon to save properties here.</p>
            <a href="{{ route('home') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 transition">
                Browse Properties
            </a>
        </div>

        {{-- Grid Container --}}
        <div x-show="!loading && !isEmpty" 
             class="grid grid-cols-1 md:grid-cols-3 gap-8" 
             id="wishlist-grid"
             style="display: none;">
             {{-- Property Cards will be injected here by JS --}}
        </div>

    </div>

    <script>
        function wishlistPage() {
            return {
                loading: true,
                isEmpty: false,
                
                init() {
                    // 1. Get IDs from LocalStorage
                    const ids = JSON.parse(localStorage.getItem('wishlist_ids')) || [];

                    if (ids.length === 0) {
                        this.loading = false;
                        this.isEmpty = true;
                        return;
                    }

                    // 2. Fetch HTML from Server
                    fetch(`{{ route('wishlist.data') }}?ids=${ids.join(',')}`)
                        .then(response => response.json())
                        .then(data => {
                            const grid = document.getElementById('wishlist-grid');
                            grid.innerHTML = data.html; // Inject HTML
                            
                            this.loading = false;

                            // 3. CRITICAL FIX: Wake up Alpine.js!
                            // This makes the "Compare" and "Heart" buttons work in the new HTML
                            if (window.Alpine) {
                                Alpine.initTree(grid);
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            this.loading = false;
                            this.isEmpty = true; 
                        });
                }
            }
        }
    </script>
</x-layout>