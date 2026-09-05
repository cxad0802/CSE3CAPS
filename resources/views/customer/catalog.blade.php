@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow">
    
    <!-- Catalog Header -->
    <div class="flex justify-between items-center mb-8 border-b pb-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Equipment Catalog</h1>
            <p class="text-gray-500 mt-1">Browse and request IT equipment for your studies.</p>
        </div>
        
        <!-- Search Bar -->
        <div class="w-1/3">
            <div class="relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" class="focus:ring-red-500 focus:border-red-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2 border" placeholder="Search laptops, cameras, cables...">
            </div>
        </div>
    </div>

    <!-- Equipment Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Sample Equipment Item -->
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-200">
            <div class="h-48 bg-gray-100 flex items-center justify-center border-b">
                <!-- Mockup Image Icon -->
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div class="p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mb-2">Available: 5</span>
                        <h3 class="text-lg font-bold text-gray-900 leading-tight">Dell Latitude 7420</h3>
                        <p class="text-sm text-gray-500 mt-1">Standard Issue Laptop</p>
                    </div>
                </div>
                <div class="mt-4">
                    <button class="w-full bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-800 transition-colors">
                        Request to Borrow
                    </button>
                </div>
            </div>
        </div>
        
        <!-- You can duplicate the "Sample Equipment Item" block above to populate the grid -->

    </div>
</div>
@endsection