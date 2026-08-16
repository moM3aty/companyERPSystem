<?php
// Path: resources/views/reports/sales/index.php
$pageTitle = 'Sales Analytics Dashboard';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/reports" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Report Center</a>
        <h1 class="text-2xl font-bold text-gray-800">Sales Analytics</h1>
        <p class="text-sm text-gray-500 mt-1">Analyze revenue, top products, and salesperson performance.</p>
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
            <option>This Month (Aug 2026)</option>
            <option>Last Month (Jul 2026)</option>
            <option>Q3 2026</option>
            <option>Year to Date (YTD)</option>
        </select>
    </div>
    <button class="bg-nour-dark text-white px-4 py-1.5 rounded text-sm font-medium shadow-sm hover:bg-black">Refresh</button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="card p-5 h-80 flex flex-col">
        <h3 class="font-bold text-gray-800 mb-2 border-b border-gray-100 pb-2">Revenue Trend (Last 6 Months)</h3>
        <div class="flex-1 bg-gray-50 border border-dashed border-gray-200 rounded flex items-center justify-center text-gray-400">
            <i class="fas fa-chart-line text-4xl mb-2 opacity-50"></i><br>Chart.js Line Graph Here
        </div>
    </div>
    <div class="card p-5 h-80 flex flex-col">
        <h3 class="font-bold text-gray-800 mb-2 border-b border-gray-100 pb-2">Sales by Category</h3>
        <div class="flex-1 bg-gray-50 border border-dashed border-gray-200 rounded flex items-center justify-center text-gray-400">
            <i class="fas fa-chart-pie text-4xl mb-2 opacity-50"></i><br>Chart.js Doughnut Here
        </div>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="p-4 border-b border-gray-100 bg-gray-50"><h3 class="font-bold text-gray-800">Top Performing Sales Reps</h3></div>
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap text-sm">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase border-b border-gray-200">
                    <th class="p-3">Salesperson</th>
                    <th class="p-3 text-center">Invoices</th>
                    <th class="p-3 text-right">Total Revenue</th>
                    <th class="p-3 text-right text-green-600">Commission Earned</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50">
                    <td class="p-3 font-bold text-gray-900">Omar Khalid</td>
                    <td class="p-3 text-center">45</td>
                    <td class="p-3 text-right font-medium">$85,200.00</td>
                    <td class="p-3 text-right font-bold text-green-600">$4,260.00</td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="p-3 font-bold text-gray-900">Sarah Smith</td>
                    <td class="p-3 text-center">32</td>
                    <td class="p-3 text-right font-medium">$62,100.00</td>
                    <td class="p-3 text-right font-bold text-green-600">$3,105.00</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>