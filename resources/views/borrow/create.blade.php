<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Equipment - UniBorrow IT</title>
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
                <a href="{{ route('requests.mine') }}" class="hover:text-white transition-colors">My Requests</a>
            </nav>
        </div>
        <div class="flex items-center gap-3 text-sm">
            <span class="text-gray-300">{{ session('firebase.email') }}</span>
            <div class="w-8 h-8 rounded-full bg-red-700 flex items-center justify-center font-bold ml-2">{{ strtoupper(substr(session('firebase.displayName', '?'), 0, 1)) }}</div>
        </div>
    </header>

    <!-- Main Form Area -->
    <main class="flex-1 max-w-5xl mx-auto w-full px-6 py-12">
        
        <div class="mb-8">
            <a href="/catalog" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-red-700 mb-4 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Catalog
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Borrow Request</h1>
            <p class="text-gray-500 mt-1">Please provide details for your equipment loan.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col md:flex-row">
            
            <!-- Left Side: Equipment Summary -->
            <div class="bg-gray-50 md:w-2/5 p-8 border-b md:border-b-0 md:border-r border-gray-200">
                <div class="uppercase tracking-wider text-xs font-bold text-gray-500 mb-4">Selected Item</div>
                
                <div class="w-full h-48 bg-white border border-gray-200 rounded-lg flex items-center justify-center mb-6">
                    <!-- Placeholder for Equipment Image -->
                    <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                
                <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $equipment->name }}</h2>
                @if ($equipment->description)
                    <p class="text-sm text-gray-600">{{ $equipment->description }}</p>
                @endif
                <div class="space-y-3 text-sm text-gray-600 mt-4">
                    <div class="flex justify-between border-b border-gray-200 pb-2">
                        <span>Category</span>
                        <span class="font-medium text-gray-900">{{ $equipment->categoryLabel() }}</span>
                    </div>
                    @if ($equipment->location)
                        <div class="flex justify-between border-b border-gray-200 pb-2">
                            <span>Pick up at</span>
                            <span class="font-medium text-gray-900">{{ $equipment->location }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between border-b border-gray-200 pb-2">
                        <span>In stock</span>
                        <span class="font-medium text-gray-900">{{ $equipment->availableQuantity }} of {{ $equipment->totalQuantity }}</span>
                    </div>
                    <div class="flex justify-between pt-1">
                        <span>Availability</span>
                        @if ($equipment->isBorrowable())
                            <span class="font-medium text-teal-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Ready to borrow
                            </span>
                        @else
                            <span class="font-medium text-gray-500">Currently unavailable</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Side: Request Form -->
            <div class="md:w-3/5 p-8">
                <form action="{{ route('borrow.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">

                    @if ($errors->any())
                        <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Date Selection -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="borrow_from" class="block text-sm font-bold text-gray-700 mb-2">Pickup Date</label>
                            <input id="borrow_from" name="borrow_from" type="date" value="{{ old('borrow_from') }}" min="{{ now()->toDateString() }}" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-600 focus:border-red-600 block p-3 outline-none transition-colors" required>
                        </div>
                        <div>
                            <label for="borrow_to" class="block text-sm font-bold text-gray-700 mb-2">Return Date</label>
                            <input id="borrow_to" name="borrow_to" type="date" value="{{ old('borrow_to') }}" min="{{ now()->addDay()->toDateString() }}" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-600 focus:border-red-600 block p-3 outline-none transition-colors" required>
                        </div>
                    </div>

                    <!-- Usage Purpose -->
                    <div>
                        <label for="purpose" class="block text-sm font-bold text-gray-700 mb-2">Purpose of Loan</label>
                        <select id="purpose" name="purpose" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-600 focus:border-red-600 block p-3 outline-none transition-colors" required>
                            <option value="" disabled @selected(! old('purpose'))>Select a purpose...</option>
                            @foreach ($purposes as $value => $label)
                                <option value="{{ $value }}" @selected(old('purpose') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Additional Details -->
                    <div>
                        <label for="details" class="block text-sm font-bold text-gray-700 mb-2">Additional Details (Optional)</label>
                        <textarea id="details" name="details" rows="3" maxlength="500" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-600 focus:border-red-600 block p-3 outline-none transition-colors" placeholder="Mention specific project names, software requirements, or accessories needed...">{{ old('details') }}</textarea>
                    </div>

                    <!-- Terms Agreement -->
                    <div class="flex items-start mt-6">
                        <div class="flex items-center h-5">
                            <input id="terms" name="terms" type="checkbox" value="1" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-red-300 text-red-600" required>
                        </div>
                        <label class="ml-2 text-sm font-medium text-gray-600">
                            I agree to the <a href="#" class="text-red-700 hover:underline">Equipment Borrowing Policy</a> and accept responsibility for loss or damage.
                        </label>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-6 border-t border-gray-200 flex justify-end gap-3">
                        <button type="button" onclick="window.location.href='/catalog'" class="px-5 py-2.5 text-sm font-bold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 transition-colors flex items-center gap-2">
                            Submit Request
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

</body>
</html>