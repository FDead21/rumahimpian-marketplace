@if(session('success') || session('error'))
    <div x-data="{ open: true }"
         x-show="open"
         class="fixed inset-0 z-[150] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/60 backdrop-blur-sm p-4"
         aria-labelledby="modal-title" role="dialog" aria-modal="true"
         style="display: none;">

        {{-- The Modal Box --}}
        <div x-show="open"
             @click.away="open = false"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             class="relative transform overflow-hidden rounded-3xl bg-white p-8 text-center shadow-2xl transition-all w-full max-w-sm border border-gray-100">

            @if(session('success'))
                {{-- Success Theme --}}
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-green-50 mb-6 ring-8 ring-green-50/50">
                    <svg class="h-10 w-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-2xl font-extrabold text-gray-900 mb-2">{{ __('Success!') }}</h3>
                <p class="text-gray-500 mb-8 leading-relaxed">{{ session('success') }}</p>
                <button @click="open = false" class="w-full rounded-xl bg-sky-600 px-4 py-3.5 text-sm font-bold text-white shadow-md hover:bg-sky-700 transition transform hover:-translate-y-0.5">
                    {{ __('Great, thanks!') }}
                </button>
            @endif

            @if(session('error'))
                {{-- Error Theme --}}
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6 ring-8 ring-red-50/50">
                    <svg class="h-10 w-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <h3 class="text-2xl font-extrabold text-gray-900 mb-2">{{ __('Oops!') }}</h3>
                <p class="text-gray-500 mb-8 leading-relaxed">{{ session('error') }}</p>
                <button @click="open = false" class="w-full rounded-xl bg-red-600 px-4 py-3.5 text-sm font-bold text-white shadow-md hover:bg-red-700 transition transform hover:-translate-y-0.5">
                    {{ __('Try Again') }}
                </button>
            @endif

        </div>
    </div>
@endif