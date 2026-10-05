<!-- Note: Extend the master admin layout -->
@extends('layouts.admin')

@section('content')
<!-- Note: Target container for dashboard overview -->
<div id="dashboard-content" class="max-w-7xl mx-auto px-8 py-10">

    <!-- Header Section -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Overview</h1>
            <p class="text-gray-500 text-sm">Real-time status of university IT assets.</p>
        </div>
        <!-- Note: Export Data Button -->
        <button id="export-btn" onclick="exportToPDF()" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Export Data
        </button>
    </div>

    <!-- Top Grid: KPI Cards and Alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- 4 Tall KPI Cards -->
        <div class="lg:col-span-2 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="border border-gray-200 rounded-lg p-5 flex flex-col justify-between h-56 bg-white shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <span class="text-sm font-bold text-gray-700">Total Assets</span>
                    <div class="p-1.5 bg-gray-100 rounded-md text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <div><div class="text-4xl font-bold text-gray-900">1,248</div></div>
            </div>

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
                <button onclick="openWarrantyModal()" class="text-xs text-red-700 font-bold hover:underline focus:outline-none">View All</button>
            </div>
            
            <div class="space-y-4 overflow-y-auto flex-1 pr-2">
                <div class="relative pl-4 border-l-2 border-red-500">
                    <div class="absolute w-2 h-2 bg-red-500 rounded-full -left-[5px] top-1.5"></div>
                    <h4 class="text-sm font-bold text-gray-900 leading-tight">MacBook Pro 14" (Asset #8821)</h4>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-1">Reported: Battery swelling. Needs...</p>
                    <p class="text-[10px] text-gray-400 mt-1 uppercase font-semibold">Reported 2 hrs ago</p>
                </div>
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
                        <td class="py-3 font-semibold text-red-600">Oct 12, 2026</td>
                        <td class="py-3 text-right"><button onclick="alert('Renewing contract for IT-SRV-092...');" class="font-bold text-red-700 hover:underline focus:outline-none">Renew</button></td>
                    </tr>
                    <tr>
                        <td class="py-3 font-medium text-gray-900">IT-LPT-441</td>
                        <td class="py-3 text-gray-600">Lenovo ThinkPad T14</td>
                        <td class="py-3 font-semibold text-red-600">Oct 18, 2026</td>
                        <td class="py-3 text-right"><button onclick="alert('Renewing contract for IT-LPT-441...');" class="font-bold text-red-700 hover:underline focus:outline-none">Renew</button></td>
                    </tr>
                    <tr>
                        <td class="py-3 font-medium text-gray-900">IT-AV-105</td>
                        <td class="py-3 text-gray-600">Epson Projector G-Series</td>
                        <td class="py-3 text-gray-600">Oct 25, 2026</td>
                        <td class="py-3 text-right"><button onclick="alert('Reviewing contract for IT-AV-105...');" class="font-bold text-red-700 hover:underline focus:outline-none">Review</button></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Frequently Borrowed Bar Chart -->
        <div class="border border-gray-200 rounded-lg p-6 bg-white shadow-sm">
            <h3 class="font-bold text-gray-900 text-lg mb-1">Frequently Borrowed</h3>
            <p class="text-xs text-gray-500 mb-6">Top categories by volume this semester.</p>
            <div class="space-y-5">
                <div>
                    <div class="flex justify-between text-sm mb-1"><span class="font-semibold text-gray-800">Standard Laptops (Windows)</span><span class="text-gray-500 text-xs">420 loans</span></div>
                    <div class="w-full bg-gray-100 rounded-full h-2"><div class="bg-red-700 h-2 rounded-full" style="width: 85%"></div></div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1"><span class="font-semibold text-gray-800">MacBooks</span><span class="text-gray-500 text-xs">315 loans</span></div>
                    <div class="w-full bg-gray-100 rounded-full h-2"><div class="bg-red-600 h-2 rounded-full opacity-80" style="width: 65%"></div></div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1"><span class="font-semibold text-gray-800">DSLR Cameras</span><span class="text-gray-500 text-xs">180 loans</span></div>
                    <div class="w-full bg-gray-100 rounded-full h-2"><div class="bg-red-900 h-2 rounded-full opacity-60" style="width: 40%"></div></div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1"><span class="font-semibold text-gray-800">Audio Recorders</span><span class="text-gray-500 text-xs">95 loans</span></div>
                    <div class="w-full bg-gray-100 rounded-full h-2"><div class="bg-red-200 h-2 rounded-full" style="width: 20%"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- WARRANTY MODAL (Activated by "View All" button) -->
