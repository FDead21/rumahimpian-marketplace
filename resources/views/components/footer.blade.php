<footer class="bg-gray-900 text-white pt-16 pb-8 border-t border-gray-800 font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
            
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">R</div>
                    <span class="text-2xl font-bold tracking-tight">RumahImpian</span>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed">
                    The #1 Marketplace for dream homes in Indonesia. We connect buyers with the best agents and developers across the archipelago.
                </p>
            </div>

            <div>
                <h3 class="font-bold text-lg mb-4">Discover</h3>
                <ul class="space-y-3 text-gray-400 text-sm">
                    <li><a href="{{ route('home', ['listing_type' => 'SALE']) }}" class="hover:text-indigo-400 transition">Properties for Sale</a></li>
                    <li><a href="{{ route('home', ['listing_type' => 'RENT']) }}" class="hover:text-indigo-400 transition">Properties for Rent</a></li>
                    <li><a href="#" class="hover:text-indigo-400 transition">New Projects</a></li>
                    <li><a href="#" class="hover:text-indigo-400 transition">Calculators</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-bold text-lg mb-4">Company</h3>
                <ul class="space-y-3 text-gray-400 text-sm">
                    <li><a href="#" class="hover:text-indigo-400 transition">About Us</a></li>
                    <li><a href="#" class="hover:text-indigo-400 transition">Careers</a></li>
                    <li><a href="/admin/register" class="hover:text-indigo-400 transition">Join as Agent</a></li>
                    <li><a href="#" class="hover:text-indigo-400 transition">Privacy Policy</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-bold text-lg mb-4">Stay Updated</h3>
                <p class="text-gray-400 text-sm mb-4">Get the latest listings and market news.</p>
                <form class="flex gap-2">
                    <input type="email" placeholder="Email address" class="bg-gray-800 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600 w-full border border-gray-700">
                    <button type="button" class="bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-lg font-bold transition">
                        →
                    </button>
                </form>
            </div>

        </div>

        <div class="border-t border-gray-800 my-8"></div>

        <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} RumahImpian Indonesia. All rights reserved.</p>
            
            <div class="flex space-x-6 mt-4 md:mt-0">
                <a href="#" class="hover:text-white transition">Instagram</a>
                <a href="#" class="hover:text-white transition">Twitter</a>
                <a href="#" class="hover:text-white transition">LinkedIn</a>
                <a href="#" class="hover:text-white transition">Facebook</a>
            </div>
        </div>

    </div>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('wishlist', {
                items: JSON.parse(localStorage.getItem('wishlist_ids')) || [],

                toggle(id) {
                    if (this.items.includes(id)) {
                        this.items = this.items.filter(i => i !== id); // Remove
                    } else {
                        this.items.push(id); // Add
                    }
                    localStorage.setItem('wishlist_ids', JSON.stringify(this.items));
                },

                has(id) {
                    return this.items.includes(id);
                }
            });
        });
    </script>
</footer>