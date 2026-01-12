<x-filament-panels::page>
    
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome back, {{ Auth::user()->name }}! 👋</h2>
        <p class="text-gray-500 dark:text-gray-400">Here is what's happening with your listings today.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/50 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400 text-2xl">
                🏠
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Active Listings</p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalProperties }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-green-50 dark:bg-green-900/50 rounded-lg flex items-center justify-center text-green-600 dark:text-green-400 text-2xl">
                👀
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Total Views</p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalViews) }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/50 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-400 text-2xl">
                💬
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">New Leads</p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $recentLeads->count() }}</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-white/5">
                    <h3 class="font-bold text-gray-900 dark:text-white">My Recent Properties</h3>
                </div>
                
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($properties as $property)
                        <div class="p-4 flex gap-4 hover:bg-gray-50 dark:hover:bg-white/5 transition items-center">
                            <div class="w-16 h-16 bg-gray-200 dark:bg-gray-800 rounded-lg overflow-hidden flex-shrink-0">
                                @if($property->media->first())
                                    <img src="{{ asset('storage/' . $property->media->first()->file_path) }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            
                            <div class="flex-grow">
                                <h4 class="font-bold text-gray-900 dark:text-white truncate text-sm">{{ $property->title }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                    {{ $property->city }} • {{ $property->views }} Views
                                </p>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $property->status == 'PUBLISHED' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                                    {{ $property->status }}
                                </span>
                            </div>

                            <a href="{{ route('property.show', ['id' => $property->id, 'slug' => $property->slug]) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-white/10 p-2 rounded-full text-sm">
                                View ↗
                            </a>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                            You haven't listed any properties yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-white/5">
                    <h3 class="font-bold text-gray-900 dark:text-white">Recent Inquiries</h3>
                </div>
                
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($recentLeads as $lead)
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-1">
                                <span class="font-bold text-sm text-gray-900 dark:text-white">{{ $lead->buyer_name }}</span>
                                <span class="text-[10px] text-gray-400">{{ $lead->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-[10px] text-indigo-600 dark:text-indigo-400 mb-2 font-medium">
                                re: {{ Str::limit($lead->property->title ?? 'Deleted Property', 20) }}
                            </p>
                            <div class="bg-gray-50 dark:bg-white/5 p-2 rounded text-xs text-gray-600 dark:text-gray-300 italic mb-2">
                                "{{ Str::limit($lead->message, 50) }}"
                            </div>
                            <div class="text-[10px] text-gray-500 dark:text-gray-400 font-mono">
                                📞 {{ $lead->buyer_phone }}
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                            No messages yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    @livewire(\App\Filament\Widgets\PropertiesOverviewChart::class)
    
</x-filament-panels::page>