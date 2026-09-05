<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniBorrow IT</title>
    <!-- Connect to Vite for CSS/JS compilation -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    
    <!-- Top Navigation Bar -->
    <nav class="bg-red-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Back to home button -->
                    <a href="/catalog" class="flex-shrink-0 flex items-center text-white gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span class="font-bold text-xl">UniBorrow IT</span>
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-red-100 text-sm">Welcome, Customer</span>
                    <a href="/login" class="text-white hover:text-red-200 text-sm font-medium">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main content area -->
    <main class="flex-grow flex items-center justify-center">
        @yield('content')
    </main>

    <!-- Common Footer -->
    <footer class="bg-white border-t border-gray-200 py-8 mt-auto">
        <div class="max-w-6xl mx-auto grid grid-cols-4 gap-8 text-sm px-4">
            <div>
                <h4 class="font-bold mb-2">UniBorrow IT</h4>
                <p class="text-gray-600">Institutional Equipment Allocation System.</p>
            </div>
            <div>
                <h4 class="font-bold mb-2">Support</h4>
                <ul class="text-gray-600 space-y-1">
                    <li><a href="#" class="hover:text-red-600">Help Center</a></li>
                    <li><a href="#" class="hover:text-red-600">Contact IT</a></li>
                    <li><a href="#" class="hover:text-red-600">System Status</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-2">Legal</h4>
                <ul class="text-gray-600 space-y-1">
                    <li><a href="#" class="hover:text-red-600">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-red-600">Terms of Service</a></li>
                    <li><a href="#" class="hover:text-red-600">Accessibility</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-2">Connect</h4>
                <ul class="text-gray-600 space-y-1">
                    <li><a href="#" class="hover:text-red-600">University Portal</a></li>
                    <li><a href="#" class="hover:text-red-600">Staff Directory</a></li>
                </ul>
            </div>
        </div>
    </footer>
</body>
</html>