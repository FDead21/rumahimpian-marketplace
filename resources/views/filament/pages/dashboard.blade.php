<x-filament-panels::page>
    
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Welcome, {{ $user->name }}! 👋
            </h2>
            <p class="text-gray-500 dark:text-gray-400">
                @if($role === 'ADMIN') Platform Overview @else Your Performance @endif
            </p>
        </div>
        
        <a href="{{ route('home') }}" target="_blank" 
           class="inline-flex items-center justify-center gap-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 px-4 py-2 rounded-lg font-medium transition shadow-sm">
            <span>🌍 Open Website</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
        </a>
    </div>

    {{-- STATS GRID (Cards) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Card 1 --}}
        <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/50 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400 text-2xl">🏠</div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Properties</p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalProperties }}</h3>
            </div>
        </div>

        {{-- Card 2 --}}
        <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-green-50 dark:bg-green-900/50 rounded-lg flex items-center justify-center text-green-600 dark:text-green-400 text-2xl">👀</div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Views</p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalViews ?? 0) }}</h3>
            </div>
        </div>

        {{-- Card 3 --}}
        @if($role === 'ADMIN')
            <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/50 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-400 text-2xl">👥</div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Users</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalUsers }}</h3>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/50 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-400 text-2xl">💬</div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">New Leads</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $recentLeads->count() }}</h3>
                </div>
            </div>
        @endif
    </div>

    {{-- CHARTS GRID (Fixed Layout) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        
        {{-- LEFT: Line Chart (Spans 2 columns) --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
             @livewire(\App\Filament\Widgets\PropertiesOverviewChart::class)
        </div>

        {{-- RIGHT: Pie Chart (Spans 1 column) --}}
        <div class="lg:col-span-1 bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
             @livewire(\App\Filament\Widgets\PropertyTypeChart::class)
        </div>

    </div>

    {{-- BOTTOM: Recent Items --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-900">Recent Properties</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($properties as $property)
                        <div class="p-4 flex gap-4 hover:bg-gray-50 transition items-center">
                            <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                @if($property->media->first())
                                    <img src="{{ asset('storage/' . $property->media->first()->file_path) }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="flex-grow">
                                <h4 class="font-bold text-gray-900 truncate text-sm">{{ $property->title }}</h4>
                                <p class="text-xs text-gray-500 mb-1">{{ $property->city }}</p>
                            </div>
                            <a href="{{ route('property.show', [$property->id, $property->slug]) }}" target="_blank" class="text-indigo-600 text-sm hover:underline">View ↗</a>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 text-sm">No properties found.</div>
                    @endforelse
                </div>
            </div>
        </div>
        
        {{-- Recent Inquiries (Agent) or Agencies (Admin) --}}
        @if($role !== 'ADMIN')
        <div>
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-900">Recent Inquiries</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentLeads as $lead)
                        <div class="p-4">
                            <div class="flex justify-between mb-1">
                                <span class="font-bold text-sm">{{ $lead->buyer_name }}</span>
                                <span class="text-[10px] text-gray-400">{{ $lead->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-[10px] text-indigo-600 mb-2">re: {{ Str::limit($lead->property->title ?? 'Deleted', 20) }}</p>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 text-sm">No messages yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif
    </div>

</x-filament-panels::page>