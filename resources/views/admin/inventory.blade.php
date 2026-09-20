@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-8 py-10">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Inventory Management</h1>
            <p class="text-gray-500 text-sm">Manage all university IT equipment, track status, and add new assets.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full pl-10 p-2.5 shadow-sm" placeholder="Search Asset ID or Name...">
            </div>
            <!-- Add New Equipment Button -->
            <button onclick="window.location.href='/admin/inventory/create'" class="px-4 py-2.5 bg-red-700 hover:bg-red-800 text-white rounded-lg text-sm font-medium shadow-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Equipment
            </button>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold">Asset ID</th>
                        <th scope="col" class="px-6 py-4 font-bold">Equipment Name</th>
                        <th scope="col" class="px-6 py-4 font-bold">Category</th>
                        <th scope="col" class="px-6 py-4 font-bold">Status</th>
                        <th scope="col" class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    
                    <!-- Row 1: Available -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">IT-LPT-8821</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">MacBook Pro 14" (M2 Pro)</div>
                            <div class="text-xs text-gray-500 mt-0.5">S/N: C02X543219</div>
                        </td>
                        <td class="px-6 py-4">Laptops</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                                Available
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-gray-500 hover:text-red-700 text-sm font-medium">Edit</button>
                        </td>
                    </tr>

                    <!-- Row 2: In Use -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">IT-CAM-2210</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">Sony A7III Camera Kit</div>
                            <div class="text-xs text-gray-500 mt-0.5">S/N: SONY99283</div>
                        </td>
                        <td class="px-6 py-4">AV Equipment</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                In Use
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-gray-500 hover:text-red-700 text-sm font-medium">Edit</button>
                        </td>
                    </tr>

                    <!-- Row 3: Maintenance -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">IT-PRJ-004</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">Epson Projector G-Series</div>
                            <div class="text-xs text-gray-500 mt-0.5">S/N: EPS44021</div>
                        </td>
                        <td class="px-6 py-4">Projectors</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Maintenance
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-gray-500 hover:text-red-700 text-sm font-medium">Edit</button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection