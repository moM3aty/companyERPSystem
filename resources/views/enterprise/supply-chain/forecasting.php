<?php
// Path: resources/views/enterprise/supply-chain/forecasting.php
$pageTitle = 'Demand Forecasting';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Demand Forecasting Engine</h1>
        <p class="text-sm text-gray-500 mt-1">Algorithmic predictions for future product demand based on historical sales data.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="btn-primary shadow-md flex items-center">
            <i class="fas fa-magic mr-2"></i> Run Algorithm (SMA 90-Days)
        </button>
    </div>
</div>

<div class="card p-0 overflow-hidden mb-6">
    <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Forecast Results for: <span class="text-nour-primary">September 2026</span></h3>
        <div class="flex gap-4 text-sm font-medium">
            <span class="text-gray-500"><i class="fas fa-check-circle text-green-500 mr-1"></i> Calculated Today</span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">SKU / Product</th>
                    <th class="p-4 font-semibold text-right">Avg Monthly Sales (Last 3M)</th>
                    <th class="p-4 font-semibold text-right text-nour-primary">Predicted Demand</th>
                    <th class="p-4 font-semibold text-center">Confidence Score</th>
                    <th class="p-4 font-semibold text-center">Action</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4">
                        <span class="font-bold text-gray-900 block">Server R740</span>
                        <span class="text-xs text-gray-500 font-mono">HW-SRV-001</span>
                    </td>
                    <td class="p-4 text-right text-gray-600">42 Units</td>
                    <td class="p-4 text-right font-black text-nour-primary text-lg">45 Units</td>
                    <td class="p-4 text-center">
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold">89.5%</span>
                    </td>
                    <td class="p-4 text-center">
                        <button class="text-xs bg-white border border-gray-300 px-2 py-1 rounded hover:bg-gray-50 font-medium">Adjust Safety Stock</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4">
                        <span class="font-bold text-gray-900 block">Network Switch 24-Port</span>
                        <span class="text-xs text-gray-500 font-mono">NT-SW-024</span>
                    </td>
                    <td class="p-4 text-right text-gray-600">12 Units</td>
                    <td class="p-4 text-right font-black text-nour-primary text-lg">15 Units</td>
                    <td class="p-4 text-center">
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-bold">65.2%</span>
                    </td>
                    <td class="p-4 text-center">
                        <button class="text-xs bg-white border border-gray-300 px-2 py-1 rounded hover:bg-gray-50 font-medium">Adjust Safety Stock</button>
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