<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <title>{{ __('Register as Agent') }} - RumahImpian</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-indigo-600 mb-6">{{ __('Join as Agent') }}</h2>

        <form action="/agent/register" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Full Name') }}</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Email Address') }}</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('WhatsApp Number') }}</label>
                <input type="text" name="phone_number" placeholder="0812..." required class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Confirm Password') }}</label>
                <input type="password" name="password_confirmation" required class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-md transition">
                {{ __('Register & Login') }}
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-4">
            {{ __('Already have an account?') }} <a href="/admin/login" class="text-indigo-600 font-bold">{{ __('Login here') }}</a>
        </p>
    </div>

</body>
</html>