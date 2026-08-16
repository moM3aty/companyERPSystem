<?php
// Path: resources/views/sales/show.php
$pageTitle = 'Sales Invoice Details';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 pb-4">
    <div>
        <a href="/sales" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Sales</a>
        <h1 class="text-2xl font-bold text-gray-800">Tax Invoice: <span class="text-nour-primary">INV-2026-089</span></h1>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <a href="/documents/invoice/sales" target="_blank" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-file-pdf mr-1 text-red-500"></i> PDF (ZATCA)
        </a>
        <button class="bg-gray-800 hover:bg-black text-white px-4 py-2 rounded shadow-md text-sm font-bold transition-colors">
            <i class="fas fa-money-check-alt mr-1"></i> Record Payment
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="card p-5 border-l-4 border-l-green-500 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Invoice Status</p>
            <h3 class="text-xl font-bold text-green-600">Posted / Unpaid</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-600 text-xl"><i class="fas fa-file-invoice"></i></div>
    </div>
    <div class="card p-5 border-l-4 border-l-nour-dark flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Grand Total</p>
            <h3 class="text-xl font-bold text-gray-900">$28,175.00</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 text-xl"><i class="fas fa-dollar-sign"></i></div>
    </div>
    <div class="card p-5 border-l-4 border-l-red-500 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Amount Due</p>
            <h3 class="text-xl font-bold text-red-600">$28,175.00</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-500 text-xl"><i class="fas fa-exclamation-circle"></i></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <div class="card p-0 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50"><h3 class="font-bold text-gray-800">Invoice Lines</h3></div>
            <div class="table-responsive">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead class="bg-white text-gray-500 text-[10px] uppercase tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="p-3 font-semibold">Description</th>
                            <th class="p-3 font-semibold text-center">Qty</th>
                            <th class="p-3 font-semibold text-right">Unit Price</th>
                            <th class="p-3 font-semibold text-right">VAT (15%)</th>
                            <th class="p-3 font-semibold text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 font-bold text-gray-900">Dell PowerEdge R740 Server</td>
                            <td class="p-3 text-center font-medium">2</td>
                            <td class="p-3 text-right text-gray-600">10,000.00</td>
                            <td class="p-3 text-right text-gray-500">3,000.00</td>
                            <td class="p-3 text-right font-bold text-gray-900">23,000.00</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 font-bold text-gray-900">Software Implementation Service</td>
                            <td class="p-3 text-center font-medium">1</td>
                            <td class="p-3 text-right text-gray-600">4,500.00</td>
                            <td class="p-3 text-right text-gray-500">675.00</td>
                            <td class="p-3 text-right font-bold text-gray-900">5,175.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card p-5 bg-gray-50 flex items-center justify-between">
            <div>
                <h4 class="font-bold text-gray-800 mb-1">Accounting Entry</h4>
                <p class="text-xs text-gray-500">Auto-posted to General Ledger.</p>
            </div>
            <a href="#" class="text-sm font-bold text-nour-primary hover:underline">View Journal Entry #JE-08012</a>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1 space-y-6">
        <div class="card p-5">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3 border-b border-gray-100 pb-2">Customer Info</h3>
            <h4 class="font-black text-gray-900 text-lg">Alpha Tech Solutions</h4>
            <p class="text-sm text-gray-600 mt-1">VAT: 300987654321003</p>
            <p class="text-sm text-gray-600 mt-1"><i class="fas fa-phone mr-1 text-gray-400"></i> +966 50 123 4567</p>
        </div>
        
        <div class="card p-5 bg-gray-50">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3 border-b border-gray-200 pb-2">Dates</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Issue Date:</span> <span class="font-bold text-gray-800">Aug 15, 2026</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Due Date:</span> <span class="font-bold text-red-600">Sep 15, 2026</span></div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>