<?php
// Path: resources/views/enterprise/advanced-pricing/price-lists.php
$pageTitle = 'Price Lists';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Customer Price Lists & Volume Pricing</h1>
        <p class="text-sm text-gray-500 mt-1">Define specific pricing tiers and quantity break discounts.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="btn-primary shadow-md">
            <i class="fas fa-plus mr-2"></i> Create Price List
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- List Sidebar -->
    <div class="lg:col-span-1 space-y-4">
        <div class="card p-0 overflow-hidden">
            <div class="p-4 bg-nour-primary text-white font-bold flex justify-between items-center">
                <span>Active Lists</span>
                <span class="bg-white/20 px-2 py-0.5 rounded text-xs">2</span>
            </div>
            <ul class="divide-y divide-gray-100 text-sm cursor-pointer">
                <li class="p-4 hover:bg-gray-50 bg-blue-50/50 border-l-4 border-l-nour-primary transition-colors">
                    <div class="font-bold text-gray-900">VIP Wholesale Tier 1</div>
                    <div class="text-xs text-gray-500 mt-1">Currency: SAR | Linked Customers: 45</div>
                </li>
                <li class="p-4 hover:bg-gray-50 border-l-4 border-l-transparent transition-colors">
                    <div class="font-bold text-gray-900">Government B2G List</div>
                    <div class="text-xs text-gray-500 mt-1">Currency: SAR | Valid until: 2026-12-31</div>
                </li>
            </ul>
        </div>
    </div>

    <!-- Rules Configuration -->
    <div class="lg:col-span-2">
        <div class="card p-0">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-base font-bold text-gray-800">Rules for: VIP Wholesale Tier 1</h3>
                <button class="text-sm text-nour-primary font-medium hover:underline"><i class="fas fa-plus-circle mr-1"></i> Add Rule</button>
            </div>
            <div class="table-responsive">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                            <th class="p-4 font-semibold">Product</th>
                            <th class="p-4 font-semibold text-center">Min. Qty (Volume)</th>
                            <th class="p-4 font-semibold text-right">Fixed Price</th>
                            <th class="p-4 font-semibold text-right">OR Discount %</th>
                            <th class="p-4 font-semibold w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 font-medium text-gray-900">Dell PowerEdge R740</td>
                            <td class="p-4 text-center text-gray-600">1</td>
                            <td class="p-4 text-right"><input type="text" value="9500.00" class="w-24 border border-gray-300 rounded px-2 py-1 text-sm text-right focus:ring-nour-primary"></td>
                            <td class="p-4 text-right text-gray-400">-</td>
                            <td class="p-4 text-right"><button class="text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button></td>
                        </tr>
                        <tr class="hover:bg-gray-50 bg-gray-50/50">
                            <td class="p-4 font-medium text-gray-900">Dell PowerEdge R740</td>
                            <td class="p-4 text-center font-bold text-nour-primary">10 <span class="text-[10px] text-gray-400 font-normal block">Volume Break</span></td>
                            <td class="p-4 text-right text-gray-400">-</td>
                            <td class="p-4 text-right"><input type="text" value="15.0" class="w-16 border border-gray-300 rounded px-2 py-1 text-sm text-right focus:ring-nour-primary"> %</td>
                            <td class="p-4 text-right"><button class="text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 bg-gray-50 text-right">
                <button class="btn-primary px-6">Save Price Rules</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>