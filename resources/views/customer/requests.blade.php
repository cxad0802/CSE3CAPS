<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Requests - UniBorrow IT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased flex flex-col min-h-screen">

    <!-- Top Navbar (Customer Style) -->
    <header class="bg-[#1c1c1c] text-white h-16 flex items-center justify-between px-8 shadow-md">
        <div class="flex items-center gap-10">
            <div class="flex items-center gap-2 cursor-pointer" onclick="window.location.href='/catalog'">
                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                <span class="font-bold text-lg tracking-wide">UniBorrow IT</span>
            </div>
            <nav class="hidden md:flex gap-6 text-sm text-gray-300 font-medium">
                <a href="/catalog" class="hover:text-white transition-colors">Equipment Catalog</a>
                <a href="/my-requests" class="text-white font-bold transition-colors">My Requests</a>
            </nav>
        </div>
        <div class="flex items-center gap-3 text-sm">
            <span class="text-gray-300">Student ID: <strong class="text-white">22014567</strong></span>
            <div class="w-8 h-8 rounded-full bg-red-700 flex items-center justify-center font-bold ml-2">S</div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-5xl mx-auto w-full px-6 py-10">
        
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Requests</h1>
                <p class="text-gray-500 mt-1">Track the status of your borrowed equipment and history.</p>
            </div>
            <!-- Simple Filter -->
            <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block p-2.5 outline-none shadow-sm">
                <option value="all">All Requests</option>
                <option value="active">Active & Pending</option>
                <option value="returned">Returned</option>
            </select>
        </div>

        <!-- Requests List -->
        <div class="space-y-4">
            
            <!-- Pending Request Card -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-yellow-400"></div>
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pending Approval
                            </span>
                            <span class="text-xs text-gray-400 font-medium">Req ID: #REQ-0192</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">MacBook Pro 14" (M2 Pro)</h3>
                        <p class="text-sm text-gray-500 mt-1">Requested for: Sep 20 - Sep 27 (7 Days)</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                        Cancel Request
                    </button>
                </div>
            </div>

            <!-- Active Loan Card -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-green-500"></div>
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                Active Loan
                            </span>
                            <span class="text-xs text-gray-400 font-medium">Asset: IT-CAM-2210</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Sony A7III Camera Kit</h3>
                        <p class="text-sm text-gray-500 mt-1">Due in: <span class="font-bold text-red-600">2 Days</span> (Return by Sep 22)</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg shadow-sm transition-colors">
                        Return Instructions
                    </button>
                </div>
            </div>

            <!-- Returned (History) Card -->
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 relative overflow-hidden opacity-75">
                <div class="absolute top-0 left-0 w-1 h-full bg-gray-400"></div>
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 bg-white border border-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-800">
                                Returned
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Epson Projector G-Series</h3>
                        <p class="text-sm text-gray-500 mt-1">Borrowed: Aug 10 - Aug 12 (Returned on time)</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-red-700 transition-colors">
                        Borrow Again
                    </button>
                </div>
            </div>

        </div>
    </main>

</body>
</html>