<?php
// Path: resources/views/enterprise/supply-chain/demand.php
$pageTitle = 'Demand Analysis & History';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Historical Demand Analysis</h1>
        <p class="text-sm text-gray-500 mt-1">Review raw consumption and sales data before running the forecasting engine.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <a href="/enterprise/supply-chain/forecasting" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-chart-line mr-2"></i> Go to Forecasts
        </a>
    </div>
</div>

<div class="card p-4 mb-6 bg-white">
    <form class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div class="col-span-1 md:col-span-2">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Search Product</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-search text-gray-400"></i></div>
                <input type="text" placeholder="Product name or SKU..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm bg-gray-50">
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Time Horizon</label>
            <select class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm bg-gray-50">
                <option>Last 3 Months</option>
                <option>Last 6 Months</option>
                <option>Last 12 Months</option>
                <option>Year to Date (YTD)</option>
            </select>
        </div>
        <div>
            <button type="button" class="w-full bg-gray-800 text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-900 transition-colors">
                Analyze Data
            </button>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Chart Placeholder -->
    <div class="card p-5 col-span-1 lg:col-span-2 flex flex-col">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Consumption Trend (Top 5 Products)</h3>
        <div class="flex-1 bg-gray-50 rounded border border-gray-200 border-dashed flex items-center justify-center min-h-[300px] relative overflow-hidden">
            <!-- Simulated Line Chart -->
            <svg class="absolute inset-0 w-full h-full text-gray-300" preserveAspectRatio="none" viewBox="0 0 100 100">
                <path d="M0,80 Q20,60 40,70 T80,40 T100,20" fill="none" stroke="currentColor" stroke-width="2"/>
                <path d="M0,90 Q30,80 50,85 T90,50 T100,45" fill="none" stroke="#005eb8" stroke-width="2" opacity="0.5"/>
            </svg>
            <div class="z-10 bg-white/90 px-4 py-2 rounded shadow-sm text-xs font-bold text-gray-500 border border-gray-200">
                <i class="fas fa-chart-area mr-1"></i> Interactive Chart Canvas
            </div>
        </div>
    </div>

    <!-- Stats Table -->
    <div class="card p-0 col-span-1">
        <div class="p-4 border-b border-gray-100 bg-gray-50"><h3 class="font-bold text-gray-800">Aggregate Demand (Last 3M)</h3></div>
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-white text-gray-500 text-xs uppercase border-b border-gray-200">
                <tr>
                    <th class="p-3">Product SKU</th>
                    <th class="p-3 text-right">Total Qty</th>
                    <th class="p-3 text-right">Trend</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                <tr class="hover:bg-gray-50">
                    <td class="p-3 font-medium text-nour-primary">HW-SRV-001</td>
                    <td class="p-3 text-right font-bold">126</td>
                    <td class="p-3 text-right text-green-500"><i class="fas fa-arrow-trend-up"></i> +12%</td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="p-3 font-medium text-nour-primary">NT-SW-024</td>
                    <td class="p-3 text-right font-bold">36</td>
                    <td class="p-3 text-right text-red-500"><i class="fas fa-arrow-trend-down"></i> -5%</td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="p-3 font-medium text-nour-primary">CBL-COP-001</td>
                    <td class="p-3 text-right font-bold">4,500</td>
                    <td class="p-3 text-right text-gray-400"><i class="fas fa-minus"></i> 0%</td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="p-3 font-medium text-nour-primary">PKG-BOX-L</td>
                    <td class="p-3 text-right font-bold">1,200</td>
                    <td class="p-3 text-right text-green-500"><i class="fas fa-arrow-trend-up"></i> +45%</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>