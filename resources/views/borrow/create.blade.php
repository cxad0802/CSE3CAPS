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
                <a href="/catalog" class="hover:text-white transition-colors">Equipment Catalog</a>
                <a href="#" class="text-white font-bold transition-colors">My Requests</a>
            </nav>
        </div>
        <div class="flex items-center gap-3 text-sm">
            <span class="text-gray-300">Student ID: <strong class="text-white">22014567</strong></span>
            <div class="w-8 h-8 rounded-full bg-red-700 flex items-center justify-center font-bold ml-2">S</div>
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
                
                <h2 class="text-xl font-bold text-gray-900 mb-2">MacBook Pro 14" (M2 Pro)</h2>
                <div class="space-y-3 text-sm text-gray-600 mt-4">
                    <div class="flex justify-between border-b border-gray-200 pb-2">
                        <span>Asset ID</span>
                        <span class="font-medium text-gray-900">IT-LPT-8821</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-200 pb-2">
                        <span>Category</span>
                        <span class="font-medium text-gray-900">Standard Laptops</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-200 pb-2">
                        <span>Max Loan Period</span>
                        <span class="font-medium text-gray-900">14 Days</span>
                    </div>
                    <div class="flex justify-between pt-1">
                        <span>Availability</span>
                        <span class="font-medium text-teal-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Ready to borrow
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Side: Request Form -->
            <div class="md:w-3/5 p-8">
                <form action="#" method="POST" class="space-y-6">
                    <!-- Date Selection -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Pickup Date</label>
                            <input type="date" id="pickup_date" name="pickup_date" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-600 focus:border-red-600 block p-3 outline-none transition-colors" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Return Date</label>
                            <input type="date" id="return_date" name="return_date" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-600 focus:border-red-600 block p-3 outline-none transition-colors disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed" disabled required>
                        </div>
                    </div>

                    <!-- Usage Purpose -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Purpose of Loan</label>
                        <select class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-600 focus:border-red-600 block p-3 outline-none transition-colors">
                            <option value="" disabled selected>Select a purpose...</option>
                            <option value="assignment">Class Assignment / Project</option>
                            <option value="research">Academic Research</option>
                            <option value="event">University Event</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Additional Details -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Additional Details (Optional)</label>
                        <textarea rows="3" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-600 focus:border-red-600 block p-3 outline-none transition-colors" placeholder="Mention specific project names, software requirements, or accessories needed..."></textarea>
                    </div>

                    <!-- Terms Agreement -->
                    <div class="flex items-start mt-6">
                        <div class="flex items-center h-5">
                            <input type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-red-300 text-red-600" required>
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

    <!-- JavaScript for Date Validation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pickupInput = document.getElementById('pickup_date');
            const returnInput = document.getElementById('return_date');
            
            // Prevent selecting past dates for pickup
            const today = new Date().toISOString().split('T')[0];
            pickupInput.min = today;

            pickupInput.addEventListener('change', function() {
                if (this.value) {
                    // Enable return date and set its minimum allowable date to the pickup date
                    returnInput.disabled = false;
                    returnInput.min = this.value;
                    
                    // Auto-correct return date if it's currently earlier than the new pickup date
                    if (returnInput.value && returnInput.value < this.value) {
                        returnInput.value = this.value;
                    }
                } else {
                    // Reset and disable if pickup date is cleared
                    returnInput.disabled = true;
                    returnInput.value = '';
                }
            });
        });
    </script>
</body>
</html>