<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $agent->name }} - {{ __('Agent Profile') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    @include('components.navbar')

    <div class="bg-indigo-900 text-white">
        <div class="max-w-4xl mx-auto px-4 py-16 text-center">
            
            <div class="w-24 h-24 bg-white rounded-full mx-auto mb-4 flex items-center justify-center text-indigo-900 font-bold text-3xl border-4 border-indigo-200">
                {{ substr($agent->name, 0, 1) }}
            </div>

            <h1 class="text-3xl font-bold mb-2">{{ $agent->name }}</h1>
            <div class="flex items-center justify-center gap-1"> 
                <div class="font-bold text-gray-900 text-lg">{{ $agent->name }}</div> 
                
                @if($agent->is_verified)
                    <svg class="w-5 h-5 text-blue-500 fill-current" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15l-4-4 1.41-1.41L11 14.17l6.59-6.59L19 9l-8 8z" fill="currentColor" stroke="none"/>
                    </svg>
                    <span class="text-[10px] bg-blue-100 text-blue-700 px-1 rounded border border-blue-200">{{ __('Verified') }}</span>
                @endif
            </div>
            
            @if($agent->agency)
                <a href="{{ route('agency.show', $agent->agency->slug) }}" class="inline-block bg-indigo-800 hover:bg-indigo-700 px-4 py-1 rounded-full text-sm font-medium transition mb-4">
                    🏢 {{ $agent->agency->name }}
                </a>
            @else
                <span class="inline-block bg-indigo-800 px-4 py-1 rounded-full text-sm font-medium mb-4">
                    {{ __('Freelance Agent') }}
                </span>
            @endif

            <div class="flex justify-center gap-4 text-indigo-200 text-sm">
                <span>{{ __('Joined') }} {{ $agent->created_at->format('M Y') }}</span>
                <span>•</span>
                <span>{{ $properties->total() }} {{ __('Active Listings') }}</span>
            </div>

            <a href="https://wa.me/{{ $agent->phone_number }}" target="_blank" class="inline-flex items-center gap-2 mt-6 bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-bold transition">
                <span>{{ __('Chat on WhatsApp') }}</span>
            </a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12 flex-grow w-full">
        <h2 class="text-xl font-bold text-gray-900 mb-6">{{ __('Active Listings') }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($properties as $property)
                <x-property-card :property="$property" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $properties->links() }}
        </div>
    </div>

    @include('components.footer')
</body>
</html>