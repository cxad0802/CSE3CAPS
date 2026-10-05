<!-- Note: Extend the master student layout -->
@extends('layouts.app')

@section('content')
<!-- Note: Get the current filter from the URL, default to 'all' -->
@php
    $currentFilter = request()->query('filter', 'all');
@endphp

<div class="max-w-7xl mx-auto px-4 md:px-8 py-10">
    
    <!-- Page Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Requests</h1>
            <p class="text-gray-500 text-sm">Track your equipment borrowing history and statuses.</p>
        </div>
    </div>

    <!-- Filter Tabs (Dynamic styling based on current filter) -->
    <div class="flex gap-4 mb-6 border-b border-gray-200 pb-2 overflow-x-auto">
        <a href="/my-requests?filter=all" 
           class="text-sm font-medium pb-2 whitespace-nowrap {{ $currentFilter === 'all' ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-500 hover:text-gray-700' }}">
           All Requests
        </a>
        <a href="/my-requests?filter=pending" 
           class="text-sm font-medium pb-2 whitespace-nowrap {{ $currentFilter === 'pending' ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-500 hover:text-gray-700' }}">
           Pending
        </a>
        <a href="/my-requests?filter=approved" 
           class="text-sm font-medium pb-2 whitespace-nowrap {{ $currentFilter === 'approved' ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-500 hover:text-gray-700' }}">
           Approved
        </a>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden overflow-x-auto">
        <table class="w-full text-sm text-left min-w-[600px]">
            <thead class="text-xs font-bold text-gray-500 bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4">Request ID</th>
                    <th class="px-6 py-4">Equipment</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                
                <!-- Note: Show this row only if filter is 'all' or 'pending' -->
                @if($currentFilter === 'all' || $currentFilter === 'pending')
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-900">#REQ-001</td>
                    <td class="px-6 py-4 text-gray-600">MacBook Pro 14"</td>
                    <td class="px-6 py-4 text-gray-600">Oct 10, 2026</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Pending</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <!-- Note: Added future development alert -->
                        <button onclick="alert('The Cancel feature will be developed in the future.'); return false;" class="text-red-600 hover:text-red-800 font-medium">Cancel</button>
                    </td>
                </tr>
                @endif

                <!-- Note: Show this row only if filter is 'all' or 'approved' -->
                @if($currentFilter === 'all' || $currentFilter === 'approved')
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-900">#REQ-002</td>
                    <td class="px-6 py-4 text-gray-600">Dell XPS 15</td>
                    <td class="px-6 py-4 text-gray-600">Sep 25, 2026</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Approved</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="alert('The Return feature will be developed in the future.'); return false;" class="text-blue-600 hover:text-blue-800 font-medium">Return</button>
                    </td>
                </tr>
                @endif

            </tbody>
        </table>

        <!-- Note: Show empty state if a filter has no results (Optional but good for UX) -->
        @if($currentFilter !== 'all' && $currentFilter !== 'pending' && $currentFilter !== 'approved')
        <div class="p-8 text-center text-gray-500">
            No requests found for this filter.
        </div>
        @endif

    </div>
</div>
@endsection