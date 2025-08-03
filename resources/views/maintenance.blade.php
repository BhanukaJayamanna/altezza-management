<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ Setting::getAppName() }} - Maintenance Mode</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="text-center">
        <div class="mb-8">
            <svg class="mx-auto h-24 w-24 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
        </div>
        
        <h1 class="text-4xl font-bold text-gray-900 mb-4">System Maintenance</h1>
        
        <p class="text-xl text-gray-600 mb-8 max-w-lg mx-auto">
            {{ $message }}
        </p>
        
        <div class="text-sm text-gray-500">
            <p>We apologize for any inconvenience.</p>
            <p>Please check back shortly.</p>
        </div>
        
        <div class="mt-8">
            <button onclick="location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                Refresh Page
            </button>
        </div>
    </div>
</body>
</html>
