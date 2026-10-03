@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-8 py-10">

    <!-- Header Section -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Overview</h1>
            <p class="text-gray-500 text-sm">Real-time status of university IT assets.</p>
        </div>
        <button onclick="exportToPDF()" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Export Data
        </button>
    </div>

    <!-- Top Grid: KPI Cards and Alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- 4 Tall KPI Cards -->
        <div class="lg:col-span-2 grid grid-cols-2 md:grid-cols-4 gap-4">
            
            <!-- Card 1: Total Assets -->
            <div class="border border-gray-200 rounded-lg p-5 flex flex-col justify-between h-56 bg-white shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <span class="text-sm font-bold text-gray-700">Total Assets</span>
                    <div class="p-1.5 bg-gray-100 rounded-md text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-gray-900">1,248</div>
                </div>
            </div>

            <!-- Card 2: Available -->
            <div class="border border-gray-200 rounded-lg p-5 flex flex-col justify-between h-56 bg-white shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <span class="text-sm font-bold text-gray-700">Available</span>
                    <div class="p-1.5 bg-teal-50 rounded-md text-teal-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-gray-900 mb-1">892</div>
                    <p class="text-xs font-semibold text-teal-600">71% readiness</p>
                </div>
            </div>

            <!-- Card 3: Checked Out -->
            <div class="border border-gray-200 rounded-lg p-5 flex flex-col justify-between h-56 bg-white shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <span class="text-sm font-bold text-gray-700">Checked Out</span>
                    <div class="p-1.5 bg-red-50 rounded-md text-red-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-gray-900 mb-1">315</div>
                    <p class="text-xs text-gray-500">Active loans</p>
                </div>
            </div>

            <!-- Card 4: In Maintenance -->
            <div class="border border-red-200 rounded-lg p-5 flex flex-col justify-between h-56 bg-white shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>
                <div class="flex justify-between items-start pl-2">
                    <span class="text-sm font-bold text-red-700">In<br>Maintenance</span>
                    <div class="p-1.5 bg-red-100 rounded-full text-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <div class="pl-2">
                    <div class="text-4xl font-bold text-red-600 mb-1">41</div>
                    <p class="text-xs font-semibold text-red-500">Require attention</p>
                </div>
            </div>

        </div>

        <!-- Critical Alerts Panel -->
        <div class="border border-red-200 rounded-lg p-5 bg-white shadow-sm flex flex-col h-56">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-900 flex items-center gap-2 text-lg">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Critical Alerts
                </h3>
                <a href="#" class="text-xs text-red-700 font-bold hover:underline">View All</a>
            </div>
            
            <div class="space-y-4 overflow-y-auto flex-1 pr-2">
                <!-- Alert Item -->
                <div class="relative pl-4 border-l-2 border-red-500">
                    <div class="absolute w-2 h-2 bg-red-500 rounded-full -left-[5px] top-1.5"></div>
                    <h4 class="text-sm font-bold text-gray-900 leading-tight">MacBook Pro 14" (Asset #8821)</h4>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-1">Reported: Battery swelling. Needs...</p>
                    <p class="text-[10px] text-gray-400 mt-1 uppercase font-semibold">Reported 2 hrs ago</p>
                </div>
                <!-- Alert Item -->
                <div class="relative pl-4 border-l-2 border-red-500">
                    <div class="absolute w-2 h-2 bg-red-500 rounded-full -left-[5px] top-1.5"></div>
                    <h4 class="text-sm font-bold text-gray-900 leading-tight">Dell XPS 15 (Asset #7490)</h4>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-1">Overdue by 5 days. Student...</p>
                    <p class="text-[10px] text-red-600 mt-1 uppercase font-semibold">Flagged system</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Grid: Tables and Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
        
        <!-- Expiring Warranties Table -->
        <div class="border border-gray-200 rounded-lg p-6 bg-white shadow-sm">
            <h3 class="font-bold text-gray-900 text-lg mb-1">Expiring Warranties</h3>
            <p class="text-xs text-gray-500 mb-6">Items needing renewal in next 30 days.</p>
            
            <table class="w-full text-sm text-left">
                <thead class="text-xs font-bold text-gray-500 border-b border-gray-200">
                    <tr>
                        <th class="pb-3">Asset ID</th>
                        <th class="pb-3">Equipment Type</th>
                        <th class="pb-3">Expiration</th>
                        <th class="pb-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="py-3 font-medium text-gray-900">IT-SRV-092</td>
                        <td class="py-3 text-gray-600">Dell PowerEdge R740</td>
                        <td class="py-3 font-semibold text-red-600">Oct 12, 2024</td>
                        <td class="py-3 text-right"><a href="#" class="font-bold text-red-700 hover:underline">Renew</a></td>
                    </tr>
                    <tr>
                        <td class="py-3 font-medium text-gray-900">IT-LPT-441</td>
                        <td class="py-3 text-gray-600">Lenovo ThinkPad T14</td>
                        <td class="py-3 font-semibold text-red-600">Oct 18, 2024</td>
                        <td class="py-3 text-right"><a href="#" class="font-bold text-red-700 hover:underline">Renew</a></td>
                    </tr>
                    <tr>
                        <td class="py-3 font-medium text-gray-900">IT-AV-105</td>
                        <td class="py-3 text-gray-600">Epson Projector G-Series</td>
                        <td class="py-3 text-gray-600">Oct 25, 2024</td>
                        <td class="py-3 text-right"><a href="#" class="font-bold text-red-700 hover:underline">Review</a></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Frequently Borrowed Bar Chart -->
        <div class="border border-gray-200 rounded-lg p-6 bg-white shadow-sm">
            <h3 class="font-bold text-gray-900 text-lg mb-1">Frequently Borrowed</h3>
            <p class="text-xs text-gray-500 mb-6">Top categories by volume this semester.</p>
            
            <div class="space-y-5">
                <!-- Bar 1 -->
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-semibold text-gray-800">Standard Laptops (Windows)</span>
                        <span class="text-gray-500 text-xs">420 loans</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-red-700 h-2 rounded-full" style="width: 85%"></div>
                    </div>
                </div>
                <!-- Bar 2 -->
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-semibold text-gray-800">MacBooks</span>
                        <span class="text-gray-500 text-xs">315 loans</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-red-600 h-2 rounded-full opacity-80" style="width: 65%"></div>
                    </div>
                </div>
                <!-- Bar 3 -->
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-semibold text-gray-800">DSLR Cameras</span>
                        <span class="text-gray-500 text-xs">180 loans</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-red-900 h-2 rounded-full opacity-60" style="width: 40%"></div>
                    </div>
                </div>
                <!-- Bar 4 -->
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-semibold text-gray-800">Audio Recorders</span>
                        <span class="text-gray-500 text-xs">95 loans</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-red-200 h-2 rounded-full" style="width: 20%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-100 rounded-lg p-8 mt-auto grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="md:col-span-1">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                <span class="font-bold text-gray-900">UniBorrow IT</span>
            </div>
            <p class="text-xs text-gray-500">© 2024 University IT Services. All rights reserved.</p>
        </div>
        
        <div>
            <h4 class="text-xs font-bold text-gray-900 uppercase mb-3">Institutional</h4>
            <ul class="text-xs text-gray-500 space-y-2">
                <li><a href="#" class="hover:text-gray-900">Institutional Links</a></li>
                <li><a href="#" class="hover:text-gray-900">Accessibility</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-xs font-bold text-gray-900 uppercase mb-3">Support</h4>
            <ul class="text-xs text-gray-500 space-y-2">
                <li><a href="#" class="hover:text-gray-900">Contact Info</a></li>
                <li><a href="#" class="hover:text-gray-900">Privacy Policy</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-xs font-bold text-gray-900 uppercase mb-3">Connect</h4>
            <div class="flex gap-4 text-gray-500">
                <a href="#" class="hover:text-gray-900"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg></a>
                <a href="#" class="hover:text-gray-900"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></a>
            </div>
        </div>
    </footer>

</div>

<!-- JavaScript for Mock PDF Download -->
<script>
    function exportToPDF() {
        const mockPdfContent = '%PDF-1.4\n1 0 obj\n<< /Title (Admin Dashboard Export) >>\nendobj\n';
        const blob = new Blob([mockPdfContent], { type: 'application/pdf' });
        const url = window.URL.createObjectURL(blob);
        
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'UniBorrow_ExportData.pdf';
        
        document.body.appendChild(a);
        a.click();
        
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }
</script>
@endsection