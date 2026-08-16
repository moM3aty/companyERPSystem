<?php
// Path: resources/views/purchasing/invoices/index.php
$pageTitle = 'Purchase Invoices (Bills)';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Accounts Payable (Vendor Bills)</h1>
        <p class="text-sm text-gray-500 mt-1">Manage supplier invoices, track due dates, and queue payments.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="btn-primary shadow-md">
            <i class="fas fa-plus mr-2"></i> Register Bill
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card p-5 border-t-4 border-t-red-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Overdue Payables</p>
        <h3 class="text-2xl font-bold text-red-600">$45,200.00</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">Across 4 vendor bills</p>
    </div>
    <div class="card p-5 border-t-4 border-t-yellow-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Due Next 7 Days</p>
        <h3 class="text-2xl font-bold text-gray-900">$18,400.00</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">Cash flow readiness needed</p>
    </div>
    <div class="card p-5 border-t-4 border-t-green-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Paid (MTD)</p>
        <h3 class="text-2xl font-bold text-gray-900">$124,500.00</h3>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <input type="text" placeholder="Search Bill No, Supplier..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-64">
        <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600">
            <option>All Unpaid</option>
            <option>Overdue Only</option>
            <option>Paid</option>
        </select>
    </div>
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Bill No.</th>
                    <th class="p-4 font-semibold">Supplier</th>
                    <th class="p-4 font-semibold">Bill Date</th>
                    <th class="p-4 font-semibold">Due Date</th>
                    <th class="p-4 font-semibold text-right">Total Amount</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-red-50/10 transition-colors group bg-red-50/20">
                    <td class="p-4 font-bold text-nour-primary">BILL-2607-042</td>
                    <td class="p-4 font-medium text-gray-900">Alpha Manufacturing <span class="block text-[10px] text-gray-500">Ref: INV-9921</span></td>
                    <td class="p-4 text-gray-600">Jul 15, 2026</td>
                    <td class="p-4 font-bold text-red-600">Aug 15, 2026</td>
                    <td class="p-4 text-right font-bold text-gray-900">$12,400.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-red-100 text-red-800 rounded text-[10px] uppercase font-bold">Overdue</span></td>
                    <td class="p-4 text-right">
                        <button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-2 py-1 rounded text-xs font-medium transition-colors shadow-sm">Pay Now</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4 font-bold text-nour-primary">BILL-2608-011</td>
                    <td class="p-4 font-medium text-gray-900">Global Tech Suppliers</td>
                    <td class="p-4 text-gray-600">Aug 16, 2026</td>
                    <td class="p-4 font-medium text-gray-800">Sep 16, 2026</td>
                    <td class="p-4 text-right font-bold text-gray-900">$35,000.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded text-[10px] uppercase font-bold">Unpaid</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary px-2"><i class="fas fa-eye"></i></button>
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