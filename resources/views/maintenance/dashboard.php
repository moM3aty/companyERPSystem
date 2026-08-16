<?php
// Path: resources/views/maintenance/dashboard.php
$pageTitle = 'CMMS Dashboard';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i> Home</a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Maintenance</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Maintenance Dashboard (CMMS)</h1>
        <p class="text-sm text-gray-500 mt-1">Monitor asset health, preventive maintenance plans, and work orders.</p>
    </div>
    
    <div class="mt-4 sm:mt-0 flex gap-3">
        <a href="/maintenance/work-orders/create" class="btn-primary flex items-center shadow-md shadow-nour-primary/30">
            <i class="fas fa-tools mr-2"></i> New Work Order
        </a>
    </div>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between border-l-4 border-l-nour-primary">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Active Work Orders</p>
            <h4 class="text-2xl font-bold text-gray-900">24</h4>
        </div>
        <div class="w-10 h-10 rounded-full bg-blue-50 text-nour-primary flex items-center justify-center text-lg"><i class="fas fa-wrench"></i></div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between border-l-4 border-l-red-500">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Critical / Broken</p>
            <h4 class="text-2xl font-bold text-red-600">3</h4>
        </div>
        <div class="w-10 h-10 rounded-full bg-red-50 text-red-500 flex items-center justify-center text-lg"><i class="fas fa-exclamation-triangle"></i></div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between border-l-4 border-l-yellow-500">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Due PM Plans</p>
            <h4 class="text-2xl font-bold text-yellow-600">8</h4>
        </div>
        <div class="w-10 h-10 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center text-lg"><i class="fas fa-calendar-check"></i></div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between border-l-4 border-l-green-500">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Overall Uptime</p>
            <h4 class="text-2xl font-bold text-green-600">98.5%</h4>
        </div>
        <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-lg"><i class="fas fa-heartbeat"></i></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 card p-0 overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-base font-bold text-gray-800">Recent Work Orders</h3>
            <a href="/maintenance/work-orders" class="text-sm font-medium text-nour-primary hover:underline">View All</a>
        </div>
        <div class="table-responsive">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                        <th class="p-4 font-semibold">Order No.</th>
                        <th class="p-4 font-semibold">Asset / Machine</th>
                        <th class="p-4 font-semibold">Priority</th>
                        <th class="p-4 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 font-bold text-nour-primary">WO-2608-015</td>
                        <td class="p-4 font-medium text-gray-900">CNC Machine Alpha (Asset-102)</td>
                        <td class="p-4"><span class="text-red-600 font-bold text-xs"><i class="fas fa-arrow-up mr-1"></i> Critical</span></td>
                        <td class="p-4 text-center"><span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">In Progress</span></td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 font-bold text-nour-primary">WO-2608-014</td>
                        <td class="p-4 font-medium text-gray-900">Forklift T-3 (Asset-088)</td>
                        <td class="p-4"><span class="text-gray-600 font-bold text-xs">Normal</span></td>
                        <td class="p-4 text-center"><span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Pending</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="lg:col-span-1 card p-0 overflow-hidden flex flex-col">
        <div class="p-4 border-b border-gray-100 bg-gray-50">
            <h3 class="text-base font-bold text-gray-800">Upcoming Preventive Maintenance</h3>
        </div>
        <div class="p-0 flex-1">
            <ul class="divide-y divide-gray-100">
                <li class="p-4 hover:bg-gray-50 transition-colors cursor-pointer">
                    <p class="font-bold text-gray-800 text-sm">Monthly HVAC Inspection</p>
                    <div class="flex justify-between items-center mt-1 text-xs text-gray-500">
                        <span>HQ Building A</span>
                        <span class="font-bold text-red-500">Due Today</span>
                    </div>
                </li>
                <li class="p-4 hover:bg-gray-50 transition-colors cursor-pointer">
                    <p class="font-bold text-gray-800 text-sm">Oil Change & Filter</p>
                    <div class="flex justify-between items-center mt-1 text-xs text-gray-500">
                        <span>Delivery Truck #4</span>
                        <span class="font-bold text-yellow-600">In 3 Days</span>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>