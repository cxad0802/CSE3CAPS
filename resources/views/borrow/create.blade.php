<!-- Note: Extend the master student layout to inherit the navbar, hamburger menu, and footer -->
@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 md:px-8 py-10">
    
    <!-- Back Link -->
    <a href="/catalog" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-red-600 transition-colors mb-6">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Catalog
    </a>

    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Borrow Request</h1>
        <p class="text-gray-500 text-sm">Please provide details for your equipment loan.</p>
    </div>

    <!-- Request Form Layout -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col md:flex-row">
        
        <!-- Left Side: Equipment Details -->
        <div class="w-full md:w-1/3 bg-gray-50 p-6 md:p-8 border-b md:border-b-0 md:border-r border-gray-200">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 block">Selected Item</span>
            
            <div class="h-40 bg-white border border-gray-200 rounded-lg flex items-center justify-center mb-6">
                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>

            <h3 class="text-xl font-bold text-gray-900 mb-4">MacBook Pro 14" (M2 Pro)</h3>
            
            <div class="space-y-3 text-sm">
                <div class="flex justify-between border-b border-gray-200 pb-2">
                    <span class="text-gray-500">Asset ID</span>
                    <span class="font-medium text-gray-900">IT-LPT-8821</span>
                </div>
                <div class="flex justify-between border-b border-gray-200 pb-2">
                    <span class="text-gray-500">Category</span>
                    <span class="font-medium text-gray-900">Standard Laptops</span>
                </div>
                <div class="flex justify-between border-b border-gray-200 pb-2">
                    <span class="text-gray-500">Max Loan Period</span>
                    <span class="font-medium text-gray-900">14 Days</span>
                </div>
                <div class="flex justify-between pt-1">
                    <span class="text-gray-500">Availability</span>
                    <span class="font-medium text-green-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Ready to borrow
                    </span>
                </div>
            </div>
        </div>

        <!-- Right Side: Form Inputs -->
        <div class="w-full md:w-2/3 p-6 md:p-8">
            <!-- Note: Action updated to static route for Direct UI testing -->
            <form action="/borrow/request" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Pickup Date</label>
                        <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Return Date</label>
                        <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-500" readonly>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-900 mb-2">Purpose of Loan</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500" required>
                        <option value="">Select a purpose...</option>
                        <option value="assignment">Course Assignment / Project</option>
                        <option value="research">Academic Research</option>
                        <option value="presentation">Presentation / Event</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-900 mb-2">Additional Details (Optional)</label>
                    <textarea rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Mention specific project names, software requirements, or accessories needed..."></textarea>
                </div>

                <div class="mb-8 flex items-start gap-2">
                    <input type="checkbox" id="policy" class="mt-1" required>
                    <label for="policy" class="text-sm text-gray-600">
                        <!-- Note: Added future development alert to policy link -->
                        I agree to the <a href="#" onclick="alert('Policy document will be available in future development.'); return false;" class="text-red-600 font-bold hover:underline">Equipment Borrowing Policy</a> and accept responsibility for loss or damage.
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="/catalog" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors flex items-center gap-2">
                        Submit Request
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection