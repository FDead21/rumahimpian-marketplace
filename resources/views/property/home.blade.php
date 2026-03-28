<x-layout>
    
    <x-portal-hero />

    <div class="h-32 bg-gray-50"></div>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('Latest Listings') }}</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($properties as $property)
                @include('property.components.property-card', ['property' => $property])
            @endforeach
        </div>

        <div class="mt-8">
            {{ $properties->links() }}
        </div>

        @if($properties->count() == 0)
            <div class="text-center py-12 text-gray-500">
                {{ __('No properties found. Please go to Admin Panel and change status to') }} <strong>PUBLISHED</strong>.
            </div>
        @endif
    </div>

    @if(isset($latestArticles) && $latestArticles->count() > 0)
    <div class="bg-gray-50 py-16 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">{{ __('Latest Property News') }}</h2>
                    <p class="text-gray-500 mt-2">{{ __('Insights, trends, and tips for your property journey.') }}</p>
                </div>
                <a href="{{ route('articles.index') }}" class="hidden md:flex items-center text-sky-600 font-bold hover:text-sky-700 transition">
                    {{ __('View All News') }} 
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($latestArticles as $article)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition duration-300 overflow-hidden flex flex-col h-full border border-gray-100 group">
                    <a href="{{ route('articles.show', $article->slug) }}" class="relative h-48 overflow-hidden block">
                        @if($article->thumbnail)
                            <img src="{{ asset('storage/' . $article->thumbnail) }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                <span class="text-4xl">📰</span>
                            </div>
                        @endif
                        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-gray-800 shadow-sm">
                            {{ $article->published_at->format('M d, Y') }}
                        </div>
                    </a>
                    
                    <div class="p-6 flex-1 flex flex-col">
                        <a href="{{ route('articles.show', $article->slug) }}" class="block mb-3">
                            <h3 class="text-xl font-bold text-gray-900 group-hover:text-sky-600 transition line-clamp-2">
                                {{ $article->title }}
                            </h3>
                        </a>
                        <p class="text-gray-500 text-sm mb-4 line-clamp-3 flex-1">
                            {{ Str::limit(strip_tags($article->content), 120) }}
                        </p>
                        <a href="{{ route('articles.show', $article->slug) }}" class="inline-flex items-center text-sky-600 font-bold text-sm hover:underline mt-auto">
                            {{ __('Read Article') }} 
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8 md:hidden text-center">
                <a href="{{ route('articles.index') }}" class="inline-block px-6 py-3 bg-white border border-gray-300 rounded-lg text-gray-700 font-bold hover:bg-gray-50">
                    {{ __('View All News') }}
                </a>
            </div>
        </div>
    </div>
    @endif

</x-layout>