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
        <form method="GET" action="{{ route('catalog') }}" class="w-1/3">
            <div class="relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" class="focus:ring-red-500 focus:border-red-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2 border" placeholder="Search laptops, cameras, cables...">
            </div>
        </form>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Equipment Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        @forelse ($equipments as $equipment)
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-200">
                <div class="h-48 bg-gray-100 flex items-center justify-center border-b">
                    @if ($equipment->imageUrl)
                        <img src="{{ $equipment->imageUrl }}" alt="{{ $equipment->name }}" class="h-full w-full object-cover">
                    @else
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    @endif
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            @if ($equipment->isBorrowable())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mb-2">Available: {{ $equipment->availableQuantity }}</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-700 mb-2">Unavailable</span>
                            @endif
                            <h3 class="text-lg font-bold text-gray-900 leading-tight">{{ $equipment->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $equipment->description ?: $equipment->categoryLabel() }}</p>
                            @if ($equipment->location)
                                <p class="text-xs text-gray-400 mt-2">Pick up at {{ $equipment->location }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4">
                        @if ($equipment->isBorrowable())
                            <a href="{{ route('borrow.create', ['equipment' => $equipment->id]) }}" class="block text-center w-full bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-800 transition-colors">
                                Request to Borrow
                            </a>
                        @else
                            <button type="button" disabled class="w-full bg-gray-200 text-gray-500 px-4 py-2 rounded-md text-sm font-medium cursor-not-allowed">
                                Out of stock
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center text-gray-500 py-16">
                @if ($search !== '')
                    Nothing matches "{{ $search }}". <a href="{{ route('catalog') }}" class="text-red-700 hover:underline">Clear the search</a>.
                @else
                    No equipment has been added yet.
                @endif
            </p>
        @endforelse

    </div>
</div>
@endsection