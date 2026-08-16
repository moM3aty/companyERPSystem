<?php
// Path: resources/views/enterprise/advanced-pricing/price-rules.php
$pageTitle = 'Dynamic Pricing Rules';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/enterprise/advanced-pricing/dashboard" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Dashboard</a>
        <h1 class="text-2xl font-bold text-gray-800">Dynamic Pricing Rules (Formula-based)</h1>
        <p class="text-sm text-gray-500 mt-1">Set complex IF/THEN rules for automatic price adjustments globally.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="btn-primary shadow-md">
            <i class="fas fa-plus mr-2"></i> Create New Rule
        </button>
    </div>
</div>

<div class="space-y-4">
    <!-- Rule Item -->
    <div class="card p-0 border-l-4 border-l-nour-primary overflow-hidden hover:shadow-md transition-shadow">
        <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-900 text-lg">Clearance Sale: Old Electronics</h3>
            <div class="flex items-center gap-3">
                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded font-bold uppercase">Active</span>
                <button class="text-gray-400 hover:text-blue-500"><i class="far fa-edit"></i></button>
            </div>
        </div>
        <div class="p-5 flex flex-col md:flex-row gap-6 items-center">
            <div class="flex-1 bg-gray-100 rounded p-4 font-mono text-sm text-gray-700 border border-gray-200 w-full">
                <span class="text-purple-600 font-bold">IF</span> Category <span class="text-blue-600">EQUALS</span> 'Electronics'<br>
                <span class="text-purple-600 font-bold">AND</span> Days_In_Stock <span class="text-blue-600">GREATER_THAN</span> 365
            </div>
            <div class="text-2xl text-gray-300"><i class="fas fa-arrow-right"></i></div>
            <div class="flex-1 bg-green-50 rounded p-4 font-mono text-sm text-green-800 border border-green-200 w-full font-bold">
                <span class="text-purple-600">THEN</span> Apply Discount = 25%
            </div>
        </div>
    </div>

    <!-- Rule Item -->
    <div class="card p-0 border-l-4 border-l-yellow-500 overflow-hidden hover:shadow-md transition-shadow">
        <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-900 text-lg">Weekend Surcharge (Services)</h3>
            <div class="flex items-center gap-3">
                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded font-bold uppercase">Scheduled</span>
                <button class="text-gray-400 hover:text-blue-500"><i class="far fa-edit"></i></button>
            </div>
        </div>
        <div class="p-5 flex flex-col md:flex-row gap-6 items-center">
            <div class="flex-1 bg-gray-100 rounded p-4 font-mono text-sm text-gray-700 border border-gray-200 w-full">
                <span class="text-purple-600 font-bold">IF</span> Product_Type <span class="text-blue-600">EQUALS</span> 'Service'<br>
                <span class="text-purple-600 font-bold">AND</span> Day_Of_Week <span class="text-blue-600">IN</span> ('Friday', 'Saturday')
            </div>
            <div class="text-2xl text-gray-300"><i class="fas fa-arrow-right"></i></div>
            <div class="flex-1 bg-red-50 rounded p-4 font-mono text-sm text-red-800 border border-red-200 w-full font-bold">
                <span class="text-purple-600">THEN</span> Price Multiplier = 1.15x
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>