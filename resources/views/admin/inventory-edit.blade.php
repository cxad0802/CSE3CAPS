<!-- Note: Extend the master admin layout -->
@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 md:px-8 py-10">
    
    <!-- Back Button -->
    <a href="/admin/inventory" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-red-600 transition-colors mb-6">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Inventory
    </a>

    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Equipment Details</h1>
        <p class="text-gray-500 text-sm">Update asset information, change status, or log maintenance.</p>
    </div>

    <!-- Note: Display Errors if any -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Note: Form submits via POST to the dynamic update route -->
        <form action="/admin/inventory/update/{{ $equipment['id'] }}" method="POST" class="p-6 md:p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Asset ID (Read-only Document ID from Firestore) -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Asset ID</label>
                    <input type="text" value="{{ $equipment['id'] }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed" readonly>
                </div>
                
                <!-- Category -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Category</label>
                    <!-- Note: Pre-selects the existing category from Firestore -->
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500" required>
                        <option value="laptops" {{ (isset($equipment['category']) && $equipment['category'] == 'laptops') ? 'selected' : '' }}>Laptops & Computers</option>
                        <option value="cameras" {{ (isset($equipment['category']) && $equipment['category'] == 'cameras') ? 'selected' : '' }}>Cameras & AV</option>
                        <option value="audio" {{ (isset($equipment['category']) && $equipment['category'] == 'audio') ? 'selected' : '' }}>Audio Equipment</option>
                        <option value="projectors" {{ (isset($equipment['category']) && $equipment['category'] == 'projectors') ? 'selected' : '' }}>Projectors</option>
                        <option value="accessories" {{ (isset($equipment['category']) && $equipment['category'] == 'accessories') ? 'selected' : '' }}>Accessories</option>
                    </select>
                </div>
            </div>

            <!-- Equipment Name -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-900 mb-2">Equipment Name</label>
                <input type="text" name="equipment_name" value="{{ $equipment['name'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500" required>
            </div>

            <div class="mb-6">
                <!-- Status -->
                <label class="block text-sm font-bold text-gray-900 mb-2">Current Status</label>
                <!-- Note: Pre-selects the existing status from Firestore -->
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500" required>
                    <option value="available" {{ (isset($equipment['status']) && $equipment['status'] == 'available') ? 'selected' : '' }}>Available (Ready for borrowing)</option>
                    <option value="in-use" {{ (isset($equipment['status']) && $equipment['status'] == 'in-use') ? 'selected' : '' }}>In Use</option>
                    <option value="maintenance" {{ (isset($equipment['status']) && $equipment['status'] == 'maintenance') ? 'selected' : '' }}>Maintenance (Requires repair)</option>
                    <option value="retired" {{ (isset($equipment['status']) && $equipment['status'] == 'retired') ? 'selected' : '' }}>Retired / Broken</option>
                </select>
            </div>

            <!-- Maintenance / Description Notes -->
            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-900 mb-2">Description / Notes</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Add notes about repairs, condition, or specs...">{{ $equipment['description'] ?? '' }}</textarea>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="/admin/inventory" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors shadow-sm">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection