<?php
// Path: resources/views/closing/index.php
$pageTitle = 'Period Closing';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Financial Period Closing</h1>
        <p class="text-sm text-gray-500 mt-1">Lock accounting periods, execute depreciation, and generate consolidated reports.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="card p-6 border-t-4 border-t-nour-primary flex flex-col justify-between">
        <div>
            <div class="w-12 h-12 bg-nour-light/20 text-nour-primary rounded-lg flex items-center justify-center text-xl mb-4">
                <i class="fas fa-lock"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Month-End Close</h3>
            <p class="text-sm text-gray-500 mb-4">Lock the period to prevent any further journal entries. Generates closing balances.</p>
            <div class="p-3 bg-gray-50 rounded border border-gray-200 text-sm font-medium mb-4">
                Current Open Period: <span class="text-nour-dark font-bold">August 2026</span>
            </div>
        </div>
        <button class="w-full btn-primary bg-red-600 hover:bg-red-700 transition-colors border-none" onclick="confirm('Are you sure you want to lock August 2026?')">
            Lock Period
        </button>
    </div>

    <div class="card p-6 border-t-4 border-t-yellow-500 flex flex-col justify-between">
        <div>
            <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center text-xl mb-4">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Run Depreciation</h3>
            <p class="text-sm text-gray-500 mb-4">Execute fixed asset depreciation algorithms and post automatic journal entries.</p>
        </div>
        <button class="w-full bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 py-2 rounded-md font-medium transition-colors shadow-sm">
            Execute Depreciation
        </button>
    </div>

    <div class="card p-6 border-t-4 border-t-green-500 flex flex-col justify-between">
        <div>
            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center text-xl mb-4">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Inventory Valuation</h3>
            <p class="text-sm text-gray-500 mb-4">Capture a snapshot of all warehouse stock values using current Moving Average Cost.</p>
        </div>
        <button class="w-full bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 py-2 rounded-md font-medium transition-colors shadow-sm">
            Generate Snapshot
        </button>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>