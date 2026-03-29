<div class="bg-slate-800 rounded-xl overflow-hidden border border-slate-700 hover:border-sky-500 transition group relative">
    {{-- Note: Check if your web.php uses 'rental.show' or 'rental.vehicle.show' --}}
    <a href="{{ route('rental.vehicles.show', $vehicle->slug ?? 'slug') }}" class="block h-36 bg-slate-700 relative overflow-hidden">
        @if($vehicle->coverImage())
            <img src="{{ asset('storage/' . $vehicle->coverImage()) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
        @else
            <div class="w-full h-full flex items-center justify-center text-3xl opacity-50">
                {{ $vehicle->vehicle_type === 'CAR' ? '' : ($vehicle->vehicle_type === 'MOTORBIKE' ? '' : '') }}
            </div>
        @endif
    </a>
    <div class="p-4">
        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">{{ $vehicle->brand ?? 'Brand' }}</p>
        <a href="{{ route('rental.vehicles.show', $vehicle->slug ?? 'slug') }}">
            <h4 class="font-bold text-white truncate group-hover:text-sky-400 transition">{{ $vehicle->name }}</h4>
        </a>
        <div class="flex items-center justify-between mt-3 border-t border-slate-700 pt-3">
            <div class="flex items-center gap-1 text-xs text-slate-400">
                <span></span> {{ $vehicle->max_passengers ?? 2 }}
            </div>
            <p class="text-sky-400 font-bold text-sm">Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}<span class="text-[9px] text-slate-500 font-normal">/day</span></p>
        </div>
    </div>
</div>