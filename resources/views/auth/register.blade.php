<!DOCTYPE html>
<html lang="en">
<head>
    <title>Agent Registration - RumahImpian</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-indigo-600 mb-6">Join as an Agent</h2>

        <form action="/agent/register" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">WhatsApp Number</label>
                <input type="text" name="phone_number" placeholder="0812..." required class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-md transition">
                Register & Login
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-4">
            Already have an account? <a href="/admin/login" class="text-indigo-600 font-bold">Login here</a>
        </p>
    </div>

</body>
</html>