<!-- ============================================== -->
<div id="warrantyModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center backdrop-blur-sm transition-opacity px-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[85vh] flex flex-col transform transition-all">
        
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Expired & Expiring Warranties</h3>
                <p class="text-sm text-gray-500">Comprehensive list of equipment needing warranty renewal.</p>
            </div>
            <button onclick="closeWarrantyModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <!-- Modal Body (Scrollable Table) -->
        <div class="p-0 overflow-y-auto overflow-x-auto bg-gray-50">
            <table class="w-full text-sm text-left min-w-[600px]">
                <thead class="text-xs font-bold text-gray-500 bg-white border-b border-gray-200 sticky top-0 shadow-sm">
                    <tr>
                        <th class="px-6 py-4">Asset ID</th>
                        <th class="px-6 py-4">Equipment Name</th>
                        <th class="px-6 py-4">Expiration Date</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <!-- EXPIRED Items (Highlighted Red) -->
                    <tr class="bg-red-50 hover:bg-red-100 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-900">IT-NET-002</td>
                        <td class="px-6 py-4 text-gray-800 font-medium">Cisco Catalyst 2960 Switch</td>
                        <td class="px-6 py-4 font-bold text-red-600">Sep 15, 2026</td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 text-[11px] font-bold bg-red-200 text-red-800 rounded-full uppercase tracking-wider">Expired</span></td>
                        <td class="px-6 py-4 text-right"><button onclick="alert('Processing renewal for IT-NET-002...');" class="font-bold text-red-700 hover:underline focus:outline-none">Renew Now</button></td>
                    </tr>
                    <tr class="bg-red-50 hover:bg-red-100 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-900">IT-LPT-108</td>
                        <td class="px-6 py-4 text-gray-800 font-medium">MacBook Pro 13" (M1)</td>
                        <td class="px-6 py-4 font-bold text-red-600">Aug 30, 2026</td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 text-[11px] font-bold bg-red-200 text-red-800 rounded-full uppercase tracking-wider">Expired</span></td>
                        <td class="px-6 py-4 text-right"><button onclick="alert('Processing renewal for IT-LPT-108...');" class="font-bold text-red-700 hover:underline focus:outline-none">Renew Now</button></td>
                    </tr>
                    
                    <!-- EXPIRING SOON Items -->
                    <tr class="bg-white hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">IT-SRV-092</td>
                        <td class="px-6 py-4 text-gray-600">Dell PowerEdge R740</td>
                        <td class="px-6 py-4 font-semibold text-orange-600">Oct 12, 2026</td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 text-[11px] font-bold bg-orange-100 text-orange-800 rounded-full uppercase tracking-wider">Expiring</span></td>
                        <td class="px-6 py-4 text-right"><button onclick="alert('Processing renewal for IT-SRV-092...');" class="font-bold text-red-700 hover:underline focus:outline-none">Renew</button></td>
                    </tr>
                    <tr class="bg-white hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">IT-LPT-441</td>
                        <td class="px-6 py-4 text-gray-600">Lenovo ThinkPad T14</td>
                        <td class="px-6 py-4 font-semibold text-orange-600">Oct 18, 2026</td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 text-[11px] font-bold bg-orange-100 text-orange-800 rounded-full uppercase tracking-wider">Expiring</span></td>
                        <td class="px-6 py-4 text-right"><button onclick="alert('Processing renewal for IT-LPT-441...');" class="font-bold text-red-700 hover:underline focus:outline-none">Renew</button></td>
                    </tr>
                    <tr class="bg-white hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">IT-AV-105</td>
                        <td class="px-6 py-4 text-gray-600">Epson Projector G-Series</td>
                        <td class="px-6 py-4 font-semibold text-gray-600">Oct 25, 2026</td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 text-[11px] font-bold bg-gray-200 text-gray-700 rounded-full uppercase tracking-wider">Review Needed</span></td>
                        <td class="px-6 py-4 text-right"><button onclick="alert('Reviewing IT-AV-105...');" class="font-bold text-red-700 hover:underline focus:outline-none">Review</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Modal Footer -->
        <div class="p-6 border-t border-gray-200 bg-white rounded-b-xl flex justify-end">
            <button onclick="closeWarrantyModal()" class="px-6 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-200 transition-colors">
                Close
            </button>
        </div>
    </div>
</div>
<!-- ============================================== -->

<!-- Note: Load jsPDF library directly from CDN (No screenshot permission issues) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<!-- Scripts -->
<script>
    function exportToPDF() {
        // Note: Ensure jsPDF library is loaded
        if (typeof window.jspdf === 'undefined') {
            alert('PDF library is still loading. Please try again.');
            return;
        }

        try {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Note: Add document title and metadata text directly (Bypass canvas restrictions entirely)
            doc.setFontSize(20);
            doc.text("UniBorrow IT Asset Overview", 14, 20);

            doc.setFontSize(11);
            doc.setTextColor(100);
            doc.text("Real-time status report generated from system records.", 14, 28);

            // Note: Add summary text data blocks
            doc.setFontSize(13);
            doc.setTextColor(0);
            doc.text("Key Performance Indicators (KPIs):", 14, 40);

            doc.setFontSize(10);
            doc.text("- Total Assets: 1,248", 20, 48);
            doc.text("- Available Assets: 892 (71% readiness)", 20, 55);
            doc.text("- Checked Out: 315 active loans", 20, 62);
            doc.text("- In Maintenance: 41 items require attention", 20, 69);

            // Note: Add expiring warranties section
            doc.setFontSize(13);
            doc.text("Expiring Warranties Summary:", 14, 85);

            doc.setFontSize(10);
            doc.text("1. IT-SRV-092 | Dell PowerEdge R740 | Exp: Oct 12, 2026", 20, 93);
            doc.text("2. IT-LPT-441 | Lenovo ThinkPad T14 | Exp: Oct 18, 2026", 20, 100);
            doc.text("3. IT-AV-105  | Epson Projector G-Series | Exp: Oct 25, 2026", 20, 107);

            // Note: Save the generated PDF file directly
            doc.save('UniBorrow_Overview.pdf');
        } catch (error) {
            console.error("Error generating text-based PDF:", error);
            alert("Failed to export data. Please try again.");
        }
    }

    // Note: Modal state management functions
    function openWarrantyModal() {
        document.getElementById('warrantyModal').classList.remove('hidden');
    }
    
    function closeWarrantyModal() {
        document.getElementById('warrantyModal').classList.add('hidden');
    }
</script>
@endsection