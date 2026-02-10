<x-layout>
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 mb-4">Latest Property News</h1>
                <p class="text-lg text-gray-500">Insights, market trends, and tips for your dream home.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($articles as $article)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition duration-300 flex flex-col h-full">
                    <a href="{{ route('articles.show', $article->slug) }}" class="block relative h-48 overflow-hidden group">
                        @if($article->thumbnail)
                            <img src="{{ asset('storage/' . $article->thumbnail) }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                            </div>
                        @endif
                    </a>
                    
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="text-sm text-indigo-600 font-semibold mb-2">
                            {{ $article->published_at->format('M d, Y') }}
                        </div>
                        <a href="{{ route('articles.show', $article->slug) }}" class="block">
                            <h3 class="text-xl font-bold text-gray-900 mb-3 hover:text-indigo-600 transition">{{ $article->title }}</h3>
                        </a>
                        <p class="text-gray-600 text-sm mb-4 flex-1">
                            {{ Str::limit(strip_tags($article->content), 100) }}
                        </p>
                        <a href="{{ route('articles.show', $article->slug) }}" class="inline-flex items-center text-indigo-600 font-bold hover:underline">
                            Read Article <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $articles->links() }}
            </div>
        </div>
    </div>
</x-layout>