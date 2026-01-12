<x-layout>
    
    <div class="relative bg-indigo-900 py-20">
        <div class="absolute inset-0 overflow-hidden opacity-20">
            <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1920&q=80" class="w-full h-full object-cover">
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-4">
                Find Your Dream Home
            </h1>
            <p class="text-xl text-indigo-200 mb-8 max-w-2xl">
                Search thousands of properties for sale and rent from top agents.
            </p>

            <div class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl p-6">
                <form action="{{ route('home') }}" method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-4 relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                class="block w-full pl-4 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" 
                                placeholder="Search by City, District, or Property Name...">
                        </div>
                        <div class="md:col-span-1">
                            <select name="type" class="block w-full py-3 px-3 border border-gray-300 rounded-lg">
                                <option value="">All Types</option>
                                <option value="House">House</option>
                                <option value="Apartment">Apartment</option>
                                <option value="Villa">Villa</option>
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <select name="listing_type" class="block w-full py-3 px-3 border border-gray-300 rounded-lg">
                                <option value="">Buy / Rent</option>
                                <option value="SALE">For Sale</option>
                                <option value="RENT">For Rent</option>
                            </select>
                        </div>
                         <div class="md:col-span-1">
                            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition">
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Latest Listings</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($properties as $property)
                <x-property-card :property="$property" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $properties->links() }}
        </div>
    </div>

</x-layout>