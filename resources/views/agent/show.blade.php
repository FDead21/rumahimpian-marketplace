<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $agent->name }} - Agent Profile</title>
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
            
            @if($agent->agency)
                <a href="{{ route('agency.show', $agent->agency->slug) }}" class="inline-block bg-indigo-800 hover:bg-indigo-700 px-4 py-1 rounded-full text-sm font-medium transition mb-4">
                    🏢 {{ $agent->agency->name }}
                </a>
            @else
                <span class="inline-block bg-indigo-800 px-4 py-1 rounded-full text-sm font-medium mb-4">
                    Freelance Agent
                </span>
            @endif

            <div class="flex justify-center gap-4 text-indigo-200 text-sm">
                <span>Joined {{ $agent->created_at->format('M Y') }}</span>
                <span>•</span>
                <span>{{ $properties->total() }} Active Listings</span>
            </div>

            <a href="https://wa.me/{{ $agent->phone_number }}" target="_blank" class="inline-flex items-center gap-2 mt-6 bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-bold transition">
                <span>Chat on WhatsApp</span>
            </a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12 flex-grow w-full">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Active Listings</h2>

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