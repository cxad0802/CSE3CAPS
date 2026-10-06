<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniBorrow IT - Student</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased flex flex-col min-h-screen relative">

    <!-- Top Navbar -->
    <header class="bg-[#1c1c1c] text-white h-16 flex items-center justify-between px-4 md:px-8 shadow-md z-40 relative">
        <div class="flex items-center gap-4 md:gap-10">
            
            <!-- Mobile Hamburger Button -->
            <!-- Note: Visible only on mobile screens (md:hidden) -->
            @if(!request()->is('login'))
            <button onclick="toggleMobileNav('studentMobileNav')" class="md:hidden text-white hover:text-gray-300 focus:outline-none p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            @endif

            <!-- Logo -->
            <div class="flex items-center gap-2 cursor-pointer" onclick="window.location.href='/catalog'">
                <svg class="w-5 h-5 text-red-600 hidden md:block" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                <span class="font-bold text-lg tracking-wide">UniBorrow IT</span>
            </div>
            
            <!-- Desktop Navigation -->
            @if(!request()->is('login'))
            <nav class="hidden md:flex gap-6 text-sm text-gray-300 font-medium">
                <a href="/catalog" class="hover:text-white transition-colors">Equipment Catalog</a>
                <a href="/my-requests" class="hover:text-white transition-colors">My Requests</a>
            </nav>
            @endif
        </div>
        
        <!-- User Info Section -->
        @if(!request()->is('login'))
        <div class="flex items-center gap-3 md:gap-4 text-sm">
            <!-- Student ID Display -->
            <span class="text-gray-300 hidden sm:inline">Student ID: <strong class="text-white">22244263</strong></span>
            
            <div class="relative user-menu-container">
                <button onclick="toggleDropdown('studentDropdown')" class="flex items-center gap-1 md:gap-2 focus:outline-none hover:opacity-80 transition-opacity">
                    <div class="w-8 h-8 rounded-full bg-red-700 flex items-center justify-center font-bold text-white text-xs md:text-sm">
                        S
                    </div>
                    <svg class="w-4 h-4 text-gray-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <!-- Dropdown Content -->
                <div id="studentDropdown" class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-xl z-50 overflow-hidden">
                    <div class="py-1">
                        <a href="#" onclick="alert('This feature will be developed in the future.'); return false;" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-red-600 transition-colors">Profile</a>
                        <a href="#" onclick="alert('This feature will be developed in the future.'); return false;" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-red-600 transition-colors">Settings</a>
                        <hr class="border-gray-200 my-1">
                        <button onclick="openLogoutModal(); toggleDropdown('studentDropdown')" class="w-full text-left block px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                            Log out
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </header>

    <!-- Mobile Navigation Dropdown Menu -->
    <!-- Note: Toggled via hamburger button on mobile devices -->
    @if(!request()->is('login'))
    <div id="studentMobileNav" class="dropdown-menu hidden md:hidden bg-[#2a2a2a] text-white w-full absolute top-16 left-0 z-30 shadow-lg border-t border-gray-700">
        <div class="px-4 py-2 space-y-1">
            <a href="/catalog" class="block px-3 py-3 rounded-md text-base font-medium hover:bg-[#3a3a3a] transition-colors">Equipment Catalog</a>
            <a href="/my-requests" class="block px-3 py-3 rounded-md text-base font-medium hover:bg-[#3a3a3a] transition-colors">My Requests</a>
        </div>
    </div>
    @endif

    <!-- Main Content Area -->
    <main class="flex-1 w-full flex flex-col">
        @yield('content')
    </main>

    <!-- Global Footer -->
    @if(!request()->is('login'))
    <footer class="bg-gray-100 p-8 mt-auto border-t border-gray-200 grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="md:col-span-1">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                <span class="font-bold text-gray-900">UniBorrow IT</span>
            </div>
            <p class="text-xs text-gray-500">© 2026 University IT Services. All rights reserved.</p>
        </div>
        
        <div>
            <h4 class="text-xs font-bold text-gray-900 uppercase mb-3">Institutional</h4>
            <ul class="text-xs text-gray-500 space-y-2">
                <li><a href="#" onclick="alert('This feature will be developed in the future.'); return false;" class="hover:text-gray-900 transition-colors">Institutional Links</a></li>
                <li><a href="#" onclick="alert('This feature will be developed in the future.'); return false;" class="hover:text-gray-900 transition-colors">Accessibility</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-xs font-bold text-gray-900 uppercase mb-3">Support</h4>
            <ul class="text-xs text-gray-500 space-y-2">
                <li><a href="#" onclick="alert('This feature will be developed in the future.'); return false;" class="hover:text-gray-900 transition-colors">Contact Info</a></li>
                <li><a href="#" onclick="alert('This feature will be developed in the future.'); return false;" class="hover:text-gray-900 transition-colors">Privacy Policy</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-xs font-bold text-gray-900 uppercase mb-3">Connect</h4>
            <div class="flex gap-4 text-gray-500">
                <a href="#" onclick="alert('This feature will be developed in the future.'); return false;" class="hover:text-gray-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                </a>
                <a href="#" onclick="alert('This feature will be developed in the future.'); return false;" class="hover:text-gray-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </a>
            </div>
        </div>
    </footer>
    @endif

    <!-- Logout Modal -->
    <div id="logoutModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl p-8 max-w-sm w-full mx-4 transform transition-all">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="text-center">
                <h3 class="text-xl font-bold text-gray-900 mb-2">You will be logged out</h3>
                <p class="text-sm text-gray-500">Are you sure you want to end your current session?</p>
            </div>
            <div class="mt-8 flex justify-center gap-3">
                <button onclick="closeLogoutModal()" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors w-1/2">
                    Cancel
                </button>
                <form action="/logout" method="POST" class="w-1/2">
                    @csrf
                    <button type="submit" class="w-full px-5 py-2.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors text-center block">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Script for handling Dropdowns and Modals -->
    <script>
        // Note: Function to toggle dropdown menus based on element ID
        function toggleDropdown(menuId) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if(menu.id !== menuId) menu.classList.add('hidden');
            });
            document.getElementById(menuId).classList.toggle('hidden');
        }

        // Note: Function specific to mobile hamburger navigation
        function toggleMobileNav(navId) {
            document.getElementById(navId).classList.toggle('hidden');
        }

        function openLogoutModal() {
            document.getElementById('logoutModal').classList.remove('hidden');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
        }

        // Note: Close dropdowns if user clicks outside of them
        window.addEventListener('click', function(e) {
            if (!e.target.closest('.user-menu-container') && !e.target.closest('button[onclick*="toggleMobileNav"]')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });
    </script>
</body>
</html>