<?php
// Path: resources/views/reports/inventory/index.php
$pageTitle = 'Inventory Analytics Dashboard';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/reports" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Report Center</a>
        <h1 class="text-2xl font-bold text-gray-800">Inventory Analytics</h1>
        <p class="text-sm text-gray-500 mt-1">Track valuation, dead stock, and aging across all warehouses.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card p-5 border-t-4 border-t-nour-primary">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Valuation (AVCO)</p>
        <h3 class="text-2xl font-black text-gray-900">$452,800.00</h3>
        <p class="text-xs text-gray-400 mt-1">Across 3 warehouses</p>
    </div>
    <div class="card p-5 border-t-4 border-t-red-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Dead Stock Value (>180 Days)</p>
        <h3 class="text-2xl font-black text-red-600">$12,450.00</h3>
        <p class="text-xs text-gray-400 mt-1">Requires liquidation strategy</p>
    </div>
    <div class="card p-5 border-t-4 border-t-green-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Inventory Turnover Ratio</p>
        <h3 class="text-2xl font-black text-green-600">4.2</h3>
        <p class="text-xs text-gray-400 mt-1">YTD Average</p>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Stock Aging Report</h3>
        <button class="text-xs font-medium text-green-600 hover:underline"><i class="fas fa-file-excel mr-1"></i> Download</button>
    </div>
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap text-sm">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase border-b border-gray-200">
                    <th class="p-3 font-semibold">SKU / Product</th>
                    <th class="p-3 font-semibold text-center">Total Stock</th>
                    <th class="p-3 font-semibold text-center text-green-600">< 30 Days</th>
                    <th class="p-3 font-semibold text-center text-yellow-600">30 - 90 Days</th>
                    <th class="p-3 font-semibold text-center text-red-600">> 90 Days (Slow)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50">
                    <td class="p-3 font-bold text-gray-900">DLL-XPS-15 <span class="text-xs font-normal text-gray-500 block">Dell XPS Laptop</span></td>
                    <td class="p-3 text-center font-bold">145</td>
                    <td class="p-3 text-center font-medium">100</td>
                    <td class="p-3 text-center font-medium">45</td>
                    <td class="p-3 text-center font-medium text-gray-300">0</td>
                </tr>
                <tr class="hover:bg-gray-50 bg-red-50/10">
                    <td class="p-3 font-bold text-gray-900">OLD-CBL-01 <span class="text-xs font-normal text-gray-500 block">Legacy Connector Cable</span></td>
                    <td class="p-3 text-center font-bold">500</td>
                    <td class="p-3 text-center font-medium text-gray-300">0</td>
                    <td class="p-3 text-center font-medium text-gray-300">0</td>
                    <td class="p-3 text-center font-bold text-red-600">500</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>