@extends('layouts.app')

@section('content')
<!-- Note: Get current filter from controller -->
@php
    $currentFilter = $currentFilter ?? 'all';
@endphp

<div class="max-w-7xl mx-auto px-4 md:px-8 py-10">
    
    <!-- Page Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Requests</h1>
            <p class="text-gray-500 text-sm">Track your equipment borrowing history and statuses.</p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Dynamic Filter Tabs -->
    <div class="flex gap-4 mb-6 border-b border-gray-200 pb-2 overflow-x-auto">
        <a href="/my-requests?filter=all" class="text-sm font-medium pb-2 whitespace-nowrap {{ $currentFilter === 'all' ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-500 hover:text-gray-700' }}">All Requests</a>
        <a href="/my-requests?filter=pending" class="text-sm font-medium pb-2 whitespace-nowrap {{ $currentFilter === 'pending' ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-500 hover:text-gray-700' }}">Pending</a>
        <a href="/my-requests?filter=approved" class="text-sm font-medium pb-2 whitespace-nowrap {{ $currentFilter === 'approved' ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-500 hover:text-gray-700' }}">Approved</a>
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
                
                @forelse($myRequests as $req)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-900">#{{ substr($req['id'], 0, 8) }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $req['equipmentName'] ?? 'Unknown Equipment' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $req['createdAt'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-xs font-medium uppercase rounded-full
                            {{ $req['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ in_array($req['status'], ['approved', 'active']) ? 'bg-green-100 text-green-700' : '' }}
                            {{ $req['status'] === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $req['status'] === 'returned' ? 'bg-gray-100 text-gray-700' : '' }}
                        ">
                            {{ $req['status'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        
                        <!-- Note: Show Cancel only if pending -->
                        @if($req['status'] === 'pending')
                            <form action="/my-requests/{{ $req['id'] }}/cancel" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to cancel this request?');">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Cancel</button>
                            </form>
                        @endif

                        <!-- Note: Show Return only if approved/active -->
                        @if(in_array($req['status'], ['approved', 'active']))
                            <form action="/my-requests/{{ $req['id'] }}/return" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to return this equipment?');">
                                @csrf
                                <button type="submit" class="text-blue-600 hover:text-blue-800 font-medium">Return</button>
                            </form>
                        @endif

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        You have no requests matching this filter.
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

</div>
@endsection