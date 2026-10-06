<!-- Note: Extend the master student layout -->
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 md:px-8 py-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Equipment Catalog</h1>
            <p class="text-gray-500 text-sm">Browse and request university IT assets for your coursework.</p>
        </div>
        <!-- Search Bar -->
        <form method="GET" action="{{ route('catalog') }}" class="w-full md:w-72">
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search equipment..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </form>
    </div>

    <!-- Note: Raised when a stale catalogue link points at an item that is gone or withheld -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Equipment Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Note: Loop through the available catalog data from Firestore -->
        @forelse($catalog as $item)
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="h-48 bg-gray-100 flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div class="p-5">
                <!-- Note: Render actual Firestore fields for 'name' and 'description' -->
                <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $item['name'] ?? 'Unknown Equipment' }}</h3>
                <p class="text-sm text-gray-500 mb-4 line-clamp-2">
                    {{ $item['description'] ?? 'Standard university IT asset for development and coursework.' }}
                </p>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-semibold text-green-600 bg-green-50 px-2.5 py-1 rounded-full">Available</span>
                    
                    <!-- Note: Encode 'name' properly to pass it cleanly via the query string -->
                    <a href="/borrow/request?equipmentId={{ $item['id'] }}&name={{ urlencode($item['name'] ?? 'Unknown Item') }}" 
                       class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition-colors">
                        Request
                    </a>
                </div>
            </div>
        </div>
        @empty
        <!-- Note: Fallback message when no items match -->
        <div class="col-span-full text-center py-10 bg-gray-50 rounded-lg border border-dashed border-gray-300 text-gray-500">
            @if($search !== '')
                Nothing in the catalogue matches "{{ $search }}".
                <a href="{{ route('catalog') }}" class="text-red-600 font-medium hover:underline">Clear the search</a>
            @else
                No equipment is currently available for borrowing. Please check back later.
            @endif
        </div>
        @endforelse

    </div>
</div>
@endsection