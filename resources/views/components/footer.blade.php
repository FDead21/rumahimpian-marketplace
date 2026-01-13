<footer class="bg-gray-900 text-white pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
        
        <div class="space-y-4">
            <div class="flex items-center space-x-2">
                <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-cyan-400">
                    {{ $settings['site_name'] ?? 'RumahImpian' }}
                </span>
            </div>
            <p class="text-gray-400 text-sm leading-relaxed">
                {{ $settings['site_description'] ?? 'Your trusted partner in finding the perfect property.' }}
            </p>
        </div>

        <div>
            <h3 class="font-bold text-lg mb-4 text-gray-100">Contact Us</h3>
            <ul class="space-y-3 text-sm text-gray-400">
                <li class="flex items-start space-x-3">
                    <span>📍</span>
                    <span>{{ $settings['contact_address'] ?? 'Bandung, Indonesia' }}</span>
                </li>
                <li class="flex items-center space-x-3">
                    <span>📞</span>
                    <span>{{ $settings['contact_phone'] ?? '+62 812 3456 7890' }}</span>
                </li>
                <li class="flex items-center space-x-3">
                    <span>✉️</span>
                    <span>{{ $settings['contact_email'] ?? 'hello@rumahimpian.id' }}</span>
                </li>
            </ul>
        </div>
        
        <div>
             <h3 class="font-bold text-lg mb-4 text-gray-100">Follow Us</h3>
             <div class="flex space-x-3">
                 
                 {{-- Instagram --}}
                 @if(!empty($settings['social_instagram']))
                 <a href="https://instagram.com/{{ $settings['social_instagram'] }}" target="_blank" class="bg-gray-800 p-2 rounded-full hover:bg-pink-600 text-white transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                 </a>
                 @endif
                 
                 {{-- Facebook --}}
                 @if(!empty($settings['social_facebook']))
                 <a href="https://facebook.com/{{ $settings['social_facebook'] }}" target="_blank" class="bg-gray-800 p-2 rounded-full hover:bg-blue-600 text-white transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                 </a>
                 @endif

                 {{-- Twitter / X --}}
                 @if(!empty($settings['social_twitter']))
                 <a href="https://x.com/{{ $settings['social_twitter'] }}" target="_blank" class="bg-gray-800 p-2 rounded-full hover:bg-black text-white transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                 </a>
                 @endif

                 {{-- YouTube --}}
                 @if(!empty($settings['social_youtube']))
                 <a href="https://youtube.com/{{ $settings['social_youtube'] }}" target="_blank" class="bg-gray-800 p-2 rounded-full hover:bg-red-600 text-white transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                 </a>
                 @endif
             </div>
        </div>

        <div>
            <h3 class="font-bold text-lg mb-4 text-gray-100">Discover</h3>
            <ul class="space-y-3 text-sm text-gray-400">
                <li>
                    <a href="{{ route('home') }}" class="hover:text-white transition flex items-center gap-2">
                        <span>🏠</span> All Properties
                    </a>
                </li>
                
                @auth
                    {{-- Logged in? Show Dashboard --}}
                    <li>
                        <a href="/admin" class="hover:text-white transition flex items-center gap-2">
                            <span>⚡</span> My Dashboard
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                            @csrf
                            <button type="submit" class="hover:text-red-400 transition flex items-center gap-2">
                                <span>🚪</span> Logout
                            </button>
                        </form>
                    </li>
                @else
                    {{-- Guest? Show Login/Register --}}
                    <li>
                        <a href="/admin/login" class="hover:text-white transition flex items-center gap-2">
                            <span>🔐</span> Agent Login
                        </a>
                    </li>
                    <li>
                        <a href="/admin/register" class="hover:text-white transition flex items-center gap-2">
                            <span>📝</span> Register as Agent
                        </a>
                    </li>
                @endauth

                <li>
                    <a href="#" class="hover:text-white transition flex items-center gap-2">
                        <span>🛡️</span> Privacy Policy
                    </a>
                </li>
            </ul>
        </div>
        
    </div>
</footer>