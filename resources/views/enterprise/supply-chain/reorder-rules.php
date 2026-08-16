<?php
// Path: resources/views/enterprise/supply-chain/reorder-rules.php
$pageTitle = 'Reorder Rules';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Reorder Rules (Min/Max Policies)</h1>
        <p class="text-sm text-gray-500 mt-1">Configure thresholds for automated replenishment via the MRP engine.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-file-import mr-2"></i> Import Policies
        </button>
        <button class="btn-primary shadow-md">
            <i class="fas fa-plus mr-2"></i> Add Rule
        </button>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-4 border-b border-gray-100 flex gap-4 bg-gray-50/50">
        <input type="text" placeholder="Search SKU or Product..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-64">
        <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-48">
            <option>HQ - Riyadh Warehouse</option>
            <option>Jeddah Warehouse</option>
        </select>
    </div>
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Product</th>
                    <th class="p-4 font-semibold text-right">Min Quantity (Reorder Point)</th>
                    <th class="p-4 font-semibold text-right">Max Quantity (Target)</th>
                    <th class="p-4 font-semibold text-center">Lead Time (Days)</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4">
                        <span class="font-bold text-gray-900 block">Copper Cable 5mm</span>
                        <span class="text-xs text-gray-500 font-mono">CBL-COP-001</span>
                    </td>
                    <td class="p-4 text-right">
                        <input type="number" value="500" class="w-20 border-gray-300 rounded text-sm text-right px-2 py-1 focus:ring-nour-primary">
                    </td>
                    <td class="p-4 text-right">
                        <input type="number" value="2000" class="w-20 border-gray-300 rounded text-sm text-right px-2 py-1 focus:ring-nour-primary">
                    </td>
                    <td class="p-4 text-center">
                        <input type="number" value="14" class="w-16 border-gray-300 rounded text-sm text-center px-2 py-1 focus:ring-nour-primary">
                    </td>
                    <td class="p-4 text-center">
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-[10px] font-bold uppercase">Active</span>
                    </td>
                    <td class="p-4 text-right">
                        <button class="text-green-600 hover:text-green-800 p-1" title="Save Rule"><i class="fas fa-save"></i></button>
                        <button class="text-red-400 hover:text-red-600 p-1 ml-2" title="Delete"><i class="far fa-trash-alt"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4">
                        <span class="font-bold text-gray-900 block">Processor Unit X9</span>
                        <span class="text-xs text-gray-500 font-mono">CHIP-INT-X9</span>
                    </td>
                    <td class="p-4 text-right">
                        <input type="number" value="20" class="w-20 border-gray-300 rounded text-sm text-right px-2 py-1 focus:ring-nour-primary">
                    </td>
                    <td class="p-4 text-right">
                        <input type="number" value="100" class="w-20 border-gray-300 rounded text-sm text-right px-2 py-1 focus:ring-nour-primary">
                    </td>
                    <td class="p-4 text-center">
                        <input type="number" value="30" class="w-16 border-gray-300 rounded text-sm text-center px-2 py-1 focus:ring-nour-primary">
                    </td>
                    <td class="p-4 text-center">
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-[10px] font-bold uppercase">Active</span>
                    </td>
                    <td class="p-4 text-right">
                        <button class="text-green-600 hover:text-green-800 p-1" title="Save Rule"><i class="fas fa-save"></i></button>
                        <button class="text-red-400 hover:text-red-600 p-1 ml-2" title="Delete"><i class="far fa-trash-alt"></i></button>
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