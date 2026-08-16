<?php
// Path: resources/views/sales/create.php
$pageTitle = 'Create Sales Order';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">New Sales Document</h1>
        <p class="text-sm text-gray-500 mt-1">Create a Quotation, Sales Order, or Direct Tax Invoice.</p>
    </div>
</div>

<form action="/api/v1/sales/orders" method="POST">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Items (Takes up 2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-0">
                <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-800">Line Items</h3>
                </div>
                <div class="p-4">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="text-gray-500 border-b border-gray-200">
                                <th class="pb-2 font-semibold w-1/2">Product / Service</th>
                                <th class="pb-2 font-semibold w-24">Qty</th>
                                <th class="pb-2 font-semibold w-32">Unit Price</th>
                                <th class="pb-2 font-semibold w-24 text-right">Total</th>
                                <th class="pb-2 font-semibold w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="items-container">
                            <tr class="border-b border-gray-100">
                                <td class="py-3 pr-2">
                                    <select class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5" name="items[0][product_id]">
                                        <option value="1">Dell PowerEdge R740 Server</option>
                                        <option value="2">Software Implementation</option>
                                    </select>
                                </td>
                                <td class="py-3 pr-2">
                                    <input type="number" value="1" class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5" name="items[0][quantity]">
                                </td>
                                <td class="py-3 pr-2">
                                    <input type="number" value="10000" class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5" name="items[0][unit_price]">
                                </td>
                                <td class="py-3 text-right font-semibold text-gray-800">
                                    10,000.00
                                </td>
                                <td class="py-3 text-right">
                                    <button type="button" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="mt-4 text-sm font-medium text-nour-primary hover:text-nour-dark flex items-center">
                        <i class="fas fa-plus-circle mr-1"></i> Add Item Line
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: Meta & Totals -->
        <div class="lg:col-span-1 space-y-6">
            <div class="card p-5">
                <h3 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Document Details</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Customer</label>
                        <select class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm" name="customer_id" required>
                            <option value="1">Alpha Tech Solutions</option>
                            <option value="2">Global Pharma Group</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Document Type</label>
                        <select class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm" name="doc_type">
                            <option value="sales_order">Sales Order (Reserve Stock)</option>
                            <option value="tax_invoice">Direct Tax Invoice (B2B)</option>
                            <option value="quotation">Quotation (Draft)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Date</label>
                        <input type="date" class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm" name="order_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
            </div>

            <div class="card p-5 bg-gray-50">
                <h3 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">Financial Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-semibold">10,000.00 SAR</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>VAT (15%)</span>
                        <span class="font-semibold">1,500.00 SAR</span>
                    </div>
                    <div class="pt-2 border-t border-gray-200 mt-2 flex justify-between items-center text-lg font-bold text-gray-900">
                        <span>Grand Total</span>
                        <span class="text-nour-primary">11,500.00 SAR</span>
                    </div>
                </div>
                
                <button type="submit" class="w-full btn-primary mt-6 py-3 text-lg shadow-lg">
                    Confirm & Save
                </button>
            </div>
        </div>

    </div>
</form>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>