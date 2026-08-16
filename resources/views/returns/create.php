<?php
// Path: resources/views/returns/create.php
$pageTitle = 'Process Return';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Process New Return</h1>
        <p class="text-sm text-gray-500 mt-1">Initiate a Sales Return (RMA) or Purchase Return (Debit Note).</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Content -->
    <div class="lg:col-span-2 space-y-6">
        <div class="card p-5">
            <h3 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Reference Document</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Return Type</label>
                    <select class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm">
                        <option value="sales_return">Customer Return (Sales)</option>
                        <option value="purchase_return">Supplier Return (Purchasing)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Original Invoice / Order No.</label>
                    <div class="flex gap-2">
                        <input type="text" placeholder="e.g., INV-2026-089" class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm">
                        <button class="bg-gray-100 border border-gray-300 px-3 rounded-md text-gray-600 hover:bg-gray-200 transition-colors"><i class="fas fa-search"></i> Load</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-0">
            <div class="p-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-base font-bold text-gray-800">Items to Return</h3>
            </div>
            <div class="p-4">
                <table class="w-full text-sm text-left border-collapse">
                    <thead>
                        <tr class="text-gray-500 border-b border-gray-200">
                            <th class="pb-2 font-semibold">Product</th>
                            <th class="pb-2 font-semibold text-center w-24">Invoiced Qty</th>
                            <th class="pb-2 font-semibold text-center w-24">Return Qty</th>
                            <th class="pb-2 font-semibold w-32">Condition</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-100">
                            <td class="py-3">
                                <span class="font-bold text-gray-800">Dell PowerEdge R740</span>
                                <p class="text-[10px] text-gray-500 mt-0.5">Unit Price: $10,000.00</p>
                            </td>
                            <td class="py-3 text-center text-gray-500 font-medium">2</td>
                            <td class="py-3 px-2">
                                <input type="number" max="2" min="0" value="1" class="w-full text-center border-gray-300 rounded focus:ring-nour-primary py-1 text-sm">
                            </td>
                            <td class="py-3">
                                <select class="w-full border-gray-300 rounded focus:ring-nour-primary text-xs py-1.5">
                                    <option>Good (To Stock)</option>
                                    <option>Damaged (To Scrap)</option>
                                    <option>Defective</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary & Actions -->
    <div class="lg:col-span-1 space-y-6">
        <div class="card p-5">
            <h3 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Return Details</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                    <input type="text" value="Alpha Tech Solutions" disabled class="w-full bg-gray-50 border-gray-300 rounded-md text-sm text-gray-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Return Reason</label>
                    <textarea rows="3" class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm" placeholder="Explain why the items are being returned..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Receive into Warehouse</label>
                    <select class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm">
                        <option>Main WH (Riyadh)</option>
                        <option>Returns Quarantine WH</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center text-lg font-bold text-gray-900 mb-4">
                    <span>Refund Total</span>
                    <span class="text-red-600">$10,000.00</span>
                </div>
                <button class="w-full btn-primary py-2.5 shadow-md flex justify-center items-center">
                    <i class="fas fa-check-circle mr-2"></i> Process Return
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>