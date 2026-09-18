@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-8 py-10">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Loan Requests</h1>
            <p class="text-gray-500 text-sm">Manage and process equipment borrowing requests.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full pl-10 p-2.5 shadow-sm" placeholder="Search by ID or Name...">
            </div>
            <button class="px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Filter
            </button>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex space-x-8" aria-label="Tabs">
            <a href="#" class="border-red-600 text-red-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                All Requests
            </a>
            <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                Pending
                <span class="bg-yellow-100 text-yellow-700 py-0.5 px-2 rounded-full text-xs">12</span>
            </a>
            <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Active Loans
            </a>
            <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Returned
            </a>
        </nav>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold">Req ID / Date</th>
                        <th scope="col" class="px-6 py-4 font-bold">Borrower</th>
                        <th scope="col" class="px-6 py-4 font-bold">Equipment</th>
                        <th scope="col" class="px-6 py-4 font-bold">Duration</th>
                        <th scope="col" class="px-6 py-4 font-bold">Status</th>
                        <th scope="col" class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    
                    <!-- Row 1: Pending -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">#REQ-0192</div>
                            <div class="text-xs text-gray-500 mt-0.5">Today, 09:30 AM</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">Justin Mason</div>
                            <div class="text-xs text-gray-500 mt-0.5">ID: 22014567</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">MacBook Pro 14"</div>
                            <div class="text-xs text-gray-500 mt-0.5">IT-LPT-8821</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-900">Sep 20 - Sep 27</div>
                            <div class="text-xs text-gray-500 mt-0.5">7 days</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                Pending
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="p-1.5 text-white bg-green-600 hover:bg-green-700 rounded tooltip" title="Approve">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </button>
                                <button class="p-1.5 text-white bg-red-600 hover:bg-red-700 rounded tooltip" title="Reject">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 2: Active -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">#REQ-0185</div>
                            <div class="text-xs text-gray-500 mt-0.5">Sep 15, 14:15 PM</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">Sarah Jenkins</div>
                            <div class="text-xs text-gray-500 mt-0.5">ID: 19882341</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">Sony A7III Camera</div>
                            <div class="text-xs text-gray-500 mt-0.5">IT-CAM-2210</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-900">Sep 16 - Sep 20</div>
                            <div class="text-xs text-red-500 font-medium mt-0.5">Due tomorrow</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                Active Loan
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded shadow-sm">
                                Mark Returned
                            </button>
                        </td>
                    </tr>

                    <!-- Row 3: Returned -->
                    <tr class="hover:bg-gray-50 transition-colors bg-gray-50/50">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">#REQ-0170</div>
                            <div class="text-xs text-gray-500 mt-0.5">Sep 10, 10:00 AM</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">Michael Wong</div>
                            <div class="text-xs text-gray-500 mt-0.5">ID: 21099834</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">Dell XPS 15</div>
                            <div class="text-xs text-gray-500 mt-0.5">IT-LPT-7490</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-900">Sep 10 - Sep 12</div>
                            <div class="text-xs text-gray-500 mt-0.5">Returned on time</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                Completed
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="px-3 py-1.5 text-xs font-medium text-gray-500 hover:text-gray-700">
                                View Details
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 bg-white flex items-center justify-between">
            <span class="text-sm text-gray-500">Showing <span class="font-medium text-gray-900">1</span> to <span class="font-medium text-gray-900">3</span> of <span class="font-medium text-gray-900">42</span> results</span>
            <div class="inline-flex rounded-md shadow-sm">
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-50">Previous</button>
                <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border-t border-b border-gray-300 hover:bg-gray-50">1</button>
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-50">Next</button>
            </div>
        </div>
    </div>

</div>
@endsection