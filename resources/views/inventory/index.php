<?php
// Path: resources/views/inventory/index.php
$pageTitle = 'Inventory Management Dashboard';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Inventory Management</h1>
        <p class="text-sm text-gray-500 mt-1">Track stock levels, movements, valuation, and multiple warehouses.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <a href="/inventory/products" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-boxes mr-2 text-gray-400"></i> Products Catalog
        </a>
        <button class="btn-primary shadow-md">
            <i class="fas fa-exchange-alt mr-2"></i> Internal Transfer
        </button>
    </div>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-center">
        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Total Valuation</p>
        <h4 class="text-2xl font-bold text-gray-900">$452,800.00</h4>
        <div class="w-full bg-gray-100 rounded-full h-1 mt-3"><div class="bg-nour-primary h-1 rounded-full w-full"></div></div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-center">
        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Active Products</p>
        <h4 class="text-2xl font-bold text-gray-900">2,451</h4>
        <div class="w-full bg-gray-100 rounded-full h-1 mt-3"><div class="bg-green-500 h-1 rounded-full w-full"></div></div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-center border-b-4 border-b-red-500">
        <p class="text-xs font-semibold text-gray-500 uppercase mb-1 text-red-500">Low Stock Alerts</p>
        <h4 class="text-2xl font-bold text-red-600">18</h4>
        <p class="text-[10px] text-gray-400 mt-1">Below reorder point</p>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-center">
        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Warehouses</p>
        <h4 class="text-2xl font-bold text-gray-900">3</h4>
        <p class="text-[10px] text-gray-400 mt-1">Riyadh, Jeddah, Dammam</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Movements -->
    <div class="card p-0 overflow-hidden flex flex-col">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Recent Stock Movements</h3>
            <span class="text-xs text-nour-primary font-medium hover:underline cursor-pointer">View Item Ledger</span>
        </div>
        <div class="table-responsive">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-white text-gray-500 text-[10px] uppercase tracking-wider border-b border-gray-200">
                    <tr>
                        <th class="p-3 font-semibold">Product</th>
                        <th class="p-3 font-semibold text-center">Type</th>
                        <th class="p-3 font-semibold text-right">Qty</th>
                        <th class="p-3 font-semibold">Ref</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-medium text-gray-800">Dell PowerEdge R740</td>
                        <td class="p-3 text-center"><span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-[10px] font-bold">IN</span></td>
                        <td class="p-3 text-right font-bold text-green-600">+10</td>
                        <td class="p-3 text-gray-500 text-xs">GRN-2026-080</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-medium text-gray-800">Network Switch 24-Port</td>
                        <td class="p-3 text-center"><span class="bg-red-100 text-red-800 px-2 py-0.5 rounded text-[10px] font-bold">OUT</span></td>
                        <td class="p-3 text-right font-bold text-red-600">-5</td>
                        <td class="p-3 text-gray-500 text-xs">DEL-2026-042</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-medium text-gray-800">Copper Cable 5mm</td>
                        <td class="p-3 text-center"><span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-bold">TRF</span></td>
                        <td class="p-3 text-right font-bold text-gray-700">50</td>
                        <td class="p-3 text-gray-500 text-xs">TRF-2026-011</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Operations -->
    <div class="card p-5">
        <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Inventory Operations</h3>
        <div class="grid grid-cols-2 gap-4">
            <button class="p-4 border border-gray-200 rounded-lg hover:border-nour-primary hover:bg-blue-50/10 transition-all text-center">
                <i class="fas fa-clipboard-check text-2xl text-nour-primary mb-2"></i>
                <p class="font-bold text-sm text-gray-800">Stock Taking</p>
                <p class="text-[10px] text-gray-500 mt-1">Physical Inventory Count</p>
            </button>
            <button class="p-4 border border-gray-200 rounded-lg hover:border-nour-primary hover:bg-blue-50/10 transition-all text-center">
                <i class="fas fa-sliders-h text-2xl text-nour-primary mb-2"></i>
                <p class="font-bold text-sm text-gray-800">Stock Adjustment</p>
                <p class="text-[10px] text-gray-500 mt-1">Shrinkage / Damage</p>
            </button>
            <button class="p-4 border border-gray-200 rounded-lg hover:border-nour-primary hover:bg-blue-50/10 transition-all text-center">
                <i class="fas fa-barcode text-2xl text-nour-primary mb-2"></i>
                <p class="font-bold text-sm text-gray-800">Barcode Labels</p>
                <p class="text-[10px] text-gray-500 mt-1">Print product stickers</p>
            </button>
            <a href="/supply-chain/reorder-rules" class="p-4 border border-gray-200 rounded-lg hover:border-nour-primary hover:bg-blue-50/10 transition-all text-center block">
                <i class="fas fa-robot text-2xl text-nour-primary mb-2"></i>
                <p class="font-bold text-sm text-gray-800">Reorder Rules</p>
                <p class="text-[10px] text-gray-500 mt-1">Min/Max Auto Planning</p>
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>