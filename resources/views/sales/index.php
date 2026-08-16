<?php
// Path: resources/views/sales/index.php
$pageTitle = 'Sales Dashboard & Orders';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i> Home</a>
                </li>
                <li><i class="fas fa-chevron-right text-xs mx-2"></i><span>Sales</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Sales Orders & Invoices</h1>
        <p class="text-sm text-gray-500 mt-1">Manage your customer orders, generate invoices, and track revenue.</p>
    </div>
    
    <div class="mt-4 sm:mt-0 flex gap-3">
        <a href="/sales/create" class="btn-primary flex items-center shadow-md shadow-nour-primary/30">
            <i class="fas fa-plus mr-2"></i> Create New Sale
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="card p-5 border-l-4 border-l-nour-primary">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Revenue (MTD)</p>
        <h3 class="text-2xl font-bold text-gray-900">$1,452,000</h3>
        <p class="text-xs text-green-600 mt-2 font-medium"><i class="fas fa-arrow-up mr-1"></i> 14.5% vs Last Month</p>
    </div>
    <div class="card p-5 border-l-4 border-l-yellow-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pending Orders</p>
        <h3 class="text-2xl font-bold text-gray-900">42</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">Awaiting fulfillment / shipping</p>
    </div>
    <div class="card p-5 border-l-4 border-l-red-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Overdue Invoices</p>
        <h3 class="text-2xl font-bold text-gray-900">8</h3>
        <p class="text-xs text-red-500 mt-2 font-medium">Value: $45,300.00</p>
    </div>
    <div class="card p-5 border-l-4 border-l-green-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Conversion Rate</p>
        <h3 class="text-2xl font-bold text-gray-900">68.2%</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">Quotations to Orders</p>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <h3 class="text-base font-bold text-gray-800">Recent Sales Documents</h3>
        <div class="flex gap-2">
            <input type="text" placeholder="Search orders..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary focus:border-nour-primary">
            <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary">
                <option>All Types</option>
                <option>Sales Orders</option>
                <option>Tax Invoices</option>
                <option>Quotations</option>
            </select>
        </div>
    </div>
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Document No.</th>
                    <th class="p-4 font-semibold">Customer</th>
                    <th class="p-4 font-semibold">Date</th>
                    <th class="p-4 font-semibold text-right">Amount</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-nour-primary">INV-2026-089</td>
                    <td class="p-4 font-medium">Alpha Tech Solutions</td>
                    <td class="p-4">Aug 15, 2026</td>
                    <td class="p-4 text-right font-bold text-gray-900">$28,175.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">Posted / Paid</span></td>
                    <td class="p-4 text-right">
                        <a href="/documents/invoice/sales" target="_blank" class="text-gray-400 hover:text-nour-primary p-1"><i class="fas fa-print"></i></a>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-nour-primary">SO-2026-042</td>
                    <td class="p-4 font-medium">Global Pharma Group</td>
                    <td class="p-4">Aug 14, 2026</td>
                    <td class="p-4 text-right font-bold text-gray-900">$12,400.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium">Processing</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-blue-500 p-1"><i class="fas fa-eye"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>