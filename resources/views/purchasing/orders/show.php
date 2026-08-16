<?php
// Path: resources/views/purchasing/orders/show.php
$pageTitle = 'Purchase Order Details';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 pb-4">
    <div>
        <a href="/purchasing/orders" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Orders</a>
        <h1 class="text-2xl font-bold text-gray-800">Purchase Order: <span class="text-nour-primary">PO-2026-089</span></h1>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <a href="/documents/purchase-order/default" target="_blank" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-print mr-1"></i> Print / PDF
        </a>
        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow-md text-sm font-bold transition-colors">
            <i class="fas fa-truck-loading mr-1"></i> Receive Goods
        </button>
    </div>
</div>

<!-- Status Stepper -->
<div class="card p-6 mb-6 overflow-x-auto">
    <div class="flex items-center min-w-[600px]">
        <div class="flex items-center text-green-600">
            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center font-bold border-2 border-green-500"><i class="fas fa-check"></i></div>
            <div class="ml-3"><p class="text-sm font-bold">Drafted</p><p class="text-[10px]">Aug 10, 2026</p></div>
        </div>
        <div class="flex-auto border-t-2 border-green-500 mx-4"></div>
        <div class="flex items-center text-green-600">
            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center font-bold border-2 border-green-500"><i class="fas fa-check"></i></div>
            <div class="ml-3"><p class="text-sm font-bold">Approved</p><p class="text-[10px]">Aug 11, 2026</p></div>
        </div>
        <div class="flex-auto border-t-2 border-green-500 mx-4"></div>
        <div class="flex items-center text-nour-primary">
            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center font-bold border-2 border-nour-primary animate-pulse">3</div>
            <div class="ml-3"><p class="text-sm font-bold">Sent to Vendor</p><p class="text-[10px] text-gray-500">Waiting Delivery</p></div>
        </div>
        <div class="flex-auto border-t-2 border-gray-200 mx-4"></div>
        <div class="flex items-center text-gray-400">
            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center font-bold border-2 border-gray-300">4</div>
            <div class="ml-3"><p class="text-sm font-bold">Received</p></div>
        </div>
        <div class="flex-auto border-t-2 border-gray-200 mx-4"></div>
        <div class="flex items-center text-gray-400">
            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center font-bold border-2 border-gray-300">5</div>
            <div class="ml-3"><p class="text-sm font-bold">Billed</p></div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Items Table -->
    <div class="lg:col-span-2 card p-0 overflow-hidden h-max">
        <div class="p-4 border-b border-gray-100 bg-gray-50"><h3 class="font-bold text-gray-800">Order Lines</h3></div>
        <div class="table-responsive">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-white text-gray-500 text-[10px] uppercase tracking-wider border-b border-gray-200">
                    <tr>
                        <th class="p-3 font-semibold">Product</th>
                        <th class="p-3 font-semibold text-center">Ordered</th>
                        <th class="p-3 font-semibold text-center">Received</th>
                        <th class="p-3 font-semibold text-right">Unit Price</th>
                        <th class="p-3 font-semibold text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-bold text-gray-900">Processor Alpha-Core X9<br><span class="text-[10px] text-gray-500 font-mono font-normal">CHIP-ALPH-X9</span></td>
                        <td class="p-3 text-center font-bold text-gray-800">100</td>
                        <td class="p-3 text-center text-gray-400">0</td>
                        <td class="p-3 text-right text-gray-600">$350.00</td>
                        <td class="p-3 text-right font-bold text-gray-900">$35,000.00</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-bold text-gray-900">Cooling Fan Module<br><span class="text-[10px] text-gray-500 font-mono font-normal">FAN-MOD-01</span></td>
                        <td class="p-3 text-center font-bold text-gray-800">200</td>
                        <td class="p-3 text-center text-gray-400">0</td>
                        <td class="p-3 text-right text-gray-600">$51.00</td>
                        <td class="p-3 text-right font-bold text-gray-900">$10,200.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-end">
            <div class="w-64 text-sm">
                <div class="flex justify-between py-1 text-gray-600"><span>Subtotal:</span> <span class="font-bold text-gray-900">$45,200.00</span></div>
                <div class="flex justify-between py-1 text-gray-600"><span>Tax (0%):</span> <span class="font-bold text-gray-900">$0.00</span></div>
                <div class="flex justify-between py-2 mt-1 border-t border-gray-300 font-black text-lg"><span>Grand Total:</span> <span class="text-nour-dark">$45,200.00</span></div>
            </div>
        </div>
    </div>

    <!-- Right Info -->
    <div class="lg:col-span-1 space-y-6">
        <div class="card p-5 bg-gray-50">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3 border-b border-gray-200 pb-2">Vendor Details</h3>
            <h4 class="font-black text-gray-900 text-lg">Global Tech Suppliers Inc.</h4>
            <p class="text-sm text-gray-600 mt-1">San Jose, CA 94088, USA</p>
            <p class="text-sm text-gray-600 mt-1"><i class="fas fa-envelope mr-1 text-gray-400"></i> sales@globaltech.com</p>
        </div>
        
        <div class="card p-5">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3 border-b border-gray-100 pb-2">Logistics</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Order Date:</span> <span class="font-bold text-gray-800">Aug 10, 2026</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Expected Delivery:</span> <span class="font-bold text-nour-primary">Aug 18, 2026</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Deliver To:</span> <span class="font-bold text-gray-800">HQ Riyadh WH</span></div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/app.php';
?>