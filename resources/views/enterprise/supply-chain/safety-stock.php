<?php
// Path: resources/views/enterprise/supply-chain/safety-stock.php
$pageTitle = 'Safety Stock Limits';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Safety Stock Monitoring</h1>
        <p class="text-sm text-gray-500 mt-1">Ensure critical products never run out based on calculated lead times and daily demand.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="btn-primary shadow-md">
            <i class="fas fa-calculator mr-2"></i> Recalculate Minimums
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card p-5 border-l-4 border-l-red-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Critical Shortages</p>
        <h3 class="text-3xl font-bold text-red-600">12</h3>
        <p class="text-xs text-red-400 mt-2 font-medium">Items currently below safety stock.</p>
    </div>
    <div class="card p-5 border-l-4 border-l-yellow-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Approaching Limit</p>
        <h3 class="text-3xl font-bold text-yellow-600">34</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">Items within 10% of safety limit.</p>
    </div>
    <div class="card p-5 border-l-4 border-l-green-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Healthy Stock</p>
        <h3 class="text-3xl font-bold text-green-600">2,405</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">Items with sufficient buffers.</p>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <input type="text" placeholder="Search Product SKU..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-64">
        <span class="text-xs text-gray-500"><i class="fas fa-info-circle text-blue-500 mr-1"></i> Formula: (Max Daily Usage * Max Lead Time) - (Avg Daily Usage * Avg Lead Time)</span>
    </div>
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">SKU / Product</th>
                    <th class="p-4 font-semibold text-center">Avg Lead Time</th>
                    <th class="p-4 font-semibold text-right">Daily Demand</th>
                    <th class="p-4 font-semibold text-right">Calculated Safety</th>
                    <th class="p-4 font-semibold text-right text-nour-dark">Current Stock</th>
                    <th class="p-4 font-semibold text-center">Health</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-red-50/20 transition-colors group bg-red-50/10">
                    <td class="p-4">
                        <span class="font-bold text-gray-900 block">Copper Cable 5mm</span>
                        <span class="text-xs text-gray-500 font-mono">CBL-COP-001</span>
                    </td>
                    <td class="p-4 text-center text-gray-600">14 Days</td>
                    <td class="p-4 text-right text-gray-600">45 M</td>
                    <td class="p-4 text-right font-bold text-gray-800">500 M</td>
                    <td class="p-4 text-right font-black text-red-600">120 M</td>
                    <td class="p-4 text-center">
                        <span class="bg-red-100 text-red-800 px-2 py-0.5 rounded text-[10px] uppercase font-bold border border-red-200">Deficit</span>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4">
                        <span class="font-bold text-gray-900 block">Server R740 Motherboard</span>
                        <span class="text-xs text-gray-500 font-mono">MB-SRV-740</span>
                    </td>
                    <td class="p-4 text-center text-gray-600">30 Days</td>
                    <td class="p-4 text-right text-gray-600">2 Units</td>
                    <td class="p-4 text-right font-bold text-gray-800">40 Units</td>
                    <td class="p-4 text-right font-black text-green-600">85 Units</td>
                    <td class="p-4 text-center">
                        <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-[10px] uppercase font-bold border border-green-200">Healthy</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>