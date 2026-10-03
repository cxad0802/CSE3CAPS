<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniBorrow IT - Student</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased flex flex-col min-h-screen">

    <!-- Top Navbar -->
    <header class="bg-[#1c1c1c] text-white h-16 flex items-center justify-between px-8 shadow-md z-40">
        <div class="flex items-center gap-10">
            <!-- Logo (Luôn hiển thị) -->
            <div class="flex items-center gap-2 cursor-pointer" onclick="window.location.href='/catalog'">
                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                <span class="font-bold text-lg tracking-wide">UniBorrow IT</span>
            </div>
            
            <!-- Điều hướng (Ẩn ở trang login) -->
            @if(!request()->is('login'))
            <nav class="hidden md:flex gap-6 text-sm text-gray-300 font-medium">
                <a href="/catalog" class="hover:text-white transition-colors">Equipment Catalog</a>
                <a href="/my-requests" class="hover:text-white transition-colors">My Requests</a>
            </nav>
            @endif
        </div>
        
        <!-- Cụm thông tin User (Ẩn ở trang login) -->
        @if(!request()->is('login'))
        <div class="flex items-center gap-4 text-sm">
            <span class="text-gray-300 hidden md:inline">Student ID: <strong class="text-white">22244263</strong></span>
            
            <!-- User Dropdown Menu -->
            <div class="relative user-menu-container">
                <button onclick="toggleDropdown('studentDropdown')" class="flex items-center gap-2 focus:outline-none hover:opacity-80 transition-opacity">
                    <div class="w-8 h-8 rounded-full bg-red-700 flex items-center justify-center font-bold text-white">S</div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <!-- Dropdown Content -->
                <div id="studentDropdown" class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-xl z-50 overflow-hidden">
                    <div class="py-1">
                        <a href="#" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-red-600 transition-colors">Profile</a>
                        <a href="#" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-red-600 transition-colors">Settings</a>
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

    <!-- Main Content Area -->
    <main class="flex-1 w-full">
        @yield('content')
    </main>

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
                <a href="/login" class="px-5 py-2.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors w-1/2 text-center block">
                    Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Layout Scripts -->
    <script>
        function toggleDropdown(menuId) {
            document.getElementById(menuId).classList.toggle('hidden');
        }

        function openLogoutModal() {
            document.getElementById('logoutModal').classList.remove('hidden');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function(e) {
            if (!e.target.closest('.user-menu-container')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });
    </script>
</body>
</html>