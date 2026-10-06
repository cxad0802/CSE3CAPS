@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-8 py-10">

    <!-- Header -->
    <div class="mb-8">
        <a href="/admin/inventory" class="text-sm text-red-600 hover:text-red-800 font-medium flex items-center gap-1 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Inventory
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Add New Equipment</h1>
        <p class="text-gray-500 mt-1">Register a new IT asset into the system inventory.</p>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <form action="/admin/inventory/store" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                
                <!-- Asset ID -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Asset ID *</label>
                    <input type="text" name="asset_id" placeholder="e.g., IT-LPT-9901" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block p-2.5 outline-none" required>
                    <p class="text-xs text-gray-500 mt-1">Unique identifier tag for the university.</p>
                </div>

                <!-- Equipment Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Equipment Name *</label>
                    <input type="text" name="equipment_name" placeholder="e.g., MacBook Pro 16-inch" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block p-2.5 outline-none" required>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <select name="category" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block p-2.5 outline-none" required>
                        <option value="" disabled selected>Select a category</option>
                        <option value="laptops">Laptops & Computers</option>
                        <option value="cameras">Cameras & AV</option>
                        <option value="audio">Audio Equipment</option>
                        <option value="projectors">Projectors</option>
                        <option value="accessories">Cables & Accessories</option>
                    </select>
                </div>

                <!-- Serial Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Serial Number (S/N)</label>
                    <input type="text" name="serial_number" placeholder="Manufacturer serial number" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block p-2.5 outline-none">
                </div>

                <!-- Initial Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Initial Status</label>
                    <select name="initial_status" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block p-2.5 outline-none">
                        <option value="available" selected>Available (Ready for borrowing)</option>
                        <option value="maintenance">Maintenance (Requires setup/repair)</option>
                    </select>
                </div>

                <!-- Warranty Expiration -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warranty Expiration Date</label>
                    <input type="date" name="warranty_expiration" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block p-2.5 outline-none">
                </div>

            </div>

            <!-- Description -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes / Specifications</label>
                <textarea name="notes" rows="4" placeholder="Add any specific details, included accessories (e.g., charger, bag), or specs..." class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block p-2.5 outline-none"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 border-t border-gray-200 pt-6">
                <button type="button" onclick="window.location.href='/admin/inventory'" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-red-700 border border-transparent rounded-lg hover:bg-red-800 transition-colors">
                    Save Equipment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection