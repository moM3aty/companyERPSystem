<?php
// Path: resources/views/treasury/receipts/index.php
$pageTitle = 'Treasury Receipts (Cash In)';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i></a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><a href="/treasury/dashboard" class="hover:text-nour-primary transition-colors">Treasury</a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Receipts</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Cash Receipts (Incoming)</h1>
        <p class="text-sm text-gray-500 mt-1">Manage all incoming funds, customer payments, and cash deposits.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-file-export mr-2 text-gray-400"></i> Export
        </button>
        <button class="btn-primary flex items-center shadow-md">
            <i class="fas fa-plus mr-2"></i> New Receipt
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card p-5 border-t-4 border-t-green-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Received (MTD)</p>
        <h3 class="text-2xl font-bold text-gray-900">$342,500.00</h3>
        <p class="text-xs text-green-600 mt-2 font-medium"><i class="fas fa-arrow-up mr-1"></i> +8.4% vs Last Month</p>
    </div>
    <div class="card p-5 border-t-4 border-t-yellow-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Unallocated Receipts</p>
        <h3 class="text-2xl font-bold text-gray-900">14</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">Needs allocation to invoices</p>
    </div>
    <div class="card p-5 border-t-4 border-t-nour-primary">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Checks Pending Clearance</p>
        <h3 class="text-2xl font-bold text-gray-900">$45,000.00</h3>
        <p class="text-xs text-blue-500 mt-2 font-medium">3 Checks in Portfolio</p>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex gap-4">
        <input type="text" placeholder="Search Receipt No, Client, Reference..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary flex-1 max-w-md">
        <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600">
            <option>All Statuses</option>
            <option>Posted</option>
            <option>Draft</option>
            <option>Voided</option>
        </select>
    </div>
    <div class="table-responsive rounded-b-lg">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Receipt No.</th>
                    <th class="p-4 font-semibold">Date</th>
                    <th class="p-4 font-semibold">Received From</th>
                    <th class="p-4 font-semibold">Treasury Account</th>
                    <th class="p-4 font-semibold text-right">Amount</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4 font-bold text-nour-primary">RC-2026-0815</td>
                    <td class="p-4 text-gray-600">Aug 15, 2026</td>
                    <td class="p-4 font-medium text-gray-900">Alpha Tech Solutions <span class="block text-[10px] text-gray-400">Ref: TRF-9981</span></td>
                    <td class="p-4 text-gray-600 text-xs">Al Rajhi Bank (Main)</td>
                    <td class="p-4 text-right font-bold text-gray-900">$28,175.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-[10px] uppercase font-bold">Posted</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary px-2"><i class="fas fa-print"></i></button>
                        <button class="text-gray-400 hover:text-blue-500 px-2"><i class="fas fa-eye"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4 font-bold text-gray-600">RC-2026-0814</td>
                    <td class="p-4 text-gray-600">Aug 14, 2026</td>
                    <td class="p-4 font-medium text-gray-900">Walk-in Customer <span class="block text-[10px] text-gray-400">Cash Payment</span></td>
                    <td class="p-4 text-gray-600 text-xs">HQ Petty Cash</td>
                    <td class="p-4 text-right font-bold text-gray-900">$1,500.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded text-[10px] uppercase font-bold">Draft</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-green-500 px-2" title="Post"><i class="fas fa-check"></i></button>
                        <button class="text-gray-400 hover:text-blue-500 px-2"><i class="far fa-edit"></i></button>
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