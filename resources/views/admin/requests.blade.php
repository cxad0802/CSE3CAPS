@extends('layouts.admin')

@section('content')
<!-- Note: Get filter and search state from URL -->
@php
    $currentFilter = request()->query('filter', 'all');
    $searchQuery = request()->query('search', '');
@endphp

<div class="max-w-7xl mx-auto px-8 py-10">

    <!-- Header Section & Search Form -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Loan Requests</h1>
            <p class="text-gray-500 text-sm">Manage and process equipment borrowing requests.</p>
        </div>
        
        <!-- Search Form -->
        <form method="GET" action="/admin/requests" class="flex items-center gap-3 w-full md:w-auto">
            @if($currentFilter !== 'all')
                <input type="hidden" name="filter" value="{{ $currentFilter }}">
            @endif
            
            <div class="relative w-full md:w-64">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ $searchQuery }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full pl-10 p-2.5 shadow-sm" placeholder="Search by ID or Name...">
            </div>
            
            <button type="submit" class="hidden">Search</button>

            <button type="button" class="px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Filter
            </button>
        </form>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Note: Raised when a decision is refused, for example because another
         administrator acted on the request first or stock ran out -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-6 overflow-x-auto">
        <nav class="flex space-x-8 min-w-max" aria-label="Tabs">
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'all', 'page' => 1]) }}" 
               class="{{ $currentFilter === 'all' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                All Requests
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'pending', 'page' => 1]) }}" 
               class="{{ $currentFilter === 'pending' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                Pending
                <span class="bg-yellow-100 text-yellow-700 py-0.5 px-2 rounded-full text-xs">{{ $pendingCount ?? 0 }}</span>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'active', 'page' => 1]) }}" 
               class="{{ $currentFilter === 'active' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Active Loans
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'returned', 'page' => 1]) }}" 
               class="{{ $currentFilter === 'returned' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Returned
            </a>
        </nav>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600 min-w-[800px]">
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
                    @forelse($paginatedRequests as $req)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">#{{ substr($req['id'], 0, 8) }}</div>
                                <!-- Note: Render correctly mapped creation date -->
                                <div class="text-xs text-gray-500 mt-0.5">{{ $req['created_at'] }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $req['borrower_name'] }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $req['equipment_name'] }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <!-- Note: Render correctly mapped duration (borrowFrom to borrowTo) -->
                                <div class="text-gray-900">{{ $req['duration'] }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase
                                    {{ strtolower($req['status']) === 'pending' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : '' }}
                                    {{ in_array(strtolower($req['status']), ['approved', 'active']) ? 'bg-green-100 text-green-800 border border-green-200' : '' }}
                                    {{ strtolower($req['status']) === 'returned' ? 'bg-gray-100 text-gray-800 border border-gray-200' : '' }}
                                    {{ strtolower($req['status']) === 'cancelled' || strtolower($req['status']) === 'rejected' ? 'bg-red-100 text-red-800 border border-red-200' : '' }}
                                ">
                                    {{ $req['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <!-- Note: Show Approve/Reject buttons if Pending -->
                                    @if(strtolower($req['status']) === 'pending')
                                        <form action="/admin/requests/{{ $req['id'] }}/approve" method="POST">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-white bg-green-600 hover:bg-green-700 rounded tooltip" title="Approve">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        </form>
                                        
                                        <form action="/admin/requests/{{ $req['id'] }}/reject" method="POST" onsubmit="return confirm('Are you sure you want to reject this request?');">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-white bg-red-600 hover:bg-red-700 rounded tooltip" title="Reject">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </form>
                                    
                                    <!-- Note: Show Mark Returned button if Approved -->
                                    @elseif(in_array(strtolower($req['status']), ['approved', 'active']))
                                        <form action="/admin/requests/{{ $req['id'] }}/return" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded shadow-sm">
                                                Mark Returned
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">No actions</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No requests found for this filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Controls -->
        <div class="px-6 py-4 border-t border-gray-200 bg-white flex items-center justify-between">
            <span class="text-sm text-gray-500">
                Showing <span class="font-medium text-gray-900">{{ $totalResults > 0 ? ($page - 1) * 10 + 1 : 0 }}</span> to <span class="font-medium text-gray-900">{{ min($page * 10, $totalResults) }}</span> of <span class="font-medium text-gray-900">{{ $totalResults }}</span> results
            </span>
            
            @if(isset($totalPages) && $totalPages > 1)
            <div class="inline-flex rounded-md shadow-sm">
                @if($page > 1)
                    <a href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-50">Previous</a>
                @else
                    <button disabled class="px-3 py-2 text-sm font-medium text-gray-300 bg-gray-50 border border-gray-300 rounded-l-lg cursor-not-allowed">Previous</button>
                @endif
                
                <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 border-t border-b border-gray-300">{{ $page }}</button>
                
                @if($page < $totalPages)
                    <a href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-50">Next</a>
                @else
                    <button disabled class="px-3 py-2 text-sm font-medium text-gray-300 bg-gray-50 border border-gray-300 rounded-r-lg cursor-not-allowed">Next</button>
                @endif
            </div>
            @endif
        </div>
    </div>

</div>
@endsection