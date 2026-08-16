<?php
// Path: resources/views/reports/custom/index.php
$pageTitle = 'Custom Report Builder';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/reports" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Report Center</a>
        <h1 class="text-2xl font-bold text-gray-800">Custom Query Builder</h1>
        <p class="text-sm text-gray-500 mt-1">Select modules and fields to generate a dynamic ad-hoc report.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-save mr-2 text-gray-400"></i> Save Query
        </button>
        <button class="btn-primary shadow-md flex items-center">
            <i class="fas fa-play mr-2"></i> Run Query
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Builder Controls -->
    <div class="lg:col-span-1 space-y-6">
        <div class="card p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2 uppercase tracking-wider">1. Select Module</h3>
            <select class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm font-medium mb-4">
                <option>Sales (Invoices & Orders)</option>
                <option>Inventory (Stock Levels)</option>
                <option>HR (Employee Data)</option>
            </select>

            <h3 class="text-sm font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2 uppercase tracking-wider">2. Select Fields</h3>
            <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar">
                <label class="flex items-center text-sm text-gray-700"><input type="checkbox" checked class="rounded text-nour-primary mr-2"> Invoice Number</label>
                <label class="flex items-center text-sm text-gray-700"><input type="checkbox" checked class="rounded text-nour-primary mr-2"> Customer Name</label>
                <label class="flex items-center text-sm text-gray-700"><input type="checkbox" checked class="rounded text-nour-primary mr-2"> Invoice Date</label>
                <label class="flex items-center text-sm text-gray-700"><input type="checkbox" checked class="rounded text-nour-primary mr-2"> Grand Total</label>
                <label class="flex items-center text-sm text-gray-700"><input type="checkbox" class="rounded text-nour-primary mr-2"> Status</label>
            </div>
        </div>

        <div class="card p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2 uppercase tracking-wider">3. Filters</h3>
            <div class="space-y-3">
                <div class="flex gap-2">
                    <select class="w-1/2 border-gray-300 rounded text-xs"><option>Invoice Date</option></select>
                    <select class="w-1/4 border-gray-300 rounded text-xs"><option>>=</option></select>
                    <input type="text" value="2026-01-01" class="w-1/4 border-gray-300 rounded text-xs">
                </div>
                <div class="flex gap-2">
                    <select class="w-1/2 border-gray-300 rounded text-xs"><option>Status</option></select>
                    <select class="w-1/4 border-gray-300 rounded text-xs"><option>=</option></select>
                    <input type="text" value="paid" class="w-1/4 border-gray-300 rounded text-xs">
                </div>
                <button class="text-xs font-bold text-nour-primary hover:underline mt-2">+ Add Condition</button>
            </div>
        </div>
    </div>

    <!-- Live Preview -->
    <div class="lg:col-span-3 card p-0 overflow-hidden flex flex-col">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800"><i class="fas fa-table mr-2 text-nour-primary"></i> Live Data Preview</h3>
            <button class="text-xs text-green-600 font-bold hover:underline"><i class="fas fa-file-excel mr-1"></i> Export CSV</button>
        </div>
        <div class="flex-1 p-6 bg-gray-100/50 flex items-center justify-center min-h-[400px]">
            <div class="text-center text-gray-400">
                <i class="fas fa-magic text-4xl mb-3 opacity-50"></i>
                <p>Select your parameters and click "Run Query" to preview data here.</p>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>