    <?php
// Path: resources/views/inventory/adjustments/create.php
$pageTitle = 'Create Stock Adjustment';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">New Stock Adjustment</h1>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-3">
        <a href="/inventory/adjustments" class="bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">Cancel</a>
        <button type="submit" form="adjustmentForm" class="btn-primary px-8 shadow-md">Save Draft</button>
    </div>
</div>

<form id="adjustmentForm" action="/api/v1/inventory/adjustments" method="POST">
    <div class="card p-5 mb-6 border-t-4 border-t-nour-primary">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Warehouse / Location <span class="text-red-500">*</span></label>
                <select class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm bg-gray-50" required>
                    <option value="">Select Warehouse...</option>
                    <option value="1">HQ - Main Riyadh</option>
                    <option value="2">Jeddah Distribution</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adjustment Reason <span class="text-red-500">*</span></label>
                <select class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm" required>
                    <option value="inventory_count">Physical Inventory Count</option>
                    <option value="damage">Damage / Spoilage / Scrap</option>
                    <option value="initial_balance">Initial Balance (Data Migration)</option>
                    <option value="internal_use">Internal Consumption</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" value="<?= date('Y-m-d') ?>" class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm">
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes / Description</label>
                <input type="text" placeholder="Optional context..." class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm">
            </div>
        </div>
    </div>

    <div class="card p-0">
        <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="text-base font-bold text-gray-800">Adjusted Items</h3>
            <div class="flex gap-2 text-xs">
                <span class="bg-red-50 text-red-700 px-2 py-1 rounded border border-red-100"><i class="fas fa-exclamation-circle mr-1"></i> Cost of adjustment will hit P&L upon posting.</span>
            </div>
        </div>
        <div class="p-4">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="text-gray-500 border-b border-gray-200">
                        <th class="pb-2 font-semibold w-1/3">Product</th>
                        <th class="pb-2 font-semibold w-24 text-center">On Hand (System)</th>
                        <th class="pb-2 font-semibold w-32 text-center">Real Counted Qty</th>
                        <th class="pb-2 font-semibold w-24 text-center">Difference</th>
                        <th class="pb-2 font-semibold w-10"></th>
                    </tr>
                </thead>
                <tbody id="adjustment-lines">
                    <tr class="border-b border-gray-100">
                        <td class="py-3 pr-2">
                            <select class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5 font-medium text-gray-800">
                                <option>Dell PowerEdge R740 Server (SKU: HW-SRV-001)</option>
                            </select>
                        </td>
                        <td class="py-3 pr-2 text-center text-gray-500 font-mono">145.00</td>
                        <td class="py-3 pr-2">
                            <input type="number" step="0.01" value="142.00" class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5 text-center font-bold text-gray-900 bg-yellow-50">
                        </td>
                        <td class="py-3 pr-2 text-center font-bold text-red-600">-3.00</td>
                        <td class="py-3 text-center">
                            <button type="button" class="text-red-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="mt-4 text-sm font-bold text-nour-primary hover:text-nour-dark"><i class="fas fa-plus-circle mr-1"></i> Add Product Line</button>
        </div>
    </div>
</form>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/app.php';
?>