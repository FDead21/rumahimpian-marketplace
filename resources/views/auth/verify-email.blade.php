<x-layout title="Verify Email">
    <div class="min-h-[60vh] flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-lg text-center">
            
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100">
                <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>

            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Check your email
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                We sent a verification link to <strong>{{ auth()->user()->email }}</strong>.<br>
                Click the link to get your <span class="text-blue-600 font-bold">Blue Verified Badge</span>!
            </p>

            <form method="POST" action="{{ route('verification.send') }}" class="mt-8">
                @csrf
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                    Resend Verification Email
                </button>
            </form>

            @if (session('message'))
                <div class="mt-4 p-2 bg-green-100 text-green-700 text-sm rounded">
                    {{ session('message') }}
                </div>
            @endif
            
            <div class="mt-4">
                <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:underline">Skip for now</a>
            </div>
        </div>
    </div>
</x-layout>