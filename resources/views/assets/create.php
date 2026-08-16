<?php
// Path: resources/views/assets/create.php
$pageTitle = 'Register Fixed Asset';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/assets" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Register</a>
        <h1 class="text-2xl font-bold text-gray-800">Register New Fixed Asset</h1>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-3">
        <button type="submit" form="assetForm" class="btn-primary px-8 shadow-md">Save Asset</button>
    </div>
</div>

<form id="assetForm" action="/api/v1/assets" method="POST">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Asset Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Asset Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="e.g., Delivery Truck 2026" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="1">Vehicles</option>
                            <option value="2">IT Equipment</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Purchase Date</label>
                        <input type="date" name="purchase_date" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Serial Number / VIN</label>
                        <input type="text" name="serial_number" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="card p-6 bg-gray-50 border-t-4 border-t-nour-primary">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">Financials (Depreciation)</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Gross Purchase Value</label>
                        <input type="number" step="0.01" name="purchase_value" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm text-right font-bold">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Depreciation Method</label>
                        <select name="depreciation_method" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="straight_line">Straight Line</option>
                            <option value="declining_balance">Declining Balance</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Useful Life (Years)</label>
                        <input type="number" name="useful_life" value="5" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm text-right">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Salvage Value</label>
                        <input type="number" step="0.01" name="salvage_value" value="0.00" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm text-right text-gray-500">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>