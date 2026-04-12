<div class="space-y-4 py-4">
    <div class="flex justify-between items-start">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
            <p class="text-sm text-gray-500">{{ $date }}</p>
        </div>
        <div class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
            {{ $status }}
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100 dark:border-gray-800">
        <div>
            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-tighter">Customer</p>
            <p class="text-sm font-semibold dark:text-white">{{ $client }}</p>
        </div>
        <div>
            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-tighter">Total Price</p>
            <p class="text-sm font-extrabold text-emerald-600">Rp {{ $price }}</p>
        </div>
    </div>
</div>