<?php
// Path: resources/views/crm/show.php
$pageTitle = 'Customer Profile: Alpha Tech Solutions';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i></a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><a href="/crm/customers" class="hover:text-nour-primary transition-colors">Customers</a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Alpha Tech</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Alpha Tech Solutions</h1>
        <p class="text-sm text-gray-500 mt-1"><i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> Jeddah, KSA &bull; <span class="text-green-600 font-medium">Active Client</span></p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-envelope mr-1 text-gray-400"></i> Send Email
        </button>
        <button class="btn-primary shadow-md">
            <i class="fas fa-plus mr-1"></i> New Order
        </button>
    </div>
</div>

<!-- Key Metrics -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card p-4 flex items-center gap-4 border-l-4 border-l-green-500">
        <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-lg"><i class="fas fa-chart-line"></i></div>
        <div>
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Lifetime Value</p>
            <h4 class="text-xl font-bold text-gray-900">$145,200</h4>
        </div>
    </div>
    <div class="card p-4 flex items-center gap-4 border-l-4 border-l-blue-500">
        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-lg"><i class="fas fa-file-invoice"></i></div>
        <div>
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Total Orders</p>
            <h4 class="text-xl font-bold text-gray-900">14</h4>
        </div>
    </div>
    <div class="card p-4 flex items-center gap-4 border-l-4 border-l-red-500">
        <div class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-lg"><i class="fas fa-exclamation-circle"></i></div>
        <div>
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Outstanding AR</p>
            <h4 class="text-xl font-bold text-red-600">$12,500</h4>
        </div>
    </div>
    <div class="card p-4 flex items-center gap-4 border-l-4 border-l-yellow-500">
        <div class="w-10 h-10 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center text-lg"><i class="fas fa-star"></i></div>
        <div>
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Credit Limit</p>
            <h4 class="text-xl font-bold text-gray-900">$50,000</h4>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Info & Activity -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Contact Card -->
        <div class="card p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2 uppercase tracking-wider">Contact Details</h3>
            <div class="space-y-3 text-sm">
                <div class="flex items-center text-gray-600"><i class="fas fa-user text-gray-400 w-5 text-center mr-2"></i> Sarah Jenkins (Purchasing)</div>
                <div class="flex items-center text-gray-600"><i class="fas fa-envelope text-gray-400 w-5 text-center mr-2"></i> sarah@alphatech.com</div>
                <div class="flex items-center text-gray-600"><i class="fas fa-phone text-gray-400 w-5 text-center mr-2"></i> +966 50 123 4567</div>
                <div class="flex items-start text-gray-600"><i class="fas fa-map-marker-alt text-gray-400 w-5 text-center mr-2 mt-1"></i> <span>456 Innovation Park,<br>Jeddah 54321, KSA</span></div>
                <div class="flex items-center text-gray-600"><i class="fas fa-building text-gray-400 w-5 text-center mr-2"></i> VAT: 300987654321003</div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <span class="text-xs font-medium text-gray-500 block mb-1">Assigned Sales Rep:</span>
                <div class="flex items-center gap-2">
                    <img src="https://ui-avatars.com/api/?name=Omar+Khalid&background=005eb8&color=fff" class="w-6 h-6 rounded-full">
                    <span class="text-sm font-bold text-gray-800">Omar Khalid</span>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="card p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2 uppercase tracking-wider">Recent Activity</h3>
            <div class="relative border-l-2 border-gray-100 ml-3 space-y-6 pb-2">
                <div class="relative pl-6">
                    <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-green-500 border-2 border-white"></div>
                    <p class="text-sm font-bold text-gray-800">Payment Received</p>
                    <p class="text-xs text-gray-500">Receipt #RC-105 for $28,175</p>
                    <span class="text-[10px] text-gray-400 mt-1 block">Aug 15, 2026</span>
                </div>
                <div class="relative pl-6">
                    <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-blue-500 border-2 border-white"></div>
                    <p class="text-sm font-bold text-gray-800">Invoice Issued</p>
                    <p class="text-xs text-gray-500">Invoice #INV-089 generated.</p>
                    <span class="text-[10px] text-gray-400 mt-1 block">Aug 14, 2026</span>
                </div>
            </div>
            <button class="w-full mt-2 text-xs font-bold text-nour-primary hover:underline text-center block">View Full History</button>
        </div>
    </div>

    <!-- Right Column: Tabs (Orders, Invoices) -->
    <div class="lg:col-span-2">
        <div class="card p-0 overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50 px-4 pt-4">
                <nav class="-mb-px flex space-x-6">
                    <a href="#" class="border-nour-primary text-nour-primary whitespace-nowrap py-3 px-1 border-b-2 font-bold text-sm">Recent Invoices</a>
                    <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors">Sales Orders</a>
                    <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors">Quotations</a>
                </nav>
            </div>
            <div class="p-0 table-responsive">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                            <th class="p-4 font-semibold">Invoice No.</th>
                            <th class="p-4 font-semibold">Issue Date</th>
                            <th class="p-4 font-semibold">Due Date</th>
                            <th class="p-4 font-semibold text-right">Amount</th>
                            <th class="p-4 font-semibold text-center">Status</th>
                            <th class="p-4 font-semibold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 font-bold text-nour-primary">INV-2026-089</td>
                            <td class="p-4 text-gray-500">Aug 14, 2026</td>
                            <td class="p-4 text-gray-500">Sep 14, 2026</td>
                            <td class="p-4 text-right font-bold text-gray-900">$28,175.00</td>
                            <td class="p-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 text-[10px] rounded uppercase font-bold">Paid</span></td>
                            <td class="p-4 text-right"><button class="text-gray-400 hover:text-nour-primary"><i class="fas fa-file-pdf"></i></button></td>
                        </tr>
                        <tr class="hover:bg-red-50/20 bg-red-50/10">
                            <td class="p-4 font-bold text-nour-primary">INV-2026-042</td>
                            <td class="p-4 text-gray-500">Jul 01, 2026</td>
                            <td class="p-4 text-red-500 font-medium">Jul 31, 2026</td>
                            <td class="p-4 text-right font-bold text-gray-900">$12,500.00</td>
                            <td class="p-4 text-center"><span class="px-2 py-0.5 bg-red-100 text-red-800 text-[10px] rounded uppercase font-bold">Overdue</span></td>
                            <td class="p-4 text-right"><button class="text-gray-400 hover:text-nour-primary"><i class="fas fa-file-pdf"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="p-3 bg-gray-50 border-t border-gray-100 text-center">
                <a href="#" class="text-sm font-medium text-nour-primary hover:underline">View All Documents</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>