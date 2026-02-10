<x-layout>
    @section('meta_title', $article->seo_title ?? $article->title)
    @section('meta_description', $article->seo_description ?? Str::limit(strip_tags($article->content), 150))
    
    <div class="bg-white py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <article class="prose lg:prose-xl max-w-none">
                <div class="mb-8 text-center">
                    <span class="bg-indigo-100 text-indigo-800 text-sm font-bold px-3 py-1 rounded-full">
                        {{ $article->published_at->format('F d, Y') }}
                    </span>
                    <h1 class="text-4xl font-extrabold text-gray-900 mt-4 mb-6 leading-tight">{{ $article->title }}</h1>
                </div>

                @if($article->thumbnail)
                    <div class="mb-10 rounded-2xl overflow-hidden shadow-lg">
                        <img src="{{ asset('storage/' . $article->thumbnail) }}" class="w-full object-cover max-h-[500px]">
                    </div>
                @endif

                <div class="text-gray-700 leading-relaxed">
                    {!! $article->content !!}
                </div>
            </article>

            <div class="border-t border-gray-200 mt-16 pt-10">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">More to Read</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedArticles as $related)
                        <a href="{{ route('articles.show', $related->slug) }}" class="group">
                            <div class="bg-gray-50 rounded-lg overflow-hidden h-32 mb-3">
                                @if($related->thumbnail)
                                    <img src="{{ asset('storage/' . $related->thumbnail) }}" class="w-full h-full object-cover group-hover:opacity-90 transition">
                                @else
                                    <div class="w-full h-full bg-gray-200"></div>
                                @endif
                            </div>
                            <h4 class="font-bold text-gray-900 group-hover:text-indigo-600 transition">{{ $related->title }}</h4>
                        </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-layout>