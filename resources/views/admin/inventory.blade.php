<!-- Note: Extend the master admin layout -->
@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 md:px-8 py-10">
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
            
            <button onclick="alert('Add Equipment feature will be developed in the future.'); return false;" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-red-700 rounded-md text-sm font-bold text-white hover:bg-red-800 shadow-sm transition-colors whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Equipment
            </button>
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
                
                <!-- Mock Row 1 -->
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-bold text-gray-900">IT-LPT-8821</td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-900">MacBook Pro 14" (M2 Pro)</p>
                        <p class="text-xs text-gray-500 mt-1">S/N: C02X543219</p>
                    </td>
                    <td class="px-6 py-4 text-gray-600">Laptops</td>
                    <td class="px-6 py-4"><span class="px-3 py-1.5 text-xs font-bold bg-teal-100 text-teal-800 rounded-md shadow-sm">Available</span></td>
                    <td class="px-6 py-4 text-right">
                        <!-- Note: Updated to anchor tag for navigation to the edit page -->
                        <a href="/admin/inventory/edit" class="text-blue-600 hover:text-blue-800 font-bold transition-colors">Edit</a>
                    </td>
                </tr>

                <!-- Mock Row 2 -->
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-bold text-gray-900">IT-CAM-2210</td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-900">Sony A7III Camera Kit</p>
                        <p class="text-xs text-gray-500 mt-1">S/N: SONY99283</p>
                    </td>
                    <td class="px-6 py-4 text-gray-600">AV Equipment</td>
                    <td class="px-6 py-4"><span class="px-3 py-1.5 text-xs font-bold bg-blue-100 text-blue-800 rounded-md shadow-sm">In Use</span></td>
                    <td class="px-6 py-4 text-right">
                        <!-- Note: Updated to anchor tag for navigation to the edit page -->
                        <a href="/admin/inventory/edit" class="text-blue-600 hover:text-blue-800 font-bold transition-colors">Edit</a>
                    </td>
                </tr>

                <!-- Mock Row 3 -->
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-bold text-gray-900">IT-PRJ-004</td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-900">Epson Projector G-Series</p>
                        <p class="text-xs text-gray-500 mt-1">S/N: EPS44021</p>
                    </td>
                    <td class="px-6 py-4 text-gray-600">Projectors</td>
                    <td class="px-6 py-4"><span class="px-3 py-1.5 text-xs font-bold bg-red-100 text-red-800 rounded-md shadow-sm">Maintenance</span></td>
                    <td class="px-6 py-4 text-right">
                        <!-- Note: Updated to anchor tag for navigation to the edit page -->
                        <a href="/admin/inventory/edit" class="text-blue-600 hover:text-blue-800 font-bold transition-colors">Edit</a>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</div>
@endsection