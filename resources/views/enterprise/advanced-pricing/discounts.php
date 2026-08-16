<?php
// Path: resources/views/enterprise/advanced-pricing/discounts.php
$pageTitle = 'Discount Matrices';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Advanced Discount Matrices</h1>
        <p class="text-sm text-gray-500 mt-1">Configure volume breaks, BOGO offers, and category-wide pricing rules.</p>
    </div>
    <button class="btn-primary shadow-md">
        <i class="fas fa-plus mr-2"></i> Create Matrix Rule
    </button>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Rule Name</th>
                    <th class="p-4 font-semibold">Target Scope</th>
                    <th class="p-4 font-semibold">Condition (Break)</th>
                    <th class="p-4 font-semibold">Discount Applied</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4 font-bold text-gray-900">Wholesale IT Equipment</td>
                    <td class="p-4"><span class="bg-purple-100 text-purple-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Category: Hardware</span></td>
                    <td class="p-4 font-medium text-gray-600">Buy ≥ 50 Units</td>
                    <td class="p-4 font-black text-green-600">-15% Off Total</td>
                    <td class="p-4 text-center"><span class="text-green-500"><i class="fas fa-check-circle"></i> Active</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary px-2"><i class="fas fa-edit"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4 font-bold text-gray-900">Summer BOGO Cable Deal</td>
                    <td class="p-4"><span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Product: Copper Cable</span></td>
                    <td class="p-4 font-medium text-gray-600">Buy exactly 2 Units</td>
                    <td class="p-4 font-black text-nour-primary">100% Off 2nd Item</td>
                    <td class="p-4 text-center"><span class="text-green-500"><i class="fas fa-check-circle"></i> Active</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary px-2"><i class="fas fa-edit"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/app.php';
?>