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
                <a href="{{ route('catalog') }}" class="hover:text-white transition-colors">Equipment Catalog</a>
                <a href="{{ route('requests.mine') }}" class="text-white font-bold transition-colors">My Requests</a>
            </nav>
        </div>
        <div class="flex items-center gap-3 text-sm">
            <span class="text-gray-300">{{ session('firebase.email') }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-gray-300 hover:text-white transition-colors">Logout</button>
            </form>
            <div class="w-8 h-8 rounded-full bg-red-700 flex items-center justify-center font-bold ml-2">{{ strtoupper(substr(session('firebase.displayName', '?'), 0, 1)) }}</div>
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
            <form method="GET" action="{{ route('requests.mine') }}">
                <select name="filter" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block p-2.5 outline-none shadow-sm">
                    <option value="all" @selected($filter === 'all')>All Requests</option>
                    <option value="active" @selected($filter === 'active')>Active &amp; Pending</option>
                    <option value="returned" @selected($filter === 'returned')>Returned</option>
                </select>
            </form>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Requests List -->
        <div class="space-y-4">

            @forelse ($requests as $borrowRequest)
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full {{ $borrowRequest->accentClasses() }}"></div>
                    <div class="flex items-start gap-4">
                        <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $borrowRequest->statusClasses() }}">
                                    {{ $borrowRequest->statusLabel() }}
                                </span>
                                <span class="text-xs text-gray-400 font-medium">Req ID: {{ $borrowRequest->reference() }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $borrowRequest->equipmentName }}</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Requested for: {{ $borrowRequest->period() }}
                                @if ($borrowRequest->days())
                                    ({{ $borrowRequest->days() }} {{ Str::plural('day', $borrowRequest->days()) }})
                                @endif
                            </p>
                            <p class="text-sm text-gray-500 mt-1">{{ $borrowRequest->purpose }}</p>
                            @if ($borrowRequest->reviewNote)
                                <p class="text-sm text-red-600 mt-1">Reviewer note: {{ $borrowRequest->reviewNote }}</p>
                            @endif
                        </div>
                    </div>
                    @if ($borrowRequest->isCancellable())
                        <div class="flex items-center gap-3">
                            <form action="{{ route('requests.cancel', $borrowRequest->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                    Cancel Request
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white border border-dashed border-gray-300 rounded-xl p-12 text-center">
                    <p class="text-gray-500">You have not requested any equipment yet.</p>
                    <a href="{{ route('catalog') }}" class="inline-block mt-4 px-5 py-2.5 text-sm font-bold text-white bg-red-700 rounded-lg hover:bg-red-800 transition-colors">
                        Browse the catalogue
                    </a>
                </div>
            @endforelse

        </div>
    </main>

</body>
</html>
