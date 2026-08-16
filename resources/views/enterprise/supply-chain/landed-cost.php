<?php
// Path: resources/views/enterprise/supply-chain/landed-cost.php
$pageTitle = 'Landed Cost Allocation';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Landed Cost Allocation</h1>
        <p class="text-sm text-gray-500 mt-1">Distribute shipping, customs, and insurance costs to recalculate item inventory valuation.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 space-y-6">
        <div class="card p-5 border-t-4 border-t-nour-primary">
            <h3 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Step 1: Document Selection</h3>
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Goods Receipt (GRN)</label>
                    <select class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm font-medium">
                        <option>GRN-2608-042 (Alpha Tech)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cost Invoice (Shipping/Customs)</label>
                    <select class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm font-medium">
                        <option>BILL-2608-005 (DHL Logistics - $1,200)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Allocation Method</label>
                    <select class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm font-medium bg-gray-50">
                        <option value="value">By Value (Proportional to price)</option>
                        <option value="qty">By Quantity</option>
                        <option value="weight">By Weight / Volume</option>
                    </select>
                </div>
                <button type="button" class="w-full btn-primary py-2 mt-2 shadow-sm">
                    <i class="fas fa-calculator mr-2"></i> Compute Allocation
                </button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="card p-0 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Step 2: Review Apportionment</h3>
                <span class="text-xs bg-yellow-100 text-yellow-800 font-bold px-2 py-1 rounded">Draft Calculation</span>
            </div>
            <div class="table-responsive">
                <table class="w-full text-left border-collapse whitespace-nowrap text-sm">
                    <thead>
                        <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                            <th class="p-3 font-semibold">Product</th>
                            <th class="p-3 font-semibold text-center">Qty</th>
                            <th class="p-3 font-semibold text-right">Base Cost</th>
                            <th class="p-3 font-semibold text-right text-nour-primary">Additional Cost</th>
                            <th class="p-3 font-semibold text-right font-bold text-gray-900">New Unit Cost</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 font-medium text-gray-800">Dell PowerEdge R740</td>
                            <td class="p-3 text-center">10</td>
                            <td class="p-3 text-right text-gray-500">$10,000.00</td>
                            <td class="p-3 text-right font-bold text-nour-primary">+$1,000.00</td>
                            <td class="p-3 text-right font-bold text-gray-900">$1,100.00</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 font-medium text-gray-800">16GB RAM DDR4</td>
                            <td class="p-3 text-center">20</td>
                            <td class="p-3 text-right text-gray-500">$2,000.00</td>
                            <td class="p-3 text-right font-bold text-nour-primary">+$200.00</td>
                            <td class="p-3 text-right font-bold text-gray-900">$110.00</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100 border-t-2 border-gray-300 font-black text-gray-900">
                            <td class="p-3 text-right" colspan="3">Total Allocated to Inventory:</td>
                            <td class="p-3 text-right text-nour-dark">$1,200.00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-200 text-right">
                <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md font-bold shadow-md transition-colors">
                    Post & Update Inventory Valuation
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/app.php';
?>