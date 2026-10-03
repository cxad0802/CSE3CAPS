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

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Note: Form submission redirects back to inventory for UI testing -->
        <form action="/admin/inventory" method="GET" class="p-6 md:p-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Asset ID (Read-only) -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Asset ID</label>
                    <input type="text" value="IT-LPT-8821" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed" readonly>
                </div>
                <!-- Serial Number -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Serial Number</label>
                    <input type="text" value="C02X543219" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-900 mb-2">Equipment Name</label>
                <input type="text" value="MacBook Pro 14&quot; (M2 Pro)" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Category -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Category</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="laptops" selected>Laptops</option>
                        <option value="cameras">AV Equipment</option>
                        <option value="projectors">Projectors</option>
                        <option value="accessories">Accessories</option>
                    </select>
                </div>
                <!-- Status -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Current Status</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="available" selected>Available</option>
                        <option value="in-use">In Use</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="retired">Retired / Broken</option>
                    </select>
                </div>
            </div>

            <!-- Maintenance Notes -->
            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-900 mb-2">Maintenance Notes (Optional)</label>
                <textarea rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Add notes about repairs or condition..."></textarea>
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