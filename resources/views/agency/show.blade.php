<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $agency->name }} - {{ __('Real Estate Office') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    @include('components.navbar')

    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-12 flex flex-col md:flex-row items-center gap-8">
            
            <div class="w-32 h-32 bg-gray-100 rounded-2xl border flex items-center justify-center overflow-hidden flex-shrink-0">
                @if($agency->logo)
                    <img src="{{ asset('storage/' . $agency->logo) }}" class="w-full h-full object-contain p-2">
                @else
                    <span class="text-4xl font-bold text-gray-300">{{ substr($agency->name, 0, 1) }}</span>
                @endif
            </div>

            <div class="text-center md:text-left flex-grow">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $agency->name }}</h1>
                <div class="space-y-1 text-gray-600">
                    <p class="flex items-center justify-center md:justify-start gap-2">
                        📍 {{ $agency->address ?? 'Indonesia' }}
                    </p>
                    <p class="flex items-center justify-center md:justify-start gap-2">
                        📞 {{ $agency->phone ?? __('Contact Agent') }}
                    </p>
                    <p class="flex items-center justify-center md:justify-start gap-2 text-indigo-600 font-bold">
                        👥 {{ $agency->agents->count() }} {{ __('Agents Registered') }}
                    </p>
                </div>
            </div>

        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12 flex-grow w-full">
        <h2 class="text-xl font-bold text-gray-900 mb-6 border-l-4 border-indigo-600 pl-4">
            {{ __('Properties by') }} {{ $agency->name }}
        </h2>

        @if($properties->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($properties as $property)
                    <x-property-card :property="$property" />
                @endforeach
            </div>
            
            <div class="mt-8">
                {{ $properties->links() }}
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                <p class="text-gray-500">{{ __('No active listings found for this agency.') }}</p>
            </div>
        @endif
    </div>

    @include('components.footer')
</body>
</html>