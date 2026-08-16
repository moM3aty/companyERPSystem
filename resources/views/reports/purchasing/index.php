<?php
// Path: resources/views/reports/purchasing/index.php
$pageTitle = 'Purchasing Analytics';
ob_start();
?>
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/reports" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Report Center</a>
        <h1 class="text-2xl font-bold text-gray-800">Purchasing & Spend Analytics</h1>
        <p class="text-sm text-gray-500 mt-1">Analyze supplier performance, procurement spending, and price variances.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-file-excel mr-2 text-green-600"></i> Export Data
        </button>
    </div>
</div>

<div class="card p-4 mb-6 bg-white flex gap-4 items-end">
    <div class="w-48">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Period</label>
        <select class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm font-medium bg-gray-50 py-1.5">
            <option>Year to Date (YTD)</option>
            <option>Q3 2026</option>
            <option>Last Month</option>
        </select>
    </div>
    <button class="bg-nour-dark text-white px-4 py-1.5 rounded text-sm font-medium shadow-sm hover:bg-black">Refresh</button>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="card p-5 border-t-4 border-t-nour-primary">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Spend (YTD)</p>
        <h3 class="text-2xl font-black text-gray-900">$1,452,000</h3>
    </div>
    <div class="card p-5 border-t-4 border-t-blue-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Active Suppliers</p>
        <h3 class="text-2xl font-black text-gray-900">42</h3>
    </div>
    <div class="card p-5 border-t-4 border-t-green-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">On-Time Delivery Rate</p>
        <h3 class="text-2xl font-black text-green-600">94.5%</h3>
    </div>
    <div class="card p-5 border-t-4 border-t-red-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Price Variance (PPV)</p>
        <h3 class="text-2xl font-black text-red-600">+$12,400</h3>
        <p class="text-[10px] text-gray-400 mt-1">Over standard cost</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="card p-5 h-80 flex flex-col">
        <h3 class="font-bold text-gray-800 mb-2 border-b border-gray-100 pb-2">Spend by Category</h3>
        <div class="flex-1 bg-gray-50 border border-dashed border-gray-200 rounded flex items-center justify-center text-gray-400">
            <i class="fas fa-chart-pie text-4xl mb-2 opacity-50"></i><br>Chart.js Doughnut Here
        </div>
    </div>
    
    <div class="card p-0 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50"><h3 class="font-bold text-gray-800">Top Suppliers by Volume</h3></div>
        <div class="table-responsive">
            <table class="w-full text-left border-collapse whitespace-nowrap text-sm">
                <thead>
                    <tr class="bg-white text-gray-500 text-xs uppercase border-b border-gray-200">
                        <th class="p-3">Supplier Name</th>
                        <th class="p-3 text-center">Orders</th>
                        <th class="p-3 text-right">Total Spend</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-bold text-gray-900">Global Tech Suppliers</td>
                        <td class="p-3 text-center font-medium">14</td>
                        <td class="p-3 text-right font-bold text-nour-primary">$450,200.00</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-bold text-gray-900">Alpha Manufacturing</td>
                        <td class="p-3 text-center font-medium">8</td>
                        <td class="p-3 text-right font-bold text-nour-primary">$210,000.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>