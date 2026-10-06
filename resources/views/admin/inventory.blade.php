<!-- Note: Extend the master admin layout -->
@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 md:px-8 py-10">
    
    <!-- Note: Display Success or Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Inventory Management</h1>
            <p class="text-gray-500 text-sm">Manage all university IT equipment, track status, and add new assets.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
            <!-- Search Bar -->
            <div class="relative w-full sm:w-64">
                <input type="text" placeholder="Search Asset ID or Name..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            
            <a href="/admin/inventory/create" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-red-700 rounded-md text-sm font-bold text-white hover:bg-red-800 shadow-sm transition-colors whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Equipment
            </a>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden overflow-x-auto">
        <table class="w-full text-sm text-left min-w-[800px]">
            <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4">Asset ID</th>
                    <th class="px-6 py-4">Equipment Name</th>
                    <th class="px-6 py-4">Category</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                
                <!-- Note: Loop through the dynamic inventory data retrieved from Firestore -->
                @forelse($inventory as $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-bold text-gray-900">{{ $item['id'] }}</td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-900">{{ $item['name'] ?? 'Unknown Item' }}</p>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ ucfirst($item['category'] ?? 'General') }}</td>
                    <td class="px-6 py-4">
                        @if(isset($item['status']) && strtolower($item['status']) === 'available')
                            <span class="px-3 py-1.5 text-xs font-bold bg-teal-100 text-teal-800 rounded-md shadow-sm">Available</span>
                        @else
                            <span class="px-3 py-1.5 text-xs font-bold bg-red-100 text-red-800 rounded-md shadow-sm">{{ ucfirst($item['status'] ?? 'Maintenance') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end items-center gap-3">
                            <!-- Note: Edit Button -->
                            <a href="/admin/inventory/edit?id={{ $item['id'] }}" class="text-blue-600 hover:text-blue-800 font-bold transition-colors">Edit</a>
                            
                            <!-- Note: Delete Form with confirmation -->
                            <form action="/admin/inventory/delete/{{ $item['id'] }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this equipment?');">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800 font-bold transition-colors">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        No equipment found in the database. Add new items to populate the inventory.
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>
</div>
@endsection