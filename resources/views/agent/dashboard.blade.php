<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard - RumahImpian</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    @include('components.navbar')

    <div class="max-w-7xl mx-auto px-4 py-8 w-full flex-grow">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-gray-500">Here is what's happening with your listings today.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ url('/portal') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 transition">
                    ⚙️ Manage Listings
                </a>
                <a href="{{ route('agent.show', Auth::id()) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                    👤 View My Public Profile
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 text-2xl">
                    🏠
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Active Listings</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $totalProperties }}</h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center text-green-600 text-2xl">
                    👀
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Views</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalViews) }}</h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center text-purple-600 text-2xl">
                    💬
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">New Leads</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $recentLeads->count() }}</h3>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900">My Recent Properties</h3>
                    </div>
                    
                    <div class="divide-y divide-gray-100">
                        @forelse($properties as $property)
                            <div class="p-4 flex gap-4 hover:bg-gray-50 transition items-center">
                                <div class="w-20 h-20 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                    @if($property->media->first())
                                        <img src="{{ asset('storage/' . $property->media->first()->file_path) }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                
                                <div class="flex-grow">
                                    <h4 class="font-bold text-gray-900 truncate">{{ $property->title }}</h4>
                                    <p class="text-xs text-gray-500 mb-1">
                                        {{ $property->city }} • {{ $property->views }} Views
                                    </p>
                                    <span class="px-2 py-0.5 rounded text-xs font-bold 
                                        {{ $property->status == 'PUBLISHED' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $property->status }}
                                    </span>
                                </div>

                                <a href="{{ route('property.show', ['id' => $property->id, 'slug' => $property->slug]) }}" target="_blank" class="text-indigo-600 hover:bg-indigo-50 p-2 rounded-full">
                                    ↗️
                                </a>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500">
                                You haven't listed any properties yet.
                            </div>
                        @endforelse
                    </div>
                    
                    @if($properties->hasPages())
                        <div class="p-4 border-t border-gray-100">
                            {{ $properties->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900">Recent Inquiries</h3>
                    </div>
                    
                    <div class="divide-y divide-gray-100">
                        @forelse($recentLeads as $lead)
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-bold text-sm text-gray-900">{{ $lead->buyer_name }}</span>
                                    <span class="text-xs text-gray-400">{{ $lead->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-indigo-600 mb-2 font-medium">
                                    re: {{ Str::limit($lead->property->title, 20) }}
                                </p>
                                <p class="text-sm text-gray-600 italic">"{{ Str::limit($lead->message, 50) }}"</p>
                                <div class="mt-2 text-xs text-gray-500">
                                    📞 {{ $lead->buyer_phone }}
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500 text-sm">
                                No messages yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

    </div>

    @include('components.footer')
</body>
</html>